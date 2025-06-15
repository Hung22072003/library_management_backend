<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <p>Dear {{ $user->name ?? 'user' }},</p>

    <p>
        Thank you for using our service. To verify your email, please enter the following
        One Time Password (OTP):
    </p>

    <h2 style="color: #2c3e50;">{{ $otp }}</h2>

    <p>This OTP is valid for <strong>1 minute</strong> from the receipt of this email.</p>

    <br>

    <p>Best regards,<br><strong>DUT.Library</strong></p>
</body>
</html>
