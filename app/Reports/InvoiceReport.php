<?php

namespace App\Reports;

use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Lightworx\FilamentReports\Reports\BaseReport;

class InvoiceReport extends BaseReport
{
    protected $invoice;

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
        Route::get('/admin/reports/invoices/{id}', function ($id) {
            $inv=Invoice::with('invoiceitems','project.client')->where('id',$id)->first();
            return (new static())->setInvoice($inv)->handle();
        })->name('reports.invoice');
    }

    public function setInvoice($invoice): static
    {
        $this->invoice = $invoice;
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
        $this->text(15, 10, "Invoice");
        $this->SetTextColor(100,100,100);
        $this->text(15, 25, "Lightworx");
        $this->SetFont('Arial', '', 10);
        $this->text(15, 30, "Open source solutions");
        $this->text(15, 34, "www.lightworx.co.za");
        $this->text(15, 38, setting('email_address'));
        $this->SetTextColor(0,0,0);

        // Client
        $this->SetFont('Arial', 'B', 11);
        $this->text(15,70,$this->invoice->project->client->client);
        $this->SetFont('Arial', '', 11);
        $this->text(15,75,"Attention: " . $this->invoice->project->client->contact_firstname . " " . $this->invoice->project->client->contact_surname);
        $this->text(15,80,$this->invoice->project->client->contact_email);
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

        $this->text(145,70,"Invoice No:");
        $this->setxy(150,68.8);
        $this->cell(52,0,$this->invoice->id,0,0,'R');
        $this->text(145,75,"Date:");
        $this->setxy(150,73.8);
        $this->cell(52,0,date('d M Y'),0,0,'R');
        $this->text(145,80,"Reference:");
        $this->setxy(150,78.8);
        $this->cell(52,0,"Inv " . $this->invoice->id,0,0,'R');
        $yy=117;
        $total=0;
        if (count($this->invoice->invoiceitems)){
            $this->SetFont('Arial', 'B', 10);
            $this->text(15,$yy,"Date");
            $this->text(35,$yy,"Description");
            $this->text(140,$yy,"Hours");
            $this->text(160,$yy,"Rate (R)");
            $this->text(187,$yy,"Amount");
            $this->SetFont('Arial', '', 10);
            $yy=$yy+6;
            foreach ($this->invoice->invoiceitems as $item){
                $this->text(15,$yy,date('d M',strtotime($item->itemdate    )));
                $this->setxy(140,$yy-1.2);
                $this->cell(12,0,$item->quantity,0,0,'C');
                $this->setxy(160,$yy-1.2);
                $this->cell(16,0,$item->unit_price,0,0,'C');
                $this->setxy(184,$yy-1.2);
                $this->cell(17,0,number_format($item->quantity * $item->unit_price,2),0,0,'R');
                $total=$total + ($item->quantity *$item->unit_price);
                $this->setXy(34,$yy-3);
                $this->multicell(102,4,$item->details);
                $yy=$this->getY()+4.5;
            }
        }
        $yy = $yy + 2;
        $this->SetFont('Arial', 'B', 10);
        $this->text(15,$yy,"Total");
        $this->setxy(184,$yy-1.2);
        $this->cell(17,0,"R " . number_format($total,2),0,0,'R');

        $this->SetTextColor(255,255,255);
        $this->SetFont('Arial', '', 9);
        $this->text(17,95,"Invoice");
        $this->text(37,95,"Project");
        $this->text(112,95,"Date");
        $this->text(167,95,"Total");
        $this->SetFont('Arial', 'B', 11);
        $this->text(17,101,$this->invoice->id);
        $this->text(37,101,$this->invoice->project->project);
        $this->text(112,101,date('d M Y'));
        $this->text(167,101,"R " . number_format($total,2));
    }

    public function generate(): void
    {
        $title="Lightworx Invoice " . $this->invoice->id . " - " . date("j M Y");
        $this->setReportTitle($title);
        $this->AddPage('P', 'A4');
    }

    protected function getFilename(): string
    {
        return 'lightworx-invoice-' . $this->invoice->id . '-' . now()->format('Y-m-d') . '.pdf';
    }
}