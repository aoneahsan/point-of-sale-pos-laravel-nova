<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $sale->reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .info { margin-bottom: 20px; }
        .info-row { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total-line { font-size: 16px; font-weight: bold; border-top: 2px solid #000; padding-top: 10px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $sale->store->name }}</h1>
        <p>{{ $sale->store->address }}</p>
        <p>Phone: {{ $sale->store->phone }} | Email: {{ $sale->store->email }}</p>
    </div>

    <div class="info">
        <div class="info-row"><strong>Invoice #:</strong> {{ $sale->reference }}</div>
        <div class="info-row"><strong>Date:</strong> {{ $sale->created_at->format('F d, Y H:i') }}</div>
        @if($sale->customer)
            <div class="info-row"><strong>Customer:</strong> {{ $sale->customer->name }}</div>
            @if($sale->customer->email)
                <div class="info-row"><strong>Email:</strong> {{ $sale->customer->email }}</div>
            @endif
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->discount, 2) }}</td>
                    <td class="text-right">${{ number_format($item->tax, 2) }}</td>
                    <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span>Subtotal:</span>
            <span>${{ number_format($sale->subtotal, 2) }}</span>
        </div>
        <div class="totals-row">
            <span>Tax:</span>
            <span>${{ number_format($sale->tax, 2) }}</span>
        </div>
        @if($sale->discount > 0)
            <div class="totals-row">
                <span>Discount:</span>
                <span>-${{ number_format($sale->discount, 2) }}</span>
            </div>
        @endif
        <div class="totals-row total-line">
            <span>Total:</span>
            <span>${{ number_format($sale->total, 2) }}</span>
        </div>
    </div>

    @if($sale->payments->isNotEmpty())
        <div style="margin-top: 30px;">
            <h3>Payments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Method</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->payments as $payment)
                        <tr>
                            <td>{{ $payment->paymentMethod->name }}</td>
                            <td class="text-right">${{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        @if($sale->store->tax_number)
            <p>Tax ID: {{ $sale->store->tax_number }}</p>
        @endif
    </div>
</body>
</html>
