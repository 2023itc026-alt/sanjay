<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background: #1a1a2e; }
        .otp-box { background: white; padding: 40px; border-radius: 10px; text-align: center; color: #333; width: 350px; }
        input { width: 100%; padding: 12px; margin: 20px 0; border: 1px solid #ddd; border-radius: 5px; text-align: center; font-size: 20px; letter-spacing: 5px; }
        button { width: 100%; padding: 12px; background: #f39c12; border: none; color: white; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="otp-box">
        <h2>Enter OTP</h2>
        <p>A 6-digit code was sent to your email.</p>
        <form action="process.php" method="POST">
            <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>">
            <input type="text" name="otp" maxlength="6" placeholder="000000" required>
            <button type="submit" name="verify_otp_submit">Verify Code</button>
        </form>
    </div>
</body>
</html>