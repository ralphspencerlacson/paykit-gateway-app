<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
</head>
<body>
    <h2>Hi {{ $details['name'] }}</h2>
    <p>Thank you for your payment of <strong>{{ $details['amount'] }}</strong>.</p>
    <p>Your receipt is attached to this email.</p>
</body>
</html>
