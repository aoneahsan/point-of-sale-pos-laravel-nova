<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function handle(): void
    {
        $sale = $this->sale->load(['store', 'customer', 'items', 'payments']);
        
        $pdf = Pdf::loadView('pdf.invoice', compact('sale'));
        
        // Save or email invoice
        $filename = "invoice-{$sale->reference}.pdf";
        $pdf->save(storage_path("app/invoices/{$filename}"));
    }
}