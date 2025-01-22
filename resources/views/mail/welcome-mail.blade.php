<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to Coinshares Mining</title>
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

        .email-body {
            padding: 20px;
        }

        .email-body h2 {
            color: #2c3e50;
        }

        .email-body a {
            color: #3498db;
            text-decoration: none;
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
            background: #2c3e50;
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
            <h1>Welcome to Coinshares Mining</h1>
        </div>
        <div class="email-body">
            <h2>Hello {{ $user->name }},</h2>
            <p>Thank you for registering with <strong>Coinshares Mining</strong>. We're thrilled to have you join our
                community!</p>
            <p>To get started, click the button below to log in to your account:</p>
            <p>
                <a href="{{ $url }}" class="btn">Login to Your Account</a>
            </p>
            <p>If you have any questions or need assistance, feel free to reach out to our support team at
                <a href="mailto:support@coinsharesmining.com">support@coinsharesmining.com</a>.
            </p>
            <p>Thank you for choosing <strong>Coinshares Mining</strong>. We’re excited to help you achieve your
                investment goals!
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
