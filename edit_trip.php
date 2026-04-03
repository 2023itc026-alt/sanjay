<?php
session_start();
require_once 'db_config.php';

// 1. Get User Context
$user_email = isset($_SESSION['user']) ? $_SESSION['user'] : '';
$trip_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Add Place Logic (Triggered from select_places.php)
if ($trip_id > 0 && isset($_GET['add_place'])) {
    $new_place = htmlspecialchars($_GET['add_place']);
    
    $get_stmt = $conn->prepare("SELECT itinerary FROM trips WHERE id = ?");
    $get_stmt->bind_param("i", $trip_id);
    $get_stmt->execute();
    $res = $get_stmt->get_result()->fetch_assoc();
    
    $current = $res['itinerary'];
    $updated = empty($current) ? $new_place : $current . ", " . $new_place;

    $upd = $conn->prepare("UPDATE trips SET itinerary = ? WHERE id = ?");
    $upd->bind_param("si", $updated, $trip_id);
    $upd->execute();
    
    header("Location: edit_trip.php?id=" . $trip_id);
    exit();
}

// 3. Fetch Trip Data (Using ID-only for stability)
$stmt = $conn->prepare("SELECT * FROM trips WHERE id = ?");
$stmt->bind_param("i", $trip_id);
$stmt->execute();
$trip = $stmt->get_result()->fetch_assoc();

if (!$trip) {
    die("Error: Trip not found for ID: " . $trip_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Trip to <?php echo htmlspecialchars($trip['destination']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- GLASS INPUTS & BUTTONS --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('bg.jpg');
            background-size: cover; background-attachment: fixed; color: white;
        }

        .planner-layout { display: grid; grid-template-columns: 350px 1fr 350px; gap: 20px; padding: 30px; }
        
        .column { 
            background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); 
            border-radius: 20px; padding: 25px; border: 1px solid rgba(255,255,255,0.1); 
            transform: translateZ(0); /* Flicker Prevention */
        }

        input[type="date"], textarea {
            width: 100%; padding: 12px; border-radius: 10px; margin-top: 5px;
            background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);
            color: white; outline: none; transition: 0.3s;
        }

        input[type="date"]:focus { border-color: #f39c12; background: rgba(255, 255, 255, 0.15); }

        .save-btn {
            display: inline-block; width: 100%; padding: 15px; background: #f39c12;
            color: white; border: none; border-radius: 30px; font-weight: bold;
            text-transform: uppercase; cursor: pointer; transition: 0.3s;
            margin-top: 20px; transform: translateZ(0); 
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.2);
        }

        .save-btn:hover {
            background: #e67e22; transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4);
        }

        .itinerary-item { 
            background: rgba(0,0,0,0.3); margin-bottom: 10px; padding: 12px; 
            border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); 
            display: flex; justify-content: space-between; 
        }
    </style>
</head>
<body>
    <div class="planner-layout">
        <div class="column">
            <a href="dashboard.php" style="color: #f39c12; text-decoration: none; font-weight: bold;">← Back to Dashboard</a>
            <h3 style="margin-top: 25px;">Currently Editing</h3>
            <div style="background: rgba(243, 156, 18, 0.1); padding: 15px; border-radius: 15px; margin-top: 15px; border: 1px solid #f39c12;">
                <h4 style="color: #f39c12;">Trip to <?php echo htmlspecialchars($trip['destination']); ?></h4>
                <p style="font-size: 0.8rem; opacity: 0.7;">Refine your dates and selected spots below.</p>
            </div>
        </div>

        <div class="column" style="text-align: center;">
            <h2>Modify Your Journey</h2>
            <p>Update your travel plans for <?php echo htmlspecialchars($trip['destination']); ?></p>
            <hr style="opacity: 0.2; margin: 20px 0;">
            
            <form action="process.php" method="POST" style="text-align: left;">
                <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="font-size: 0.9rem; opacity: 0.8;">Travel Date:</label>
                        <input type="date" name="travel_date" value="<?php echo $trip['travel_date']; ?>" required>
                    </div>
                    <div>
                        <label style="font-size: 0.9rem; opacity: 0.8;">Departure Date:</label>
                        <input type="date" name="departure_date" value="<?php echo $trip['departure_date']; ?>" required>
                    </div>
                </div>

                <label style="display: block; margin: 20px 0 10px; font-size: 0.9rem; opacity: 0.8;">Itinerary Details:</label>
                <div style="max-height: 300px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1);">
                    <?php 
                    $places = array_filter(preg_split('/[,\n]+/', $trip['itinerary']));
                    foreach($places as $place): 
                        $place = trim($place);
                    ?>
                    <div class="itinerary-item">
                        <span onclick="fetchPlaceDetails('<?php echo addslashes($place); ?>')" style="cursor: pointer; flex: 1;">
                            📍 <?php echo htmlspecialchars($place); ?>
                        </span>
                        <input type="hidden" name="itinerary_items[]" value="<?php echo htmlspecialchars($place); ?>">
                        <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #ff4d4d; cursor: pointer;">✕</button>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="select_places.php?destination=<?php echo urlencode($trip['destination']); ?>&edit_id=<?php echo $trip_id; ?>" 
                       style="color: #f39c12; text-decoration: none; font-weight: bold;">
                       + Add More Places from <?php echo htmlspecialchars($trip['destination']); ?>
                    </a>
                </div>            

                <button type="submit" name="update_trip" class="save-btn">Save Changes</button>
            </form>
        </div>

        <div class="column">
            <h3>Trip Summary</h3>
            <div style="display: flex; justify-content: space-between; margin-top: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                <span>Destination:</span>
                <strong style="color: #f39c12;"><?php echo htmlspecialchars($trip['destination']); ?></strong>
            </div>
            <p style="font-size: 0.8rem; opacity: 0.5; margin-top: 20px; line-height: 1.6;">
                Note: Destinations are fixed once created. To change it, please delete this trip from your dashboard.
            </p>
            <a href="dashboard.php" style="display: block; text-align: center; margin-top: 30px; color: #ff4d4d; text-decoration: none; font-size: 0.9rem;">Cancel Editing</a>
        </div>
    </div>

    <script>
        function fetchPlaceDetails(name) {
            // Your existing fetch logic for get_place_info.php
            alert("Fetching details for: " + name); 
        }
    </script>
</body>
</html>