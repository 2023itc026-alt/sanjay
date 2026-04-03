<?php
session_start();
require_once 'db_config.php';

// Security Gatekeeper - restricted to your specific email
$admin_email = "sanjayprasath297@gmail.com"; 
if (!isset($_SESSION['email']) || $_SESSION['email'] !== $admin_email) {
    header("Location: index.php"); exit();
}

// Fetch current packages
$result = $conn->query("SELECT * FROM trip_packages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Trip Packages</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container { padding: 40px; color: white; max-width: 1000px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(0,0,0,0.3); }
        th, td { padding: 15px; border: 1px solid rgba(255,255,255,0.1); text-align: left; }
        .action-btn { padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; }
        .del { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>🎁 Manage Trip Packages</h1>
        <a href="admin_dashboard.php" style="color: #ffa500;">← Back to Admin Home</a>

        <form action="process.php" method="POST" enctype="multipart/form-data" style="margin-top:30px; background: var(--glass); padding: 20px; border-radius: 10px;">
            <h3>Create New All-Inclusive Bundle</h3>
            <input type="text" name="pkg_name" placeholder="Package Name (e.g. Dubai Luxury Week)" required class="search-box">
            <input type="text" name="pkg_duration" placeholder="Duration (e.g. 5 Days / 4 Nights)" required class="search-box">
            <input type="number" step="0.01" name="pkg_price" placeholder="Total Package Price" required class="search-box">
            <textarea name="pkg_desc" placeholder="What's included? (Hotels, Flights, Tours...)" class="search-box"></textarea>
            <label>Package Thumbnail:</label>
            <input type="file" name="pkg_img" required style="margin-bottom: 15px;">
            <button type="submit" name="admin_add_package" class="book-btn">Launch Package</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Package Name</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="images/<?php echo $row['image']; ?>" width="60" style="border-radius: 5px;"></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['duration']; ?></td>
                    <td>$<?php echo $row['price']; ?></td>
                    <td>
                        <a href="process.php?delete_package=<?php echo $row['id']; ?>" class="action-btn del" onclick="return confirm('Delete this package?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>