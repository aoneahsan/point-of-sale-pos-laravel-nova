<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ProcessDailySalesReport;
use App\Jobs\SendLowStockAlert;
use App\Models\Store;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily sales reports
Schedule::call(function () {
    $stores = Store::active()->get();
    foreach ($stores as $store) {
        ProcessDailySalesReport::dispatch($store, now()->subDay());
    }
})->dailyAt('01:00')->name('daily-sales-reports');

// Schedule low stock alerts
Schedule::call(function () {
    $stores = Store::active()->get();
    foreach ($stores as $store) {
        SendLowStockAlert::dispatch($store);
    }
})->dailyAt('09:00')->name('low-stock-alerts');
