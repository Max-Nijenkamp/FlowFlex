<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        body { font-family: Inter, system-ui, sans-serif; background: #f1f5f9; display: grid; place-items: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgb(0 0 0 / .08); padding: 2.5rem; width: 100%; max-width: 34rem; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { text-align: left; padding: .5rem .25rem; border-bottom: 1px solid #e2e8f0; font-size: .9rem; }
        td:last-child, th:last-child { text-align: right; }
        .total { font-weight: 700; }
        button { width: 100%; padding: .7rem; background: #38BDF8; color: #fff; font-weight: 600; border: 0; border-radius: 8px; font-size: 1rem; cursor: pointer; }
    </style>
</head>
<body>
<div class="card">
    <h1>Quote {{ $quote->quote_number }}</h1>
    <table>
        <tr><th>Description</th><th>Qty</th><th>Total</th></tr>
        @foreach ($quote->lines as $line)
            <tr>
                <td>{{ $line->description }}</td>
                <td>{{ $line->quantity }}</td>
                <td>€{{ number_format($line->line_total_cents / 100, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total"><td colspan="2">Total</td><td>€{{ number_format($quote->total_cents / 100, 2) }}</td></tr>
    </table>
    @if ($quote->valid_until)
        <p style="color:#64748b;font-size:.85rem">Valid until {{ $quote->valid_until->format('d-m-Y') }}.</p>
    @endif
    <form method="POST" action="{{ url('/quotes/accept/'.$token) }}">
        @csrf
        <button type="submit">Accept quote</button>
    </form>
</div>
</body>
</html>
