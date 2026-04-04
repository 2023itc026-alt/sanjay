<?php 
session_start();
require_once 'db_config.php'; // Ensure this file has your $conn

if (!isset($_SESSION['user'])) { header("Location: index.php"); exit(); }

// 1. Fetch Dynamic Packages from Database
$stmt = $conn->prepare("SELECT * FROM trip_packages");
$stmt->execute();
$dynamic_packages = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exclusive Trip Packages | Odyssey Travels</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- STABILIZED GLASS STYLES --- */
        :root { --primary: #f39c12; --glass: rgba(255,255,255,0.05); }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('bg.jpg');
            background-size: cover; background-attachment: fixed; color: white;
        }

        .pkg-container { padding: 50px; max-width: 1200px; margin: auto; }
        .pkg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-top: 30px; }
        
        .pkg-card { 
            background: var(--glass); backdrop-filter: blur(15px);
            border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);
            transition: 0.3s; transform: translateZ(0); /* Flicker Prevention */
        }
        .pkg-card:hover { transform: translateY(-5px); border-color: var(--primary); background: rgba(255,255,255,0.1); }
        .pkg-img { width: 100%; height: 220px; object-fit: cover; background: #111; }
        .pkg-content { padding: 25px; }
        
        .info-badge { background: rgba(243, 156, 18, 0.2); color: var(--primary); padding: 5px 12px; border-radius: 5px; font-size: 11px; font-weight: 800; margin-bottom: 15px; display: inline-block; text-transform: uppercase; }
        .detail-row { display: flex; gap: 12px; margin-top: 12px; font-size: 14px; opacity: 0.8; line-height: 1.4; }
        .detail-row i { color: var(--primary); min-width: 20px; }

        .book-btn { 
            width: 100%; background: var(--primary); color: white; border: none; 
            padding: 14px; border-radius: 30px; margin-top: 25px; cursor: pointer; 
            font-weight: bold; transition: 0.3s; text-transform: uppercase;
        }
        .book-btn:hover { background: #e67e22; transform: scale(1.02); }
    </style>
</head>
<body>
    <div class="pkg-container">
        <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight:bold;">← Back to Home</a>
        <h1 style="margin-top: 20px; text-align: center; font-size: 2.5rem;">All-Inclusive Holiday Packages</h1>

        <div class="pkg-grid">
            
                     <?php while($pkg = $dynamic_packages->fetch_assoc()): ?>
                <div class="pkg-card">
                    <img src="images/<?php echo $pkg['image']; ?>" class="pkg-img" onerror="this.src='images/default.jpg'">
                   <div class="pkg-content">
    <span class="info-badge"><?php echo htmlspecialchars($pkg['duration']); ?></span>
    <h3><?php echo htmlspecialchars($pkg['name']); ?></h3>
    
    <div class="detail-row"><i>✈️</i> <strong>Arrival:</strong> <?php echo htmlspecialchars($pkg['arrival']); ?></div>
    <div class="detail-row"><i>🏨</i> <strong>Stay:</strong> <?php echo htmlspecialchars($pkg['stay']); ?></div>
    <div class="detail-row"><i>🏃</i> <strong>Activities:</strong> <?php echo htmlspecialchars($pkg['activities']); ?></div>
    <div class="detail-row"><i>🛫</i> <strong>Departure:</strong> <?php echo htmlspecialchars($pkg['departure']); ?></div>
    
    <div class="detail-row" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
        <i style="color: #2ecc71;">💰</i> 
        <strong style="font-size: 1.2rem; color: #2ecc71;">
            $<?php echo number_format($pkg['price'], 2); ?>
        </strong> 
        <span style="font-size: 0.8rem; opacity: 0.6; margin-left: 5px;">/ per person</span>
    </div>

    <a href="package_details.php?pkg=<?php echo urlencode($pkg['name']); ?>" style="text-decoration: none;">
        <button class="book-btn">Enquire Now</button>
    </a>
</div>
                </div>
            <?php endwhile; ?>

        </div>
    </div>
</body>
</html>