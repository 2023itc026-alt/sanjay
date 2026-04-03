<?php
session_start();
require_once 'db_config.php';

// 1. Capture All Parameters (Location, Vehicle, and Guide)
$selected_location = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : null;
$selected_vehicle = isset($_GET['vehicle_id']) ? intval($_GET['vehicle_id']) : null;
$selected_guide = isset($_GET['guide_id']) ? intval($_GET['guide_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fleet & Guides | Odyssey Travels</title>
    <style>
        /* --- STABILIZED GLASS STYLES --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('bg.jpg');
            background-size: cover; background-position: center; background-attachment: fixed; 
            min-height: 100vh; color: white; padding: 50px 5%;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .glass {
            position: relative; background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 25px;
            overflow: hidden; cursor: pointer; transition: 0.4s ease;
            transform: translateZ(0); will-change: transform;
        }
        .glass::before {
            content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); z-index: -1;
        }
        .glass:hover { transform: translateY(-8px); background: rgba(255, 255, 255, 0.1); border-color: #f39c12; }
        .badge { background: #f39c12; padding: 4px 10px; border-radius: 5px; font-size: 10px; font-weight: bold; }
        .price-tag { color: #2ecc71; font-weight: bold; font-size: 1.1rem; }
        .detail-item { display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 10px 0; }
        .label { color: #f39c12; font-weight: bold; font-size: 0.9rem; }
        .back-link { color: #f39c12; text-decoration: none; font-weight: bold; display: block; margin-bottom: 20px; }
/* --- GRID FOR DESTINATIONS --- */
.destination-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-top: 30px;
}

/* --- IMAGE CARD STYLE --- */
.dest-card {
    position: relative;
    height: 250px;
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: transform 0.4s ease, border-color 0.4s ease;
}

.dest-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.dest-card:hover img {
    transform: scale(1.1); /* Zoom effect from your screenshot */
}

/* --- GLASS OVERLAY FOR TEXT --- */
.dest-name-overlay {
    position: absolute;
    bottom: 0;
    width: 100%;
    padding: 20px;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    text-align: center;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.dest-name-overlay h2 {
    margin: 0;
    font-size: 1.8rem;
    color: white;
    text-transform: capitalize;
    letter-spacing: 1px;
}

.dest-card:hover {
    border-color: #f39c12;
    transform: translateY(-10px);
}
    </style>
</head>
<body>

<div class="container">
    
  <?php if (!$selected_location): ?>
        <h1 class="main-title" style="text-align:center; margin-bottom:40px;">Select Your Destination</h1>
        
        <div class="destination-grid">
            <div class="dest-card" onclick="location.href='?location=Dubai'">
                <img src="images/dubai.jpg" onerror="this.src='images/bg.jpg'">
                <div class="dest-name-overlay">
                    <h2>Dubai</h2>
                </div>
            </div>

            <div class="dest-card" onclick="location.href='?location=Paris'">
                <img src="images/paris.jpg" onerror="this.src='images/bg.jpg'">
                <div class="dest-name-overlay">
                    <h2>Paris</h2>
                </div>
            </div>

            <div class="dest-card" onclick="location.href='?location=Tokyo'">
                <img src="images/tokyo.jpg" onerror="this.src='images/bg.jpg'">
                <div class="dest-name-overlay">
                    <h2>Tokyo</h2>
                </div>
            </div>

            <div class="dest-card" onclick="location.href='?location=Malaysia'">
                <img src="images/malaysia.jpg" onerror="this.src='images/bg.jpg'">
                <div class="dest-name-overlay">
                    <h2>Malaysia</h2>
                </div>
            </div>

            <div class="dest-card" onclick="location.href='?location=Goa'">
                <img src="images/goa.avif" onerror="this.src='images/bg.jpg'">
                <div class="dest-name-overlay">
                    <h2>Goa</h2>
                </div>
            </div>

            <div class="dest-card" onclick="location.href='?location=Manali'">
                <img src="images/manali.jpg" onerror="this.src='images/bg.jpg'">
                <div class="dest-name-overlay">
                    <h2>Manali</h2>
                </div>
            </div>
        </div>
    <?php elseif ($selected_location && !$selected_vehicle): ?>
        <a href="vehicles_guides.php" class="back-link">← Change Destination</a>
        <h1 style="text-align:center; margin-bottom:40px;">Premium Fleet in <?php echo $selected_location; ?></h1>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
            <?php
            // Dynamic Fetch Filtered by Location
            $v_stmt = $conn->prepare("SELECT * FROM vehicles WHERE location = ?");
            $v_stmt->bind_param("s", $selected_location);
            $v_stmt->execute();
            $vehicles = $v_stmt->get_result();

            while($v = $vehicles->fetch_assoc()): 
                // Hybrid Fallbacks for Vehicle Data
                $v_name = !empty($v['car_model']) ? $v['car_model'] : "Luxury Sedan";
                $v_img = !empty($v['image']) ? $v['image'] : "toyota.jpg";
            ?>
                <div class="glass" onclick="location.href='?location=<?php echo $selected_location; ?>&vehicle_id=<?php echo $v['id']; ?>'">
                    <img src="images/<?php echo $v_img; ?>" style="width:100%; height:200px; object-fit:cover;" onerror="this.src='images/toyota.jpg'">
                    <div style="padding:20px;">
                        <span class="badge">AVAILABLE</span>
                        <h3 style="margin:10px 0;"><?php echo $v_name; ?></h3>
                        <p style="opacity:0.7;">Driver: <?php echo !empty($v['driver_name']) ? $v['driver_name'] : "Expert Pilot"; ?></p>
                        <p class="price-tag">$<?php echo number_format($v['price_per_day'], 2); ?> / day</p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php elseif ($selected_vehicle && !$selected_guide): ?>
        <a href="?location=<?php echo $selected_location; ?>" class="back-link">← Back to <?php echo $selected_location; ?> Fleet</a>
        <h1 style="text-align:center; margin-bottom:40px;">Select Your Expert Guide</h1>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
            <?php
            $g_stmt = $conn->prepare("SELECT * FROM guides WHERE vehicle_id = ?");
            $g_stmt->bind_param("i", $selected_vehicle);
            $g_stmt->execute();
            $guides = $g_stmt->get_result();

            while($g = $guides->fetch_assoc()): 
                $g_img = !empty($g['picture']) ? $g['picture'] : "kavin.jpg";
            ?>
                <div class="glass" style="text-align:center; padding:30px;" onclick="location.href='?location=<?php echo $selected_location; ?>&vehicle_id=<?php echo $selected_vehicle; ?>&guide_id=<?php echo $g['id']; ?>'">
                    <img src="images/<?php echo $g_img; ?>" style="width:100px; height:100px; border-radius:50%; border:3px solid #f39c12;" onerror="this.src='images/kavin.jpg'">
                    <h3 style="margin-top:15px;"><?php echo !empty($g['name']) ? $g['name'] : "Tour Specialist"; ?></h3>
                    <p style="opacity:0.7;"><?php echo !empty($g['experience']) ? $g['experience'] : "5+ Years"; ?> Experience</p>
                    <span style="color:#f39c12; font-weight:bold;">View Details →</span>
                </div>
            <?php endwhile; ?>
        </div>

    <?php elseif ($selected_guide): ?>
        <?php 
            $res = mysqli_query($conn, "SELECT * FROM guides WHERE id = $selected_guide");
            $g = mysqli_fetch_assoc($res);
            $pic = !empty($g['picture']) ? $g['picture'] : "kavin.jpg";
        ?>
        <div style="display:flex; flex-direction:column; align-items:center;">
            <a href="?location=<?php echo $selected_location; ?>&vehicle_id=<?php echo $selected_vehicle; ?>" class="back-link">← Back to Guides</a>
            <div class="glass" style="display:flex; max-width:850px; width:100%; padding:40px; gap:40px; align-items:center;">
                <div style="min-width:220px; height:220px; background:#111; border-radius:20px; overflow:hidden;">
                    <img src="images/<?php echo $pic; ?>" style="width:100%; height:100%; object-fit:cover;" onerror="this.src='images/kavin.jpg'">
                </div>
                <div style="flex:1;">
                    <span class="badge" style="background:#27ae60;">CERTIFIED EXPERT</span>
                    <h2 style="font-size:2.5rem; margin:10px 0;"><?php echo !empty($g['name']) ? $g['name'] : "Kavin Kumar"; ?></h2>
                    <div style="margin:20px 0;">
                        <div class="detail-item"><span class="label">License No:</span><span><?php echo !empty($g['license_no']) ? $g['license_no'] : "TN-67-2021-00456"; ?></span></div>
                        <div class="detail-item"><span class="label">Age:</span><span><?php echo !empty($g['age']) ? $g['age'] : "34"; ?> Years</span></div>
                        <div class="detail-item"><span class="label">Experience:</span><span><?php echo !empty($g['experience']) ? $g['experience'] : "5 Years"; ?></span></div>
                    </div>
                    <a href="process_booking.php?g_id=<?php echo $selected_guide; ?>&v_id=<?php echo $selected_vehicle; ?>" style="display:inline-block; padding:15px 40px; background:#f39c12; color:white; border-radius:30px; text-decoration:none; font-weight:bold;">CONFIRM & BOOK NOW</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>