<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Deposit Confirmation - Coinshares Mining</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: #2c3e50;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 20px;
        }

        .email-body h2 {
            color: #2c3e50;
            font-size: 20px;
        }

        .email-body p {
            margin: 10px 0;
        }

        .email-body .details {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px 0;
        }

        .email-body .details p {
            margin: 5px 0;
        }

        .email-footer {
            background: #f4f4f9;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }

        .btn {
            display: inline-block;
            background: #3498db;
            color: #fff;
            padding: 10px 20px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Deposit Confirmation</h1>
        </div>
        <div class="email-body">
            <h2>Dear {{ $data['name'] }},</h2>
            <p>We are pleased to confirm that we have received your recent deposit into your Coinshares Mining account.
            </p>
            <div class="details">
                <p><strong>Deposit Details:</strong></p>
                <p><strong>Amount Deposited:</strong> ${{ $data['transaction']->amount }}</p>
                <p><strong>Deposit Method:</strong> {{ $data['transaction']->method }}</p>
                <p><strong>Date Received:</strong> {{ $data['transaction']->updated_at }}</p>
            </div>
            <p>Your account has been credited with the above amount.</p>
            <p>If you have any questions or require further assistance, please don't hesitate to reach out to us at
                <a href="mailto:support@coinsharesmining.com">support@coinsharesmining.com</a>.
            </p>
            <br />
            <p>Best Regards,</p>
            <p>The Management Team</p>
        </div>
        <div class="email-footer">

            <p><strong>Coinshares Mining</strong></p>
        </div>
    </div>
</body>

</html>
