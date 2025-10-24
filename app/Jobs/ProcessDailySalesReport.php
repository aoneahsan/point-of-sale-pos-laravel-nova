<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Sale;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ProcessDailySalesReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $store;
    protected $date;

    public function __construct(Store $store, Carbon $date)
    {
        $this->store = $store;
        $this->date = $date;
    }

    public function handle(): void
    {
        $sales = Sale::where('store_id', $this->store->id)
            ->whereDate('created_at', $this->date)
            ->where('status', Sale::STATUS_COMPLETED)
            ->get();

        $report = [
            'store' => $this->store->name,
            'date' => $this->date->format('Y-m-d'),
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total'),
            'total_tax' => $sales->sum('tax'),
            'total_discount' => $sales->sum('discount'),
            'average_sale' => $sales->avg('total'),
        ];

        // Log or send email with report
        \Log::info('Daily Sales Report', $report);
    }
}