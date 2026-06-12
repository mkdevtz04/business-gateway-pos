<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1E293B;
            background: #ffffff;
            padding: 30px;
        }

        /* ── Header ─────────────────────────────────── */
        .header {
            background: #2563EB;
            color: white;
            padding: 20px 24px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-box {
            width: 44px;
            height: 44px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-box img { width: 32px; height: 32px; object-fit: contain; }

        .brand-name { font-size: 16px; font-weight: bold; }
        .brand-sub  { font-size: 10px; opacity: 0.8; margin-top: 2px; }

        .invoice-label { text-align: right; }
        .invoice-label .inv-title { font-size: 22px; font-weight: bold; letter-spacing: 2px; }
        .invoice-label .inv-num   { font-size: 13px; margin-top: 4px; opacity: 0.9; }
        .invoice-label .inv-date  { font-size: 11px; opacity: 0.75; margin-top: 2px; }
        .invoice-label .status    {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.4);
            padding: 2px 10px;
            border-radius: 99px;
            font-size: 10px;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── Info cards ─────────────────────────────── */
        .info-row {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-card {
            flex: 1;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-left: 3px solid #2563EB;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .info-card-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748B;
            margin-bottom: 6px;
        }

        .info-card p { font-size: 11px; color: #374151; margin: 2px 0; }
        .info-card .strong { font-weight: bold; color: #111827; font-size: 12px; }

        /* ── Items table ────────────────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead th {
            background: #1E293B;
            color: white;
            padding: 9px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        .items-table thead th:first-child { border-radius: 6px 0 0 6px; }
        .items-table thead th:last-child  { border-radius: 0 6px 6px 0; }

        .items-table tbody tr { border-bottom: 1px solid #F1F5F9; }
        .items-table tbody tr:last-child { border-bottom: none; }
        .items-table tbody tr:nth-child(even) { background: #FAFBFC; }

        .items-table tbody td {
            padding: 9px 10px;
            font-size: 11px;
            color: #374151;
            vertical-align: middle;
        }

        .qty-badge {
            background: #DBEAFE;
            color: #1D4ED8;
            padding: 2px 7px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: bold;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .font-bold   { font-weight: bold; }

        /* ── Summary ────────────────────────────────── */
        .summary-row-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .summary-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 14px 18px;
            min-width: 220px;
        }

        .sum-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 0;
            font-size: 11px;
            color: #6B7280;
        }

        .sum-row .val { font-weight: 500; color: #374151; }

        .sum-divider { border-top: 1.5px solid #2563EB; margin: 8px 0; }

        .sum-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }

        /* ── Footer ─────────────────────────────────── */
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }

        .footer p { font-size: 10px; color: #94A3B8; margin: 2px 0; }
        .footer .thank-you { font-size: 12px; font-weight: bold; color: #374151; margin-bottom: 4px; }
    </style>
</head>
<body>
    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-inner">
            <div class="brand">
                <div class="logo-box">
                    <img src="{{ public_path('storage/logos/mkdev.png') }}" alt="Logo">
                </div>
                <div>
                    <div class="brand-name">Business Gateway POS</div>
                    <div class="brand-sub">Professional Point of Sale System</div>
                </div>
            </div>
            <div class="invoice-label">
                <div class="inv-title">INVOICE</div>
                <div class="inv-num">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="inv-date">{{ $order->created_at->format('F j, Y \a\t g:i A') }}</div>
                <div class="status">Paid</div>
            </div>
        </div>
    </div>

    {{-- ── INFO CARDS ── --}}
    <div class="info-row">
        <div class="info-card">
            <div class="info-card-label">Bill To</div>
            @if($order->customer)
                <p class="strong">{{ $order->customer->name }}</p>
                @if($order->customer->contact)
                    <p>{{ $order->customer->contact }}</p>
                @endif
            @else
                <p class="strong">Walk-in Customer</p>
            @endif
        </div>
        <div class="info-card">
            <div class="info-card-label">Processed By</div>
            <p class="strong">{{ $order->user?->name ?? '—' }}</p>
            <p>{{ $order->user?->email ?? '' }}</p>
        </div>
        <div class="info-card">
            <div class="info-card-label">Payment Method</div>
            <p class="strong">{{ ucfirst($order->payment_method) }}</p>
            <p>Status: Completed</p>
        </div>
    </div>

    {{-- ── ITEMS TABLE ── --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:42%">Product</th>
                <th style="width:12%" class="text-center">Qty</th>
                <th style="width:15%" class="text-right">Unit Price</th>
                <th style="width:14%" class="text-right">Tax</th>
                <th style="width:17%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="font-bold">{{ $item->product?->name ?? 'Deleted Product' }}</td>
                <td class="text-center">
                    <span class="qty-badge">{{ $item->quantity }}</span>
                </td>
                <td class="text-right">${{ number_format($item->price, 2) }}</td>
                <td class="text-right">${{ number_format($item->tax, 2) }}</td>
                <td class="text-right font-bold">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── SUMMARY ── --}}
    @php
        $taxTotal      = $order->items->sum('tax');
        $subtotalBase  = $order->total_amount - $taxTotal;
    @endphp

    <div class="summary-row-wrap">
        <div class="summary-box">
            <div class="sum-row">
                <span>Subtotal</span>
                <span class="val">${{ number_format($subtotalBase, 2) }}</span>
            </div>
            <div class="sum-row">
                <span>Tax</span>
                <span class="val">${{ number_format($taxTotal, 2) }}</span>
            </div>
            <div class="sum-divider"></div>
            <div class="sum-total">
                <span>Total</span>
                <span>${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <p class="thank-you">Thank you for your business!</p>
        <p>Business Gateway POS &mdash; {{ now()->format('Y') }}</p>
    </div>
</body>
</html>
