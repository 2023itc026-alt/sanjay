<?php
// Security: Check if user verified OTP first
if (!isset($_GET['verified']) || $_GET['verified'] !== 'true') {
    die("Unauthorized access. Please verify OTP first.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Password</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #1a1a2e; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .box { background: white; color: #333; padding: 30px; border-radius: 10px; width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #f39c12; border: none; color: white; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>New Password</h2>
        <form action="process.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
            <input type="password" name="new_password" placeholder="Enter New Password" required>
            <button type="submit" name="update_password_submit">Update Password</button>
        </form>
    </div>
</body>
</html>