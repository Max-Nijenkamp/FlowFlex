<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $requisition->title }} — Careers</title>
<style>
    body { font-family: Inter, system-ui, sans-serif; background: #f1f5f9; display: grid; place-items: center; min-height: 100vh; margin: 0; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgb(0 0 0 / .08); padding: 2.5rem; width: 100%; max-width: 34rem; }
    label { display: block; font-size: .8rem; font-weight: 600; margin: 1rem 0 .25rem; color: #334155; }
    input { width: 100%; padding: .6rem .75rem; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; }
    button { width: 100%; margin-top: 1.5rem; padding: .7rem; background: #38BDF8; color: #fff; font-weight: 600; border: 0; border-radius: 8px; cursor: pointer; }
    .hp { position: absolute; left: -9999px; }
    .ok { background: #dcfce7; border-radius: 8px; padding: 1rem; color: #166534; }
</style>
</head>
<body>
<div class="card">
    <h1>{{ $requisition->title }}</h1>
    <p>{{ $requisition->description }}</p>
    @if (session('applied'))
        <div class="ok">✅ Application received — we'll be in touch.</div>
    @else
        <form method="POST" action="{{ url('/careers/'.$requisition->slug.'/apply') }}">
            @csrf
            <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off">
            <label>First name</label><input name="first_name" required>
            <label>Last name</label><input name="last_name" required>
            <label>Email</label><input name="email" type="email" required>
            <label>Phone (optional)</label><input name="phone">
            <button type="submit">Apply</button>
        </form>
    @endif
</div>
</body>
</html>
