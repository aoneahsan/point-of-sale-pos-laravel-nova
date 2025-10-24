<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;
use App\Nova\Metrics\TotalSales;

class ReportsDashboard extends Dashboard
{
    /**
     * Get the displayable name of the dashboard.
     */
    public function name(): string
    {
        return 'Reports Dashboard';
    }

    /**
     * Get the cards for the dashboard.
     */
    public function cards(): array
    {
        return [
            new TotalSales,
        ];
    }

    /**
     * Get the URI key for the dashboard.
     */
    public function uriKey(): string
    {
        return 'reports-dashboard';
    }
}
