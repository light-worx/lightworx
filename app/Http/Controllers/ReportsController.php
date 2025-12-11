<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Str;
use App\Classes\tFPDF;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use stdClass;

class ReportsController extends Controller
{
    public function quote($id){
        $this->invoice($id,"Quotation");
    }

    public function letterhead($title, $type, $clientName, $clientContact,$clientEmail){
        $pdf = new tFPDF();
        $pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
        $pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.ttf', true);
        $pdf->AddFont('DejaVu', 'I', 'DejaVuSans-Oblique.ttf', true);
        $pdf->AddFont('DejaVuCond','','DejaVuSansCondensed.ttf',true);
        $pdf->AddFont('DejaVuCond', 'B', 'DejaVuSansCondensed-Bold.ttf', true);
        $logo=url('/') . "/lightworx/images/lightworx.png";
        $pdf->AddPage('P');
        $pdf->SetTitle($title);
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetFont('DejaVu', 'B', 12);
        $pdf->Image($logo,168,8,45);
        $pdf->text(15, 10, $type);
        $pdf->SetTextColor(100,100,100);
        $pdf->text(15, 25, "Lightworx");
        $pdf->SetFont('DejaVu', '', 10);
        $pdf->text(15, 30, "Open source solutions");
        $pdf->text(15, 34, "www.lightworx.co.za");
        $pdf->text(15, 38, setting('email_address'));
        $pdf->SetTextColor(0,0,0);

        // Client
        $pdf->SetFont('DejaVu', 'B', 10);
        $pdf->text(15,70,$clientName);
        $pdf->SetFont('DejaVu', '', 10);
        $pdf->text(15,75,"Attention: " . $clientContact);
        $pdf->text(15,80,$clientEmail);

        // Banking
        $pdf->SetFont('DejaVu', 'B', 11);
        $pdf->text(17,269,"Bank details");
        $pdf->SetFont('DejaVu', '', 10);
        $banklines=explode(',',setting('bank_details'));
        foreach ($banklines as $i=>$bank){
            $pdf->text(17,274+$i*4,$bank);
        }
        return $pdf;
    }

    public function invoice ($id,$type="Invoice",$email=false){
        if ($type=="Invoice"){
            $inv=Invoice::with('invoiceitems','project.client')->where('id',$id)->first();
        } else {
            // $inv=Quote::with('invoiceitems','project.client')->where('id',$id)->first();
        }
        $title="Light worx " . $type . " " . $inv->id . " - " . date("j M Y");
        $pdf=$this->letterhead($title,$type,$inv->project->client->client,$inv->project->client->contact_firstname . " " . $inv->project->client->contact_surname, $inv->project->client->contact_email);

        $pdf->text(145,70,$type . " No:");
        $pdf->setxy(150,68.8);
        $pdf->cell(52,0,$inv->id,0,0,'R');
        $pdf->text(145,75,"Date:");
        $pdf->setxy(150,73.8);
        $pdf->cell(52,0,date('d M Y'),0,0,'R');
        $pdf->text(145,80,"Reference:");
        $pdf->setxy(150,78.8);
        if ($type=="Invoice"){
            $pdf->cell(52,0,"Inv " . $inv->id,0,0,'R');
        } else {
            $pdf->cell(52,0,"Quote " . $inv->id,0,0,'R');
        }
        $yy=117;
        $total=0;
        if (count($inv->invoiceitems)){
            $pdf->SetFont('DejaVu', 'B', 10);
            $pdf->text(15,$yy,"Date");
            $pdf->text(35,$yy,"Description");
            $pdf->text(140,$yy,"Hours");
            $pdf->text(160,$yy,"Rate (R)");
            $pdf->text(185,$yy,"Amount");
            $pdf->SetFont('DejaVu', '', 10);
            $yy=$yy+6;
            foreach ($inv->invoiceitems as $item){
                $pdf->text(15,$yy,date('d M',strtotime($item->itemdate    )));
                $pdf->text(35,$yy,$item->details);
                $pdf->setxy(140,$yy-1.2);
                $pdf->cell(12,0,$item->quantity,0,0,'C');
                $pdf->setxy(160,$yy-1.2);
                $pdf->cell(16,0,$item->unit_price,0,0,'C');
                $pdf->setxy(185,$yy-1.2);
                $pdf->cell(17,0,number_format($item->quantity * $item->unit_price,2),0,0,'R');
                $total=$total + ($item->quantity *$item->unit_price);
                $yy=$yy+5;
            }
        }
        $yy = $yy + 2;
        $pdf->SetFont('DejaVu', 'B', 10);
        $pdf->text(15,$yy,"Total");
        $pdf->setxy(185,$yy-1.2);
        $pdf->cell(17,0,"R " . number_format($total,2),0,0,'R');

        $pdf->RoundedRect(15,90,186,15,1,'1234','F');
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('DejaVu', '', 9);
        $pdf->text(17,95,$type);
        $pdf->text(37,95,"Project");
        $pdf->text(112,95,"Date");
        $pdf->text(167,95,"Total");
        $pdf->SetFont('DejaVu', 'B', 11);
        $pdf->text(17,101,$inv->id);
        $pdf->text(37,101,$inv->project->project);
        $pdf->text(112,101,date('d M Y'));
        $pdf->text(167,101,"R " . number_format($total,2));
        if ($email){
            return $pdf->Output('S',$title);
        } else {    
            $pdf->Output('I',$title);
            exit;
        }
    }

    public function statement(){
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $clientsOwing = Client::get()->filter(function ($client) {
            return $client->balance > 0;
        });
        $clients = Client::query()
            ->where(function ($query) use ($monthStart, $monthEnd) {
                $query->whereHas('invoices', function ($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('invoicedate', [$monthStart, $monthEnd]);
                })
                ->orWhereHas('payments', function ($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('paymentdate', [$monthStart, $monthEnd]);
                });
            })
            ->get()
            ->merge($clientsOwing)
            ->unique('id')
            ->values();
        foreach ($clients as $client){
            $openingBalance = $client->invoices()
                ->where('invoicedate', '<', $monthStart)
                ->get()
                ->sum->total
                -
                $client->payments()
                ->where('paymentdate', '<', $monthStart)
                ->sum('amount');
            $details=array();
            $invoices = $client->invoices()
                ->whereBetween('invoicedate', [$monthStart, $monthEnd])
                ->with('invoiceitems')
                ->get();
            foreach ($invoices as $invoice){
                $details[strtotime($invoice->invoicedate)] = [
                    'details' => 'Invoice ' . $invoice->id,
                    'date' => $invoice->invoicedate,
                    'amount' => $invoice->total
                ];
            }
            $payments = $client->payments()->whereBetween('paymentdate', [$monthStart, $monthEnd])->get();
            foreach ($payments as $payment){
                $details[strtotime($payment->paymentdate) + 1] = [
                    'details' => 'Payment - thank you',
                    'date' => $payment->paymentdate,
                    'amount' => -$payment->amount
                ];
            }
            $title="Statement - " . $client->client . " - " . date("j M Y");
            $pdf = $this->letterhead($title,"Statement",$client->client,$client->contact_firstname . " " . $client->contact_surname, $client->contact_email);
            $pdf->Output('I',$title);
        }
        $closingBalance = $client->balance;
    }

    public function statementPdf($clientId){
        $client = Client::findOrFail($clientId);
    }
}