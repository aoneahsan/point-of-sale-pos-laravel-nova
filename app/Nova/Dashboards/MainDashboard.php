<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;
use App\Nova\Metrics\TotalSales;
use App\Nova\Metrics\NewCustomers;
use App\Nova\Metrics\AverageSale;

class MainDashboard extends Dashboard
{
    /**
     * Get the displayable name of the dashboard.
     */
    public function name(): string
    {
        return 'Main Dashboard';
    }

    /**
     * Get the cards for the dashboard.
     */
    public function cards(): array
    {
        return [
            new TotalSales,
            new NewCustomers,
            new AverageSale,
        ];
    }

    /**
     * Get the URI key for the dashboard.
     */
    public function uriKey(): string
    {
        return 'main';
    }
}
