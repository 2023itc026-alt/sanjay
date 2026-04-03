<?php
session_start();
require_once 'db_config.php';

// Security Gatekeeper - restricted to your specific email
$admin_email = "sanjayprasath297@gmail.com"; 
if (!isset($_SESSION['email']) || $_SESSION['email'] !== $admin_email) {
    header("Location: index.php"); exit();
}

// Fetch current vehicles to show for Editing/Deleting
$result = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Vehicles & Guides</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container { padding: 40px; color: white; max-width: 1000px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(0,0,0,0.3); }
        th, td { padding: 15px; border: 1px solid rgba(255,255,255,0.1); text-align: left; }
        .action-btn { padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; margin-right: 5px; }
        .del { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>🚕 Manage Vehicles & Guides</h1>
        <a href="admin_dashboard.php" style="color: #ffa500;">← Back to Admin Home</a>

        <form action="process.php" method="POST" enctype="multipart/form-data" style="margin-top:30px; background: var(--glass); padding: 20px; border-radius: 10px;">
            <h3>Register New Vehicle/Driver</h3>
            <input type="text" name="v_driver" placeholder="Driver Name" required class="search-box">
            <input type="text" name="v_model" placeholder="Car Model (e.g. Toyota Land Cruiser)" required class="search-box">
            <input type="number" step="0.01" name="v_price" placeholder="Price Per Day" required class="search-box">
            <label>Vehicle Image:</label>
            <input type="file" name="v_img" required style="margin-bottom: 15px;">
            <button type="submit" name="admin_add_vehicle" class="book-btn">Add to Fleet</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Driver</th>
                    <th>Vehicle</th>
                    <th>Price/Day</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="images/<?php echo $row['image']; ?>" width="60" style="border-radius: 5px;"></td>
                    <td><?php echo $row['driver_name']; ?></td>
                    <td><?php echo $row['car_model']; ?></td>
                    <td>$<?php echo $row['price_per_day']; ?></td>
                    <td>
                        <a href="process.php?delete_vehicle=<?php echo $row['id']; ?>" class="action-btn del" onclick="return confirm('Remove this vehicle from the fleet?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>