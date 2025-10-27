@extends('emails.layout')

@section('title', 'Receipt - Sale #' . $sale->reference)

@section('content')
    <h2>Thank You for Your Purchase!</h2>

    <p>Hi {{ $customer->name ?? 'Valued Customer' }},</p>

    <p>
        Thank you for your purchase! Here's your receipt for sale <strong>#{{ $sale->reference }}</strong>.
    </p>

    <div class="success-box">
        <p><strong>✓ Payment Received</strong></p>
        <p>Your payment has been successfully processed.</p>
    </div>

    <div class="divider"></div>

    <!-- Sale Details -->
    <h3 style="color: #1e40af; font-size: 18px; margin-bottom: 16px;">Order Details</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>Item</th>
                <th style="text-align: right;">Qty</th>
                <th style="text-align: right;">Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td style="text-align: right;">{{ $item->quantity }}</td>
                <td style="text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: right;">${{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <table style="width: 100%; margin-top: 24px;">
        <tr>
            <td style="text-align: right; padding: 8px 0; color: #64748b;">Subtotal:</td>
            <td style="text-align: right; padding: 8px 0; font-weight: 600;">${{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        @if($sale->discount > 0)
        <tr>
            <td style="text-align: right; padding: 8px 0; color: #64748b;">Discount:</td>
            <td style="text-align: right; padding: 8px 0; font-weight: 600; color: #059669;">-${{ number_format($sale->discount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td style="text-align: right; padding: 8px 0; color: #64748b;">Tax:</td>
            <td style="text-align: right; padding: 8px 0; font-weight: 600;">${{ number_format($sale->tax, 2) }}</td>
        </tr>
        <tr style="border-top: 2px solid #e2e8f0;">
            <td style="text-align: right; padding: 12px 0 0 0; font-weight: 700; font-size: 18px; color: #1e40af;">Total:</td>
            <td style="text-align: right; padding: 12px 0 0 0; font-weight: 700; font-size: 18px; color: #1e40af;">${{ number_format($sale->total, 2) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Additional Info -->
    <div class="info-box">
        <p><strong>Sale Information</strong></p>
        <p style="margin-top: 8px;">
            <strong>Date:</strong> {{ $sale->created_at->format('F j, Y \a\t g:i A') }}<br>
            <strong>Reference:</strong> {{ $sale->reference }}<br>
            <strong>Store:</strong> {{ $sale->store->name }}<br>
            <strong>Cashier:</strong> {{ $sale->user->name }}
        </p>
    </div>

    <p>
        If you have any questions about this purchase, please contact us.
    </p>

    <center>
        <a href="{{ url('/admin/resources/sales/' . $sale->id) }}" class="email-button">
            View Order Details
        </a>
    </center>
@endsection

@section('footer')
    <p style="margin-top: 16px;">
        Questions? Contact us at support@possystem.com
    </p>
@endsection
