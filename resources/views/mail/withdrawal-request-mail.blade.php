<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Withdrawal Notification - Coinshares Mining</title>
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
            background: #e74c3c;
            color: #fff;
            padding: 10px 20px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn:hover {
            background: #c0392b;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Withdrawal Notification</h1>
        </div>
        <div class="email-body">
            <h2>Dear {{ $user->name }},</h2>
            <p>Your account is currently applying for a withdrawal. Please review the withdrawal details carefully to
                ensure accuracy from your Coinshares Mining account.</p>
            <div class="details">
                <p><strong>Details of the Withdrawal:</strong></p>
                <p><strong>Amount:</strong> {{ $amount }}</p>
                <p><strong>Withdrawal Method:</strong> {{ $method }}</p>
                <p><strong>Wallet Address:</strong> {{ $wallet_address }}</p>
                <p><strong>Date Processed:</strong> {{ $date }}</p>
            </div>
            <p>For your security, always check your account balance and transaction history in your Coinshares Mining
                account. If you notice any discrepancies or if this withdrawal was not authorized by you, please notify
                us immediately.</p>
            <a href="{{ $account_url }}" class="btn">View Account</a>
        </div>
        <div class="email-footer">
            <p>Thank you for choosing Coinshares Mining. If you have any questions or need further assistance, feel free
                to reach out to us at
                <a href="mailto:support@coinsharesmining.com">support@coinsharesmining.com</a>.
            </p>
            <p>Best Regards,</p>
            <p>The Management Team</p>
            <p><strong>Coinshares Mining</strong></p>
        </div>
    </div>
</body>

</html>
