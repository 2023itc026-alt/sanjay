<?php
session_start();
require_once 'db_config.php';

$pkg_name = isset($_GET['pkg']) ? $_GET['pkg'] : '';

$stmt = $conn->prepare("SELECT * FROM trip_packages WHERE name = ?");
$stmt->bind_param("s", $pkg_name);
$stmt->execute();
$package = $stmt->get_result()->fetch_assoc();

if (!$package) { die("Package not found."); }

$it_stmt = $conn->prepare("SELECT * FROM package_itineraries WHERE package_id = ? ORDER BY day_number ASC");
$it_stmt->bind_param("i", $package['id']);
$it_stmt->execute();
$itinerary_days = $it_stmt->get_result();

$is_unlocked = isset($_GET['unlocked']) && $_GET['unlocked'] == 'true';
$members = isset($_GET['members']) ? intval($_GET['members']) : 1;
$total_price = $package['price'] * $members;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Itinerary - <?php echo htmlspecialchars($package['name']); ?></title>
    <style>
        :root { --primary: #f39c12; --bg-glass: rgba(255,255,255,0.05); --success: #2ecc71; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { 
            background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('images/bg.jpg');
            background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; color: white;
        }
        .itinerary-grid { display: grid; grid-template-columns: 1fr 380px; gap: 40px; padding: 40px 8%; max-width: 1500px; margin: auto; }
        .timeline-thread { position: relative; border-left: 2px dashed rgba(243, 156, 18, 0.4); padding-left: 50px; margin-left: 20px; }
        .day-card { background: var(--bg-glass); backdrop-filter: blur(15px); border-radius: 20px; padding: 30px; margin-bottom: 40px; border: 1px solid rgba(255, 255, 255, 0.1); position: relative; }
        .day-card::before { content: ""; position: absolute; left: -61px; top: 30px; width: 20px; height: 20px; background: var(--primary); border-radius: 50%; box-shadow: 0 0 15px var(--primary); }
        .day-tag { color: var(--primary); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; }
        .time-slots { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px; }
        .slot { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); }
        .slot strong { display: block; color: var(--primary); font-size: 0.7rem; margin-bottom: 5px; }
        .route-sidebar { position: sticky; top: 40px; height: fit-content; }
        .route-box { background: white; color: #1a1a1a; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .route-step { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 25px; position: relative; }
        .route-step:not(:last-child)::after { content: ""; position: absolute; left: 7px; top: 22px; height: 30px; border-left: 2px solid #eee; }
        .step-dot { width: 15px; height: 15px; border-radius: 50%; border: 2px solid var(--primary); background: white; margin-top: 4px; }
        .book-btn { background: var(--primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; margin-top: 10px; transition: 0.3s; text-align: center; display: block; text-decoration: none; }
        .book-btn:hover { background: #e67e22; transform: translateY(-2px); }
        .modal-overlay { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.85); z-index: 2000; backdrop-filter: blur(10px); }
        .modal-content { background: white; color: #333; width: 450px; margin: 80px auto; padding: 40px; border-radius: 25px; position: relative; text-align: center; }
        .modal-step { display: none; }
        .modal-step.active { display: block; }
        .modal-content input { width: 100%; padding: 12px; margin: 10px 0 20px; border: 1px solid #ddd; border-radius: 8px; }
        .option-group { display: flex; gap: 10px; margin-bottom: 20px; }
        .opt-btn { flex: 1; padding: 10px; border: 2px solid var(--primary); background: none; border-radius: 10px; cursor: pointer; font-weight: bold; }
        .opt-btn.selected { background: var(--primary); color: white; }
        .calc-box { background: var(--bg-glass); border-radius: 20px; padding: 25px; margin-top: 25px; border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body>
    <div style="padding: 40px 8% 0;">
        <a href="packages.php" style="color: var(--primary); text-decoration: none; font-weight:bold;">← BACK TO PACKAGES</a>
        <h1 style="margin-top: 15px; font-size: 2.2rem;">Day-by-Day Itinerary: <?php echo htmlspecialchars($package['name']); ?></h1>
    </div>

    <div class="itinerary-grid">
        <div class="timeline-thread">
            <?php while($day = $itinerary_days->fetch_assoc()): ?>
                <div class="day-card">
                    <span class="day-tag">Day <?php echo $day['day_number']; ?></span>
                    <h2 style="margin: 5px 0 15px;">Daily Exploration</h2>
                    <div class="time-slots">
                        <div class="slot"><strong>MORNING</strong><?php echo htmlspecialchars($day['morning_act']); ?></div>
                        <div class="slot"><strong>AFTERNOON</strong><?php echo htmlspecialchars($day['afternoon_act']); ?></div>
                    </div>
                    <div class="slot" style="margin-top:15px;"><strong>EVENING / STAY</strong><?php echo htmlspecialchars($day['evening_act']); ?></div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="route-sidebar">
            <div class="route-box">
                <h3 style="margin-bottom: 25px; font-weight: bold;">Traveling Route</h3>
                <div class="route-step"><div class="step-dot"></div><div><div style="font-weight: bold; font-size: 0.9rem;"><?php echo htmlspecialchars($package['arrival']); ?></div><div style="font-size: 0.75rem; color: #777;">Start Point</div></div></div>
                <div class="route-step"><div class="step-dot"></div><div><div style="font-weight: bold; font-size: 0.9rem;"><?php echo htmlspecialchars($package['stay']); ?></div><div style="font-size: 0.75rem; color: #777;">Primary Stay</div></div></div>
                <div class="route-step"><div class="step-dot"></div><div><div style="font-weight: bold; font-size: 0.9rem;"><?php echo htmlspecialchars($package['departure']); ?></div><div style="font-size: 0.75rem; color: #777;">End Point</div></div></div>

                <?php if(!$is_unlocked): ?>
                    <button class="book-btn" onclick="openModal('unlockModal')">Unlock Full Itinerary</button>
                <?php else: ?>
                    <button class="book-btn" style="background: var(--success);" onclick="openModal('paymentModal')">Pay $<?php echo number_format($total_price, 2); ?> Now</button>
                <?php endif; ?>
            </div>

            <?php if($is_unlocked): ?>
                <div class="calc-box">
                    <h3 style="color: var(--primary); margin-bottom: 15px;">Booking Details</h3>
                    <p style="font-size: 0.9rem; opacity: 0.8;">Departure: <strong><?php echo htmlspecialchars($_GET['city'] ?? ''); ?></strong></p>
                    <p style="font-size: 0.9rem; opacity: 0.8; margin-bottom: 15px;">Travelers: <strong><?php echo $members; ?></strong></p>
                    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;"><h4 style="color: var(--success);">Total: $<?php echo number_format($total_price, 2); ?></h4></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="paymentModal" class="modal-overlay">
        <div class="modal-content">
            <span style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;" onclick="closeModal('paymentModal')">&times;</span>
            <h3 style="color: var(--success);">Scan to Confirm Payment</h3>
            <p style="margin-bottom: 20px;">Total Due: <strong>$<?php echo number_format($total_price, 2); ?></strong></p>
            
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=Payment+Completed+for+<?php echo urlencode($pkg_name); ?>.+Total+Paid:+$<?php echo number_format($total_price, 2); ?>.+Agancy:+Odyssey+Travels" 
                 style="border: 8px solid #f9f9f9; border-radius: 15px; margin-bottom: 15px;">
            
            <p style="font-size: 0.8rem; color: #777;">Scanning this will show the "Payment Completed" text on your mobile phone screen.</p>
            
            <a href="generate_pdf.php?pkg=<?php echo urlencode($pkg_name); ?>&total=<?php echo $total_price; ?>" class="book-btn" style="background: #34495e; margin-top: 20px;">Download Paid Itinerary (PDF)</a>
        </div>
    </div>

    <div id="unlockModal" class="modal-overlay">
        <div class="modal-content">
            <span style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;" onclick="closeModal('unlockModal')">&times;</span>
            <form action="" method="GET">
                <input type="hidden" name="pkg" value="<?php echo htmlspecialchars($pkg_name); ?>">
                <input type="hidden" name="unlocked" value="true">
                <div id="step1" class="modal-step active">
                    <h3>Departure Details</h3>
                    <input type="text" name="city" placeholder="Departure City" required>
                    <input type="date" name="date" required>
                    <button type="button" class="book-btn" onclick="goToStep(2)">Next</button>
                </div>
                <div id="step2" class="modal-step">
                    <h3>Travel Mode</h3>
                    <div class="option-group">
                        <button type="button" class="opt-btn" id="soloBtn" onclick="selectMode('solo')">Solo</button>
                        <button type="button" class="opt-btn" id="groupBtn" onclick="selectMode('group')">Group</button>
                    </div>
                    <div id="groupInput" style="display:none;"><input type="number" name="members" id="membersCount" value="1" min="1"></div>
                    <button type="submit" class="book-btn">Confirm & Unlock</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'block'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        function goToStep(step) { document.querySelectorAll('.modal-step').forEach(s => s.classList.remove('active')); document.getElementById('step' + step).classList.add('active'); }
        function selectMode(mode) { 
            document.getElementById('soloBtn').classList.toggle('selected', mode === 'solo'); 
            document.getElementById('groupBtn').classList.toggle('selected', mode === 'group'); 
            document.getElementById('groupInput').style.display = (mode === 'group') ? 'block' : 'none';
            if(mode === 'solo') document.getElementById('membersCount').value = 1;
        }
    </script>
</body>
</html>