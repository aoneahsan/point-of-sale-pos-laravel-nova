<?php

namespace App\Nova\Actions;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExportSales extends Action
{
    public $name = 'Export Sales';

    public function handle(ActionFields $fields, Collection $models)
    {
        $data = $models->map(function ($sale) {
            return [
                'Reference' => $sale->reference,
                'Date' => $sale->created_at->format('Y-m-d'),
                'Customer' => $sale->customer->name ?? 'Walk-in',
                'Subtotal' => $sale->subtotal,
                'Tax' => $sale->tax,
                'Discount' => $sale->discount,
                'Total' => $sale->total,
                'Status' => $sale->status,
            ];
        });

        return Action::download('sales-export.csv', $this->generateCsv($data));
    }

    protected function generateCsv($data)
    {
        $csv = fopen('php://temp', 'r+');
        
        if ($data->isNotEmpty()) {
            fputcsv($csv, array_keys($data->first()));
            $data->each(function ($row) use ($csv) {
                fputcsv($csv, $row);
            });
        }
        
        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);
        
        return $output;
    }

    public function fields(NovaRequest $request)
    {
        return [];
    }
}