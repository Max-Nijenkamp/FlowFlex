{{-- Dompdf template: tables + inline CSS only, DejaVu Sans for the € glyph. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111827;
            padding: 48px 52px;
        }
        .head { width: 100%; margin-bottom: 40px; }
        .brand { font-size: 20px; font-weight: bold; letter-spacing: -0.5px; }
        .brand span { color: #4F46E5; }
        .doc-title { text-align: right; font-size: 20px; font-weight: bold; color: #6B7280; letter-spacing: 2px; }
        .doc-number { text-align: right; font-size: 11px; color: #6B7280; padding-top: 4px; }
        .paid-stamp {
            display: inline-block; margin-top: 6px; padding: 3px 10px;
            border: 2px solid #16A34A; border-radius: 6px;
            color: #16A34A; font-weight: bold; font-size: 11px; letter-spacing: 1px;
        }
        .meta { width: 100%; margin-bottom: 32px; }
        .meta td { vertical-align: top; }
        .label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9CA3AF; padding-bottom: 4px; }
        .value { font-size: 12px; font-weight: bold; }
        .sub { font-size: 10px; color: #6B7280; padding-top: 2px; }
        .lines { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .lines th {
            font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #6B7280;
            text-align: left; padding: 8px 10px; border-bottom: 2px solid #111827;
        }
        .lines td { padding: 9px 10px; border-bottom: 1px solid #E5E7EB; }
        .lines tr.zebra td { background: #F9FAFB; }
        .num { text-align: right; }
        .totals { width: 100%; margin-top: 8px; }
        .totals td { padding: 6px 10px; }
        .total-label { text-align: right; font-size: 12px; font-weight: bold; }
        .total-value { text-align: right; font-size: 15px; font-weight: bold; width: 130px; }
        .footer {
            margin-top: 56px; padding-top: 14px; border-top: 1px solid #E5E7EB;
            font-size: 9px; color: #9CA3AF; line-height: 1.6;
        }
    </style>
</head>
<body>
    <table class="head">
        <tr>
            <td class="brand">Flow<span>Flex</span></td>
            <td>
                <div class="doc-title">INVOICE</div>
                <div class="doc-number">{{ $number }}</div>
                @if ($isPaid)
                    <div style="text-align: right;"><span class="paid-stamp">PAID{{ $paidAt ? ' · '.$paidAt : '' }}</span></div>
                @endif
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td width="34%">
                <div class="label">Billed to</div>
                <div class="value">{{ $companyName }}</div>
            </td>
            <td width="33%">
                <div class="label">Billing period</div>
                <div class="value">{{ $periodLabel }}</div>
                <div class="sub">{{ $periodRange }}</div>
            </td>
            <td width="33%">
                <div class="label">Issued</div>
                <div class="value">{{ $issuedAt }}</div>
                <div class="sub">Status: {{ $statusLabel }}</div>
            </td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th>Module</th>
                <th class="num">Users</th>
                <th class="num">Price per user</th>
                <th class="num">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lines as $index => $line)
                <tr @if ($index % 2 === 1) class="zebra" @endif>
                    <td>{{ $line['name'] }}</td>
                    <td class="num">{{ $line['count'] }}</td>
                    <td class="num">{{ $line['unit'] }}</td>
                    <td class="num">{{ $line['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="total-label">Total due</td>
            <td class="total-value">{{ $total }}</td>
        </tr>
    </table>

    <div class="footer">
        FlowFlex — all-in-one workspace for growing teams.<br>
        Payment is collected automatically via the payment method on file. Questions? Reply to the invoice email.
    </div>
</body>
</html>
