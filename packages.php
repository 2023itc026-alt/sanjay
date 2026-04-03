<?php 
session_start();
if (!isset($_SESSION['user'])) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exclusive Trip Packages | Odyssey Travels</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .pkg-container { padding: 50px; max-width: 1200px; margin: auto; overflow-y: auto; height: 100vh; }
        .pkg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-top: 30px; }
        
        .pkg-card { 
            background: var(--glass); 
            border-radius: 20px; 
            overflow: hidden; 
            border: 1px solid rgba(255,255,255,0.1);
            transition: 0.3s;
        }
        .pkg-card:hover { transform: translateY(-5px); border-color: var(--primary); }
        .pkg-img { width: 100%; height: 200px; object-fit: cover; }
        .pkg-content { padding: 20px; }
        
        .info-badge { background: rgba(243, 156, 18, 0.2); color: var(--primary); padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; margin-bottom: 10px; display: inline-block; }
        
        .detail-row { display: flex; gap: 15px; margin-top: 15px; font-size: 14px; opacity: 0.9; }
        .detail-row i { color: var(--primary); }

        .book-btn { 
            width: 100%; background: var(--primary); color: white; border: none; 
            padding: 12px; border-radius: 8px; margin-top: 20px; cursor: pointer; font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="pkg-container">
        <a href="index.php" style="color: var(--primary); text-decoration: none;">← Back to Home</a>
        <h1 style="margin-top: 20px; text-align: center;">All-Inclusive Holiday Packages</h1>

        <div class="pkg-grid">
            <div class="pkg-card">
                <img src="dubai.jpg" class="pkg-img">
                <div class="pkg-content">
                    <span class="info-badge">5 DAYS / 4 NIGHTS</span>
                    <h3>Luxury Dubai Escape</h3>
                    
                    <div class="detail-row"><i>✈️</i> <strong>Arrival:</strong> DXB International (Terminal 3)</div>
                    <div class="detail-row"><i>🏨</i> <strong>Stay:</strong> Atlantis, The Palm (5-Star)</div>
                    <div class="detail-row"><i>🏃</i> <strong>Activities:</strong> Desert Safari, Burj Khalifa Tour, Marina Cruise.</div>
                    <div class="detail-row"><i>🛫</i> <strong>Departure:</strong> Private Airport Transfer included.</div>
                    
                   <a href="package_details.php?pkg=Dubai" style="text-decoration: none;">
    <button class="book-btn">Enquire Now</button>
</a>
                </div>
            </div>

            <div class="pkg-card">
                <img src="https://images.unsplash.com/photo-1531310197839-ccf54634509e?auto=format&fit=crop&w=600" class="pkg-img">
                <div class="pkg-content">
                    <span class="info-badge">7 DAYS / 6 NIGHTS</span>
                    <h3>Swiss Alps Adventure</h3>
                    
                    <div class="detail-row"><i>✈️</i> <strong>Arrival:</strong> Zurich Airport (ZRH)</div>
                    <div class="detail-row"><i>🏨</i> <strong>Stay:</strong> Grand Hotel Zermatterhof</div>
                    <div class="detail-row"><i>🏃</i> <strong>Activities:</strong> Mount Titlis Cable Car, Glacier Express Train, Skiing.</div>
                    <div class="detail-row"><i>🛫</i> <strong>Departure:</strong> High-speed rail to Zurich Station.</div>
                    
                   <a href="package_details.php?pkg=Swiss%20Alps" style="text-decoration: none;">
            <button class="book-btn">Enquire Now</button>
        </a>
                </div>
            </div>

            <div class="pkg-card">
                <img src="thailand.webp" class="pkg-img">
                <div class="pkg-content">
                    <span class="info-badge">6 DAYS / 5 NIGHTS</span>
                    <h3>Tropical Thailand Bliss</h3>
                    
                    <div class="detail-row"><i>✈️</i> <strong>Arrival:</strong> Bangkok Suvarnabhumi (BKK)</div>
                    <div class="detail-row"><i>🏨</i> <strong>Stay:</strong> Marriott Resort Phuket</div>
                    <div class="detail-row"><i>🏃</i> <strong>Activities:</strong> Phi Phi Island Tour, Floating Market, Elephant Sanctuary.</div>
                    <div class="detail-row"><i>🛫</i> <strong>Departure:</strong> Domestic flight to BKK + International out.</div>
                    
                   <a href="package_details.php?pkg=Thailand" style="text-decoration: none;">
            <button class="book-btn">Enquire Now</button>
        </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>