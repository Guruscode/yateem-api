<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #333;">Reset Password</h2>
        <p>Hello,</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p><strong>Reset Password</strong></p>
        <p>This is your password reset token: <strong>{{ $token }}</strong></p>
        <p>If you did not request a password reset, no further action is required.</p>
        <p>Regards,<br>Engr Dexter</p>
    </div>

</body>
</html>
