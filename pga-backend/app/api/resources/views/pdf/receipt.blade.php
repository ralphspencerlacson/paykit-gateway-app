<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .details {
            margin-bottom: 20px;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #007BFF;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Payment Receipt</h2>
        <p>{{ now()->toDayDateTimeString() }}</p>
    </div>

    <div class="details">
        <p><strong>Customer Name:</strong> {{ $name ?? 'N/A' }}</p>
        <p><strong>Amount Paid:</strong> <span class="amount">{{ $amount }}</span></p>
    </div>

    <div class="footer">
        <p>This is a system-generated receipt. No signature required.</p>
    </div>
</body>
</html>
