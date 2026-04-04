<?php
session_start();
require_once 'db_config.php';

// 1. Capture Parameters for Filtered Flow
$selected_location = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : null;
$selected_vehicle = isset($_GET['vehicle_id']) ? intval($_GET['vehicle_id']) : null;
$selected_guide = isset($_GET['guide_id']) ? intval($_GET['guide_id']) : null;

// Context helps distinguish between a Full Trip (trip) and just a Cab (cab)
$context = isset($_GET['context']) ? htmlspecialchars($_GET['context']) : 'trip';
$trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
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
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/bg.jpg');
            background-size: cover; background-position: center; background-attachment: fixed; 
            min-height: 100vh; color: white; padding: 50px 5%;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        
        /* --- DYNAMIC HOVER EFFECTS --- */
        .glass {
            position: relative; background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 25px;
            overflow: hidden; cursor: pointer; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .glass:hover {
            transform: translateY(-10px) scale(1.02);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(243, 156, 18, 0.5);
            box-shadow: 0 15px 35px rgba(243, 156, 18, 0.2);
        }
        .glass::before {
            content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(10px); z-index: -1;
        }
        
        .badge { background: #f39c12; padding: 4px 10px; border-radius: 5px; font-size: 10px; font-weight: bold; }
        .price-tag { color: #2ecc71; font-weight: bold; font-size: 1.1rem; }
        .detail-item { display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 10px 0; }
        .label { color: #f39c12; font-weight: bold; font-size: 0.9rem; }
        .back-link { color: #f39c12; text-decoration: none; font-weight: bold; display: block; margin-bottom: 20px; transition: 0.3s; }
        .back-link:hover { color: #e67e22; transform: translateX(-5px); }

        /* --- DESTINATION GRID HOVER --- */
        .destination-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 30px; }
        .dest-card { position: relative; height: 250px; border-radius: 20px; overflow: hidden; cursor: pointer; border: 1px solid rgba(255, 255, 255, 0.1); transition: 0.4s ease; }
        .dest-card img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
        .dest-card:hover img { transform: scale(1.1); filter: brightness(1.1); }
        .dest-card:hover { border-color: #f39c12; box-shadow: 0 0 20px rgba(243, 156, 18, 0.3); }
        .dest-name-overlay { position: absolute; bottom: 0; width: 100%; padding: 20px; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); text-align: center; }

        .confirm-btn {
            display: inline-block; width: 100%; padding: 15px;
            background: #f39c12; color: white; border: none;
            border-radius: 30px; font-weight: bold; text-transform: uppercase;
            cursor: pointer; transition: 0.3s; text-align: center; text-decoration: none; margin-top: 20px;
        }
        .confirm-btn:hover { background: #e67e22; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(230, 126, 34, 0.4); }

        .modal-overlay { 
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.85); z-index: 1000; backdrop-filter: blur(10px); 
        }
        .modal-content { 
            background: white; color: #333; width: 450px; margin: 80px auto; 
            padding: 40px; border-radius: 25px; position: relative; text-align: center;
        }
        .modal-content input { width: 100%; padding: 12px; margin: 10px 0 20px; border: 1px solid #ddd; border-radius: 8px; }
        .modal-step { display: none; }
        .modal-step.active { display: block; }
        .close-btn { position: absolute; right: 20px; top: 15px; cursor: pointer; font-size: 24px; color: #999; }
    </style>
</head>
<body>

<div class="container">
    
    <?php if (!$selected_location): ?>
        <h1 style="text-align:center; margin-bottom:10px;">Select Your Destination</h1>
        <div class="destination-grid">
            <?php 
            $dests = ['Dubai' => 'dubai.jpg', 'Paris' => 'paris.jpg', 'Tokyo' => 'tokyo.jpg', 'Malaysia' => 'malaysia.jpg', 'Goa' => 'goa.avif', 'Manali' => 'manali.jpg'];
            foreach($dests as $name => $img): ?>
             <div class="dest-card" onclick="location.href='?location=<?php echo $name; ?>&context=<?php echo $context; ?>&trip_id=<?php echo $trip_id; ?>'">
                    <img src="images/<?php echo $img; ?>" onerror="this.src='images/bg.jpg'">
                    <div class="dest-name-overlay"><h2><?php echo $name; ?></h2></div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif ($selected_location && !$selected_vehicle): ?>
        <a href="vehicles_guides.php?context=<?php echo $context; ?>&trip_id=<?php echo $trip_id; ?>" class="back-link">← Change Destination</a>
        <h1 style="text-align:center;">Premium Fleet in <?php echo $selected_location; ?></h1>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top:40px;">
            <?php
            $v_stmt = $conn->prepare("SELECT * FROM vehicles WHERE location = ?");
            $v_stmt->bind_param("s", $selected_location);
            $v_stmt->execute();
            $vehicles = $v_stmt->get_result();
            while($v = $vehicles->fetch_assoc()): 
            ?>
                <div class="glass" style="padding: 0;" onclick="location.href='?location=<?php echo $selected_location; ?>&vehicle_id=<?php echo $v['id']; ?>&context=<?php echo $context; ?>&trip_id=<?php echo $trip_id; ?>'">
                    <img src="images/<?php echo $v['image']; ?>" style="width:100%; height:200px; object-fit:cover;" onerror="this.src='images/toyota.jpg'">
                    <div style="padding:20px;">
                        <h3 style="margin-bottom:10px;"><?php echo htmlspecialchars($v['car_model']); ?></h3>
                        <p class="price-tag">$<?php echo number_format($v['price_per_day'], 2); ?> / day</p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php elseif ($selected_vehicle && !$selected_guide): ?>
        <a href="?location=<?php echo $selected_location; ?>&context=<?php echo $context; ?>&trip_id=<?php echo $trip_id; ?>" class="back-link">← Back to Fleet</a>
        <h1 style="text-align:center;">Select Your Expert Guide</h1>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top:40px;">
            <?php
            $g_stmt = $conn->prepare("SELECT * FROM guides WHERE vehicle_id = ?");
            $g_stmt->bind_param("i", $selected_vehicle);
            $g_stmt->execute();
            $guides = $g_stmt->get_result();
            while($g = $guides->fetch_assoc()): 
            ?>
               <div class="glass" style="text-align:center; padding:30px;" onclick="location.href='?location=<?php echo $selected_location; ?>&vehicle_id=<?php echo $selected_vehicle; ?>&guide_id=<?php echo $g['id']; ?>&context=<?php echo $context; ?>&trip_id=<?php echo $trip_id; ?>'">
                    <img src="images/<?php echo $g['picture']; ?>" style="width:120px; height:120px; border-radius:50%; border:3px solid #f39c12; object-fit:cover; margin-bottom: 15px;" onerror="this.src='images/kavin.jpg'">
                    <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($g['name']); ?></h3>
                    <p style="opacity:0.7; font-size: 0.9rem;"><?php echo $g['experience']; ?> Experience</p>
                </div>
            <?php endwhile; ?>
        </div>

  <?php elseif ($selected_guide): ?>
        <?php 
            // 1. Fetch Guide and Vehicle Details
            $stmt = $conn->prepare("SELECT g.*, v.price_per_day, v.car_model, v.driver_name 
                                    FROM guides g 
                                    JOIN vehicles v ON g.vehicle_id = v.id 
                                    WHERE g.id = ?");
            $stmt->bind_param("i", $selected_guide);
            $stmt->execute();
            $g = $stmt->get_result()->fetch_assoc();
            
            // Handle profile picture fallback
            $pic = (!empty($g['picture']) && $g['picture'] !== 'default-guide.png') ? $g['picture'] : "kavin.jpg";
        ?>
        
        <div style="display:flex; flex-direction:column; align-items:center;">
            <a href="?location=<?php echo $selected_location; ?>&vehicle_id=<?php echo $selected_vehicle; ?>&context=<?php echo $context; ?>&trip_id=<?php echo $trip_id; ?>" class="back-link">← Back to Guides</a>
            
            <div class="glass" style="display:flex; max-width:850px; width:100%; padding:40px; gap:40px; align-items:center; cursor: default;">
                <img src="images/<?php echo $pic; ?>" style="min-width:220px; height:220px; border-radius:20px; object-fit:cover;" onerror="this.src='images/kavin.jpg'">
                
                <div style="flex:1;">
                    <span class="badge" style="background:#27ae60;">CERTIFIED EXPERT</span>
                    <h2 style="font-size:2.5rem; margin:10px 0;"><?php echo htmlspecialchars($g['name']); ?></h2>
                    
                    <div class="detail-item"><span class="label">Vehicle:</span><span><?php echo htmlspecialchars($g['car_model']); ?></span></div>
                    <div class="detail-item"><span class="label">Driver:</span><span><?php echo htmlspecialchars($g['driver_name']); ?></span></div>
                    <div class="detail-item"><span class="label">Experience:</span><span><?php echo $g['experience']; ?></span></div>

                    <?php if ($context === 'trip'): ?>
                        <div class="detail-item" style="background: rgba(243, 156, 18, 0.1); padding: 10px; border-radius: 10px; margin-top: 10px;">
                            <span class="label">Linked Trip:</span>
                            <span>#<?php echo $trip_id; ?></span>
                        </div>
                        <form action="process.php" method="GET">
                            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                            <input type="hidden" name="vehicle_id" value="<?php echo $selected_vehicle; ?>">
                            <input type="hidden" name="guide_id" value="<?php echo $selected_guide; ?>">
                            <button type="submit" class="confirm-btn">CONFIRM FOR MY TRIP</button>
                        </form>
                    <?php else: ?>
                        <div class="detail-item"><span class="label">Daily Rate:</span><span class="price-tag">$<?php echo number_format($g['price_per_day'], 2); ?></span></div>
                        <button class="confirm-btn" onclick="openBookingModal()">CONFIRM & BOOK CAB</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="bookingModal" class="modal-overlay">
            <div class="modal-content">
                <span class="close-btn" onclick="closeBookingModal()">&times;</span>
                
                <div id="step1" class="modal-step active">
                    <h3>Rental Period</h3>
                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">Select dates for your cab service.</p>
                    
                    <label>Start Date</label>
                    <input type="date" id="start_date" onchange="calculateCost()">
                    <label>End Date</label>
                    <input type="date" id="end_date" onchange="calculateCost()">
                    
                    <div id="priceDisplay" style="margin:20px 0; font-weight:bold; color:#2ecc71; font-size:1.2rem;">Total: $0.00</div>
                    
                    <button id="payBtn" class="confirm-btn" style="display:none;" onclick="showPaymentStep()">
                        Pay Total Amount
                    </button>
                </div>

                <div id="step2" class="modal-step">
                    <h3 style="color:#2ecc71;">Scan to Pay</h3>
                    <img id="qr_code" src="" style="border: 8px solid #f9f9f9; border-radius: 15px; margin: 15px 0;">
                    
                    <p style="font-size:0.8rem; color:#777;">
                        Booking: <strong><?php echo htmlspecialchars($g['car_model']); ?></strong> with <strong><?php echo htmlspecialchars($g['name']); ?></strong>
                    </p>
                    
                    <a href="" id="pdfLink" class="confirm-btn" style="background:#34495e; margin-top: 20px;">Download Paid Receipt (PDF)</a>
                </div>
            </div>
        </div>

        <script>
            const dayRate = <?php echo $g['price_per_day']; ?>;

            function openBookingModal() { document.getElementById('bookingModal').style.display = 'block'; }
            function closeBookingModal() { document.getElementById('bookingModal').style.display = 'none'; }

            function calculateCost() {
                const s = new Date(document.getElementById('start_date').value);
                const e = new Date(document.getElementById('end_date').value);
                
                if (s && e && e > s) {
                    const diff = Math.ceil((e - s) / (1000 * 60 * 60 * 24));
                    const total = diff * dayRate;
                    
                    document.getElementById('priceDisplay').innerText = `Total Amount: $${total.toFixed(2)}`;
                    document.getElementById('payBtn').innerText = `Pay $${total.toFixed(2)} Now`;
                    document.getElementById('payBtn').style.display = 'block';
                    
                    // QR Text and PDF Link
                    const qrText = `Payment Completed for <?php echo $g['name']; ?>. Total: $${total}`;
                    document.getElementById('qr_code').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrText)}`;
                    document.getElementById('pdfLink').href = `generate_pdf.php?pkg=Cab_<?php echo urlencode($g['name']); ?>&total=${total}`;
                }
            }

            function showPaymentStep() {
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step2').classList.add('active');
            }
        </script>
    <?php endif; ?>
</div>

</body>
</html>