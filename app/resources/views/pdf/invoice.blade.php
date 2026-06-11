<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
<style>
    body { font-family: ui-sans-serif, system-ui, sans-serif; color: #0f172a; margin: 2.5rem; }
    h1 { color: #38BDF8; }
    table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
    th, td { text-align: left; padding: .5rem; border-bottom: 1px solid #e2e8f0; font-size: .9rem; }
    td:last-child, th:last-child { text-align: right; }
    .total td { font-weight: 700; border-top: 2px solid #0f172a; }
    .meta { color: #64748b; font-size: .85rem; }
</style>
</head>
<body>
    <h1>{{ $companyName }}</h1>
    <h2>Invoice {{ $invoice->invoice_number }}</h2>
    <p class="meta">
        To: {{ $invoice->customer->name }} ({{ $invoice->customer->email }})<br>
        Issue date: {{ $invoice->issue_date->format('d-m-Y') }} · Due: {{ $invoice->due_date->format('d-m-Y') }}
    </p>
    <table>
        <tr><th>Description</th><th>Qty</th><th>Unit</th><th>Total</th></tr>
        @foreach ($invoice->lines as $line)
            <tr>
                <td>{{ $line->description }}</td>
                <td>{{ $line->quantity }}</td>
                <td>€{{ number_format($line->unit_price_cents / 100, 2) }}</td>
                <td>€{{ number_format($line->line_total_cents / 100, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total"><td colspan="3">Total ({{ $invoice->currency }})</td><td>€{{ number_format($invoice->total_cents / 100, 2) }}</td></tr>
    </table>
</body>
</html>
