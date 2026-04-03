<?php
session_start();
require_once 'db_config.php';

// Security Gatekeeper
$admin_email = "sanjayprasath297@gmail.com"; 
if (!isset($_SESSION['email']) || $_SESSION['email'] !== $admin_email) {
    header("Location: index.php"); exit();
}

// Fetch current places including the new price column
$result = $conn->query("SELECT * FROM explore_places ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Travel Planning | Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container { padding: 40px; color: white; max-width: 1100px; margin: auto; }
        .glass-form { 
            background: rgba(255,255,255,0.05); 
            padding: 30px; 
            border-radius: 15px; 
            border: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 40px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(0,0,0,0.3); border-radius: 10px; overflow: hidden; }
        th, td { padding: 15px; border: 1px solid rgba(255,255,255,0.05); text-align: left; }
        th { background: rgba(243, 156, 18, 0.2); color: #f39c12; }
        .search-box { width: 100%; padding: 12px; border-radius: 8px; border: none; margin-bottom: 15px; background: rgba(255,255,255,0.1); color: white; }
        .action-btn { padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; }
        .del { background: #e74c3c; color: white; }
        .edit { background: #3498db; color: white; margin-right: 5px; }
        .price-text { color: #2ecc71; font-weight: bold; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>🛠️ Manage Travel Planning</h1>
            <a href="admin_dashboard.php" style="color: #f39c12; text-decoration: none; font-weight: bold;">← Back to Dashboard</a>
        </div>

        <div class="glass-form">
            <form action="process.php" method="POST" enctype="multipart/form-data">
                <h3 style="margin-bottom: 20px; color: #f39c12;">Add New Destination Card</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label>Target City:</label>
                        <select name="target_destination" required class="search-box">
                            <option value="Dubai">Dubai</option>
                            <option value="Paris">Paris</option>
                            <option value="London">London</option>
                        </select>

                        <input type="text" name="p_name" placeholder="Place Name (e.g. Burj Khalifa)" required class="search-box">

                        <label>Category:</label>
                        <select name="p_cat" class="search-box">
                            <option value="Hotel">Hotel</option>
                            <option value="Restaurant">Food</option>
                            <option value="Museum">Culture</option>
                            <option value="Park">Nature</option>
                        </select>
                    </div>

                    <div>
                        <label>Price ($):</label>
                        <input type="number" step="0.01" name="p_price" placeholder="Amount (e.g. 150.00)" required class="search-box">

                        <label>Upload Image:</label>
                        <input type="file" name="p_img" required class="search-box" style="padding: 8px;">

                        <textarea name="p_desc" placeholder="Brief description of the place..." class="search-box" style="height: 90px;"></textarea>
                    </div>
                </div>
                
                <button type="submit" name="admin_add_place" class="add-itinerary-btn" style="width: 100%; cursor: pointer;">Add to Live Site</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="images/<?php echo $row['image']; ?>" width="60" style="border-radius: 5px;"></td>
                    <td><strong><?php echo $row['name']; ?></strong><br><small style="opacity: 0.6;"><?php echo $row['cat']; ?></small></td>
                    <td><?php echo $row['target_destination']; ?></td>
                    <td class="price-text">$<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <a href="edit_place.php?id=<?php echo $row['id']; ?>" class="action-btn edit">Edit</a>
                        <a href="process.php?delete_place=<?php echo $row['id']; ?>" class="action-btn del" onclick="return confirm('Delete this place?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>