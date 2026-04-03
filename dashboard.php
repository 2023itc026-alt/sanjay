<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['email'])) { header("Location: index.php"); exit(); }

$email = $_SESSION['email'];

// Define $res to fix the Undefined variable warning in image_7fbc65.jpg
$checkUser = $conn->prepare("SELECT is_admin FROM users WHERE email = ?");
$checkUser->bind_param("s", $email);
$checkUser->execute();
$res = $checkUser->get_result()->fetch_assoc();

// Correctly fetch the trips
// Fetch the trips - make sure departure_date is in the list!
$trip_query = $conn->prepare("SELECT id, destination, travel_date, departure_date, itinerary FROM trips WHERE user_email = ? ORDER BY created_at DESC");
$trip_query->bind_param("s", $email);
$trip_query->execute();
$trips = $trip_query->get_result();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container { padding: 40px; color: white; max-width: 1100px; margin: auto; }
        .trip-card { background: var(--glass); padding: 20px; border-radius: 15px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1); }
        .btn-group { margin-top: 15px; display: flex; gap: 10px; }
        .edit-btn { background: #3498db; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
       .btn-delete {
    background-color: #e74c3c; /* A professional red color */
    color: white !important;
    padding: 8px 20px;
    border-radius: 5px;
    text-decoration: none; /* Removes the underline link look */
    display: inline-block;
    font-size: 14px;
    font-weight: 600;
    transition: 0.3s;
    border: none;
    cursor: pointer;
    margin-left: 10px;
}

.btn-delete:hover {
    background-color: #c0392b; /* Darkens on hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?></h1>
        
        <?php while($row = $trips->fetch_assoc()): ?>
            <div class="trip-card">
                <h3><?php echo $row['destination']; ?></h3>
              <p><strong>Dates:</strong> 
    <?php echo date('M d, Y', strtotime($row['travel_date'])); ?> 
    <?php if(!empty($row['departure_date'])): ?>
        - <?php echo date('M d, Y', strtotime($row['departure_date'])); ?>
    <?php endif; ?>
</p>
                <p><?php echo nl2br(htmlspecialchars($row['itinerary'])); ?></p>
                
                <div class="btn-group">
                    <a href="edit_trip.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit Plan</a>
                    
                  <a href="process.php?delete_trip=<?php echo $row['id']; ?>" 
   class="btn-delete" 
   onclick="return confirm('Are you sure you want to delete this trip?')">
   Delete
</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php if($res['is_admin'] == 1): ?>
    <div style="text-align:center; padding: 20px;">
        <a href="admin_dashboard.php" style="color:red; font-weight:bold;">SECRET ADMIN PANEL</a>
    </div>
<?php endif; ?>
</body>
</html>