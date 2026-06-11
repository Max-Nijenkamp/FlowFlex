<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join {{ $companyName }} — FlowFlex</title>
    <style>
        body { font-family: Inter, system-ui, sans-serif; background: #f1f5f9; display: grid; place-items: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgb(0 0 0 / .08); padding: 2.5rem; width: 100%; max-width: 26rem; }
        h1 { font-size: 1.25rem; margin: 0 0 .25rem; }
        p { color: #64748b; margin: 0 0 1.5rem; font-size: .9rem; }
        label { display: block; font-size: .8rem; font-weight: 600; margin: 1rem 0 .25rem; color: #334155; }
        input { width: 100%; padding: .6rem .75rem; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: .95rem; }
        input[readonly] { background: #f8fafc; color: #64748b; }
        button { width: 100%; margin-top: 1.5rem; padding: .7rem; background: #38BDF8; color: #fff; font-weight: 600; border: 0; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .error { color: #dc2626; font-size: .8rem; margin-top: .25rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>Join {{ $companyName }}</h1>
    <p>Create your FlowFlex account to get started.</p>
    <form method="POST" action="{{ url('/register/invite/'.$token) }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" type="email" value="{{ $email }}" readonly>

        <label for="first_name">First name</label>
        <input id="first_name" name="first_name" value="{{ old('first_name') }}" required>
        @error('first_name')<div class="error">{{ $message }}</div>@enderror

        <label for="last_name">Last name</label>
        <input id="last_name" name="last_name" value="{{ old('last_name') }}" required>
        @error('last_name')<div class="error">{{ $message }}</div>@enderror

        <label for="password">Password (12+ characters)</label>
        <input id="password" name="password" type="password" required minlength="12">
        @error('password')<div class="error">{{ $message }}</div>@enderror

        <button type="submit">Create account</button>
    </form>
</div>
</body>
</html>
