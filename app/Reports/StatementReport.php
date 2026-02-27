<?php

namespace App\Reports;

use App\Models\Client;
use Illuminate\Support\Facades\Route;
use Lightworx\FilamentReports\Reports\BaseReport;

class StatementReport extends BaseReport
{
    protected $client, $date;

    public function __construct()
    {
        parent::__construct();
        $this->config['default_font']['family'] = 'Arial';
        $this->config['default_font']['size'] = 12;
        $this->config['page']['margins']['left'] = 15;
        $this->config['page']['margins']['right'] = 10;
        $this->config['page']['margins']['top'] = 20;
        $this->config['footer']['enabled'] = false;
    }

    public static function routes(): void
    {
        Route::get('/admin/reports/statements/{id}/{date?}', function ($id, $date = null) {
            $client=Client::with('projects.invoices')->where('id',$id)->first();
            return (new static())->setStatement($client, $date)->handle();
        })->name('reports.statement');
    }

    public function setStatement($client, $date): static
    {
        $this->client = $client;
        $this->date = $date;
        return $this;
    }

    public function Header(): void
    {
        if (!$this->config['header']['enabled']) {
            return;
        }
        $this->SetY($this->config['page']['margins']['top'] - 5);
        $logo=url('/') . "/lightworx/images/lightworx.png";
        $this->SetFont('Arial', 'B', 12);
        $this->Image($logo,168,8,45);
        $this->text(15, 10, "Statement");
        $this->SetTextColor(100,100,100);
        $this->text(15, 25, "Lightworx");
        $this->SetFont('Arial', '', 10);
        $this->text(15, 30, "Open source solutions");
        $this->text(15, 34, "www.lightworx.co.za");
        $this->text(15, 38, setting('email_address'));
        $this->SetTextColor(0,0,0);

        // Client
        $this->SetFont('Arial', 'B', 11);
        $this->text(15,70,$this->client->client);
        $this->SetFont('Arial', '', 11);
        $this->text(15,75,"Attention: " . $this->client->contact_firstname . " " . $this->client->contact_surname);
        $this->text(15,80,$this->client->contact_email);
        $this->SetFillColor(0,0,0);
        $this->Rect(15,90,186,15,'F');

        // Banking
        $this->SetFont('Arial', 'B', 11);
        $this->text(17,269,"Bank details");
        $this->SetFont('Arial', '', 10);
        $banklines=explode(',',setting('bank_details'));
        foreach ($banklines as $i=>$bank){
            $this->text(17,274+$i*4,$bank);
        }
        $this->SetDrawColor(0,0,0);
        $this->SetXY(15,262);
        $this->Ln(5);

        $this->text(145,70,"Statement:");
        $this->setxy(150,68.8);
        $this->cell(52,0,$this->client->id,0,0,'R');
        $this->text(145,75,"Date:");
        $this->setxy(150,73.8);
        $this->cell(52,0,date('d M Y'),0,0,'R');
        $this->text(145,80,"Reference:");
        $this->setxy(150,78.8);
        $this->cell(52,0,"Stmt " . $this->client->id,0,0,'R');
        $yy=117;
        $total=0;
        $yy = $yy + 2;

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
            $closingBalance = $client->balance;
            $title="Statement - " . $client->client . " - " . date("j M Y");
            $pdf = $this->letterhead($title,"Statement",$client->client,$client->contact_firstname . " " . $client->contact_surname, $client->contact_email,$pdf);

            $pdf->SetTextColor(255,255,255);    
            $pdf->SetFont('DejaVu', '', 9);
            $pdf->text(112,95,"Date");
            $pdf->text(167,95,"Total");
            $pdf->SetFont('DejaVu', 'B', 11);
            $pdf->text(17,101,"Lightworx statement");
            $pdf->text(112,101,date('d M Y'));
            $pdf->text(167,101,"R " . number_format($closingBalance,2));
            $pdf->SetTextColor(0,0,0);
            $yy=120;
            $pdf->SetFont('DejaVu', 'B', 10);
            $pdf->text(15,$yy,"Date");
            $pdf->text(35,$yy,"Description");
            $pdf->setxy(140,$yy-1.2);
            $pdf->cell(17,0,"Debit",0,0,'R');
            $pdf->setxy(160,$yy-1.2);
            $pdf->cell(17,0,"Credit",0,0,'R');
            $pdf->setxy(183,$yy-1.2);
            $pdf->cell(17,0,"Balance",0,0,'R');
            $yy=$yy+5;
            $pdf->SetFont('DejaVu', '', 10);
            $pdf->text(15,$yy,date('d M',strtotime($monthStart)));
            $pdf->text(35,$yy,"Opening balance");
            $pdf->setxy(183,$yy-1.2);
            $pdf->cell(17,0,number_format($openingBalance,2),0,0,'R');
            $yy=$yy+2;
            $runningtotal=$openingBalance;
            foreach ($details as $detail){
                $runningtotal=$runningtotal + $detail['amount'];
                $yy=$yy+5;
                $pdf->text(15,$yy,date('d M',strtotime($detail['date'])));
                $pdf->text(35,$yy,$detail['details']);
                if ($detail['amount']<0){
                    $pdf->setxy(160,$yy-1.2);
                    $pdf->cell(17,0,number_format(-$detail['amount'],2),0,0,'R');
                } else {
                    $pdf->setxy(140,$yy-1.2);
                    $pdf->cell(17,0,number_format($detail['amount'],2),0,0,'R');
                }
                $pdf->setxy(183,$yy-1.2);
                $pdf->cell(17,0,number_format($runningtotal,2),0,0,'R');
            }
            $yy = $yy + 12;
            $pdf->SetFont('DejaVu', 'B', 10);
            if ($runningtotal>0){
                $pdf->text(15,$yy,"Balance due:");
            } else {
                $pdf->text(15,$yy,"Balance due to you:");
            }

            $pdf->setxy(183,$yy-1.2);
            $pdf->cell(17,0,"R " . number_format($runningtotal,2),0,0,'R');
        }
        $pdf->Output('I',$title);





        $this->SetFont('Arial', 'B', 10);
        $this->text(15,$yy,"Total");
        $this->setxy(184,$yy-1.2);
        $this->cell(17,0,"R " . number_format($total,2),0,0,'R');

        $this->SetTextColor(255,255,255);
        $this->SetFont('Arial', '', 9);
        $this->text(17,95,"Invoice");
        $this->text(112,95,"Date");
        $this->text(167,95,"Total");
        $this->SetFont('Arial', 'B', 11);
        $this->text(17,101,$this->client->id);
        $this->text(112,101,date('d M Y'));
        $this->text(167,101,"R " . number_format($total,2));
    }

    public function generate(): void
    {
        $title="Lightworx Statement " . $this->client->client . " - " . date("j M Y");
        $this->setReportTitle($title);
        $this->AddPage('P', 'A4');
    }

    protected function getFilename(): string
    {
        return 'lightworx-statement-' . $this->client->id . '-' . now()->format('Y-m-d') . '.pdf';
    }


}