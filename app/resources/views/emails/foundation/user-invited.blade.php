<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>You've been invited</title>
</head>
<body style="font-family: sans-serif; color: #111827; padding: 40px; background: #f9fafb;">
    <div style="max-width: 520px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 40px; border: 1px solid #e5e7eb;">
        <h1 style="font-size: 20px; margin-bottom: 8px;">You've been invited to {{ $company->name }}</h1>
        <p style="color: #6b7280; margin-bottom: 24px;">Hi {{ $user->first_name }}, you've been invited to join <strong>{{ $company->name }}</strong> on FlowFlex.</p>
        <a href="{{ $acceptUrl }}" style="display: inline-block; background: #111827; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">Accept Invitation</a>
        <p style="margin-top: 24px; color: #9ca3af; font-size: 13px;">This invitation expires on {{ $expiresAt }}. If you did not expect this, ignore this email.</p>
        <p style="margin-top: 8px; color: #9ca3af; font-size: 12px;">Or copy this link: {{ $acceptUrl }}</p>
    </div>
</body>
</html>
