{{-- Dompdf template: tables + inline CSS only, DejaVu Sans for the € glyph. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111827; padding: 48px 52px; }
        .head { width: 100%; margin-bottom: 36px; }
        .brand { font-size: 20px; font-weight: bold; letter-spacing: -0.5px; }
        .doc-title { text-align: right; font-size: 20px; font-weight: bold; color: #6B7280; letter-spacing: 2px; }
        .doc-number { text-align: right; font-size: 11px; color: #6B7280; padding-top: 4px; }
        .paid-stamp { display: inline-block; margin-top: 6px; padding: 3px 10px; border: 2px solid #16A34A; border-radius: 6px; color: #16A34A; font-weight: bold; font-size: 11px; letter-spacing: 1px; }
        .meta { width: 100%; margin-bottom: 28px; }
        .label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9CA3AF; padding-bottom: 4px; }
        .value { font-size: 12px; font-weight: bold; }
        .sub { font-size: 10px; color: #6B7280; padding-top: 2px; }
        .lines { width: 100%; border-collapse: collapse; }
        .lines th { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; text-align: left; padding: 8px 10px; border-bottom: 2px solid #111827; }
        .lines td { padding: 9px 10px; border-bottom: 1px solid #E5E7EB; }
        .lines tr.zebra td { background: #F9FAFB; }
        .num { text-align: right; }
        .totals { width: 100%; margin-top: 10px; }
        .totals td { padding: 4px 10px; }
        .t-label { text-align: right; font-size: 11px; color: #6B7280; }
        .t-value { text-align: right; width: 130px; font-size: 11px; }
        .t-final .t-label, .t-final .t-value { font-size: 14px; font-weight: bold; color: #111827; padding-top: 8px; }
        .notes { margin-top: 26px; font-size: 10px; color: #4B5563; }
        .footer { margin-top: 48px; padding-top: 14px; border-top: 1px solid #E5E7EB; font-size: 9px; color: #9CA3AF; line-height: 1.6; }
    </style>
</head>
<body>
    <table class="head">
        <tr>
            <td class="brand">{{ $companyName }}</td>
            <td>
                <div class="doc-title">INVOICE</div>
                <div class="doc-number">{{ $number }}</div>
                @if ($isPaid)
                    <div style="text-align: right;"><span class="paid-stamp">PAID</span></div>
                @endif
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td width="34%">
                <div class="label">Billed to</div>
                <div class="value">{{ $customerName }}</div>
                @if ($customerVat)
                    <div class="sub">VAT {{ $customerVat }}</div>
                @endif
            </td>
            <td width="33%">
                <div class="label">Issued</div>
                <div class="value">{{ $issueDate }}</div>
            </td>
            <td width="33%">
                <div class="label">Due</div>
                <div class="value">{{ $dueDate }}</div>
                <div class="sub">Status: {{ $statusLabel }}</div>
            </td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th>Description</th>
                <th class="num">Qty</th>
                <th class="num">Unit price</th>
                <th class="num">VAT</th>
                <th class="num">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lines as $index => $line)
                <tr @if ($index % 2 === 1) class="zebra" @endif>
                    <td>{{ $line['description'] }}</td>
                    <td class="num">{{ $line['quantity'] }}</td>
                    <td class="num">{{ $line['unit'] }}</td>
                    <td class="num">{{ $line['tax_rate'] }}</td>
                    <td class="num">{{ $line['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="t-label">Subtotal</td><td class="t-value">{{ $subtotal }}</td></tr>
        <tr><td class="t-label">VAT</td><td class="t-value">{{ $taxTotal }}</td></tr>
        <tr class="t-final"><td class="t-label">Total due</td><td class="t-value">{{ $total }}</td></tr>
    </table>

    @if ($notes)
        <div class="notes">{{ $notes }}</div>
    @endif

    <div class="footer">
        {{ $companyName }} — powered by FlowFlex.<br>
        Questions about this invoice? Just reply to the email it arrived with.
    </div>
</body>
</html>
