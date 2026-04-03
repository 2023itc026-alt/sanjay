<?php 
session_start();
if (!isset($_SESSION['user'])) { header("Location: index.php"); exit(); }

$pkg = isset($_GET['pkg']) ? $_GET['pkg'] : 'Selected Trip';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Itinerary - <?php echo $pkg; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .details-container { padding: 50px; max-width: 1000px; margin: auto; overflow-y: auto; height: 100vh; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; color: #333; border-radius: 10px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: var(--primary); color: white; }
        tr:hover { background: #f9f9f9; }
        
        .extra-section { margin-top: 40px; background: var(--glass); padding: 20px; border-radius: 15px; }
        .activity-list { margin-top: 15px; list-style: none; }
        .activity-item { background: rgba(255,255,255,0.2); padding: 10px; margin-bottom: 5px; border-radius: 5px; display: flex; justify-content: space-between; }
        
        .add-btn { background: #2ecc71; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 600; }
        input[type="text"] { padding: 10px; width: 70%; border-radius: 5px; border: none; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="details-container">
        <a href="packages.php" style="color: var(--primary); text-decoration: none;">← Back to Packages</a>
        <h1 style="margin: 20px 0;">Day-by-Day Itinerary: <?php echo $pkg; ?></h1>

        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Morning</th>
                    <th>Afternoon</th>
                    <th>Evening / Stay</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Day 1</td>
                    <td>Arrival & Airport Pickup</td>
                    <td>Hotel Check-in & Relaxation</td>
                    <td>Welcome Dinner at Hotel</td>
                </tr>
                <tr>
                    <td>Day 2</td>
                    <td>Guided City Tour</td>
                    <td>Local Market Visit</td>
                    <td>Cultural Dance Show</td>
                </tr>
               <tr>
    <td>Day 3</td>
    <td>Adventure Activity (Hiking/Safari)</td>
    <td>Lunch with Locals</td>
    <td>
        Free Evening 
        <a href="extra_activities.php?pkg=<?php echo urlencode($pkg); ?>" 
           style="color: #f39c12; font-size: 12px; text-decoration: none; font-weight: bold; margin-left: 10px;">
           +Add activities
        </a>
    </td>
</tr>
                <tr>
                    <td>Day 4</td>
                    <td>Photography Session</td>
                    <td>Souvenir Shopping</td>
                    <td>Farewell Celebration</td>
                </tr>
                <tr>
                    <td>Day 5</td>
                    <td>Breakfast</td>
                    <td>Last-minute Packing</td>
                    <td>Departure Transfer</td>
                </tr>
            </tbody>
        </table>

          
</body>
</html>