<?php

namespace App\Nova\Actions;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Collection;
use App\Models\Sale;

class RefundSale extends Action
{
    public $name = 'Refund Sale';

    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $sale) {
            if ($sale->status === Sale::STATUS_COMPLETED) {
                $sale->update([
                    'status' => Sale::STATUS_REFUNDED,
                    'notes' => ($sale->notes ?? '') . "\nRefund reason: " . $fields->reason,
                ]);

                // Restore stock
                foreach ($sale->items as $item) {
                    $item->variant->increment('stock', $item->quantity);
                }
            }
        }

        return Action::message('Sales refunded successfully!');
    }

    public function fields(NovaRequest $request)
    {
        return [
            Textarea::make('Reason')->required(),
        ];
    }
}