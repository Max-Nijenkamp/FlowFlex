<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join {{ $invitation?->company?->name ?? 'FlowFlex' }} — FlowFlex</title>
    <style>
        /* Switchboard+ tokens — minimal blade fallback until the Vue site ships */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background: #fbfaf8;
            background-image: radial-gradient(48rem 30rem at 15% -10%, rgba(79, 70, 229, 0.10), transparent 60%);
            color: #111827;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border: 1px solid #d8d4ca;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 1px 2px rgba(17,24,39,.05), 0 32px 64px -32px rgba(17,24,39,.22);
        }
        .kicker { font-family: ui-monospace, monospace; font-size: 10px; letter-spacing: .2em; color: #98a0ab; margin-bottom: 8px; }
        h1 { font-size: 26px; letter-spacing: -.02em; margin-bottom: 6px; }
        .sub { color: #4b5563; font-size: 14.5px; margin-bottom: 24px; }
        label { display: block; font-size: 13.5px; font-weight: 600; margin: 14px 0 6px; }
        input {
            width: 100%; padding: 10px 12px; font-size: 14px;
            border: 1px solid #d8d4ca; border-radius: 10px; background: #fff;
        }
        input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.15); }
        button {
            margin-top: 22px; width: 100%; padding: 11px; font-size: 14.5px; font-weight: 600;
            color: #fff; background: #4f46e5; border: 0; border-radius: 10px; cursor: pointer;
        }
        button:active { transform: scale(.98); }
        .error { color: #b91c1c; font-size: 12.5px; margin-top: 5px; }
        .dead { text-align: center; color: #4b5563; font-size: 14.5px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="kicker">WORKSPACE INVITE</div>
        @if ($invitation === null)
            <h1>This invite is no longer valid</h1>
            <p class="dead" style="margin-top:12px">It may have expired or been revoked — ask your workspace admin for a fresh link.</p>
        @else
            <h1>Join {{ $invitation->company->name }}</h1>
            <p class="sub">You are joining as <strong>{{ $invitation->role }}</strong> with {{ $invitation->email }}.</p>

            @error('token')<p class="error">{{ $message }}</p>@enderror

            <form method="POST" action="{{ route('invite.register.store', ['token' => $token]) }}">
                @csrf
                <label for="first_name">First name</label>
                <input id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                @error('first_name')<p class="error">{{ $message }}</p>@enderror

                <label for="last_name">Last name</label>
                <input id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                @error('last_name')<p class="error">{{ $message }}</p>@enderror

                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
                @error('password')<p class="error">{{ $message }}</p>@enderror

                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>

                <button type="submit">Create account</button>
            </form>
        @endif
    </div>
</body>
</html>
