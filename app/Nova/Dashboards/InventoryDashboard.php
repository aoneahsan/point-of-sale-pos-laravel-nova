<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;

class InventoryDashboard extends Dashboard
{
    /**
     * Get the displayable name of the dashboard.
     */
    public function name(): string
    {
        return 'Inventory Dashboard';
    }

    /**
     * Get the cards for the dashboard.
     */
    public function cards(): array
    {
        return [
            // Add inventory-specific metrics here
        ];
    }

    /**
     * Get the URI key for the dashboard.
     */
    public function uriKey(): string
    {
        return 'inventory-dashboard';
    }
}
