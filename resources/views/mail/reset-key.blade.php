<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset IV and SECRET KEY</title>
</head>

<body>
    <p>Dear NIBSS,</p>
    <p>Your IV and SECRET KEY have been reset. Please find the new keys below:</p>
    <ul>
        <li><strong>IV:</strong> {{ $iv }}</li>
        <li><strong>SECRET KEY:</strong> {{ $secret }}</li>
    </ul>
    <p>Please use these keys for encrypting and decrypting subsequent requests and responses.</p>
    <p>Regards,<br>Your Complex Biller System</p>
</body>

</html>
