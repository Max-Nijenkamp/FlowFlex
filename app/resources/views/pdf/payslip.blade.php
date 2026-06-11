<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
<style>
    body { font-family: ui-sans-serif, system-ui, sans-serif; color: #0f172a; margin: 2.5rem; }
    h1 { color: #7C3AED; }
    table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
    th, td { text-align: left; padding: .5rem; border-bottom: 1px solid #e2e8f0; font-size: .9rem; }
    td:last-child { text-align: right; }
    .total td { font-weight: 700; border-top: 2px solid #0f172a; }
</style>
</head>
<body>
    <h1>{{ $companyName }}</h1>
    <h2>Payslip — {{ $payslip->employee->full_name }}</h2>
    <table>
        <tr><td>Gross</td><td>€{{ number_format($amounts['gross_cents'] / 100, 2) }}</td></tr>
        @foreach ($amounts['deductions'] as $deduction)
            <tr><td>{{ $deduction['name'] }}</td><td>−€{{ number_format($deduction['amount_cents'] / 100, 2) }}</td></tr>
        @endforeach
        <tr class="total"><td>Net</td><td>€{{ number_format($amounts['net_cents'] / 100, 2) }}</td></tr>
    </table>
</body>
</html>
