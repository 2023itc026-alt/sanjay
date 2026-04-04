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

// 3. Fetch Trip Data with Vehicle/Guide Info
$stmt = $conn->prepare("SELECT t.*, v.car_model, v.price_per_day as v_price FROM trips t 
                        LEFT JOIN vehicles v ON t.vehicle_id = v.id 
                        WHERE t.id = ?");
$stmt->bind_param("i", $trip_id);
$stmt->execute();
$trip = $stmt->get_result()->fetch_assoc();

if (!$trip) {
    die("Error: Trip not found for ID: " . $trip_id);
}

// Store vehicle price for JavaScript access
$vehicle_cost = isset($trip['v_price']) ? floatval($trip['v_price']) : 0;
$vehicle_name = isset($trip['car_model']) ? $trip['car_model'] : "None Selected";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Trip to <?php echo htmlspecialchars($trip['destination']); ?></title>
    <style>
        /* --- STABILIZED GLASS STYLES --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/bg.jpg');
            background-size: cover; background-position: center; background-attachment: fixed; color: white;
        }

        .planner-layout { display: grid; grid-template-columns: 350px 1fr 350px; gap: 20px; padding: 30px; align-items: start; }
        
        .column { 
            background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); 
            border-radius: 20px; padding: 25px; border: 1px solid rgba(255,255,255,0.1); 
        }

        input[type="date"] {
            width: 100%; padding: 12px; border-radius: 10px; margin-top: 5px;
            background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);
            color: white; outline: none;
        }

        .save-btn {
            display: inline-block; width: 100%; padding: 15px; background: #f39c12;
            color: white; border: none; border-radius: 30px; font-weight: bold;
            text-transform: uppercase; cursor: pointer; transition: 0.3s;
            margin-top: 20px; text-align: center; text-decoration: none;
        }

        .itinerary-item { 
            background: rgba(0,0,0,0.3); margin-bottom: 10px; padding: 12px; 
            border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); 
            display: flex; justify-content: space-between; align-items: center;
        }

        .bill-box { background: rgba(0,0,0,0.4); border-radius: 15px; padding: 15px; margin-top: 15px; border: 1px solid rgba(255,255,255,0.1); }
        .bill-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 5px; }

        /* MODAL STYLES */
        .modal-overlay { 
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.85); z-index: 2000; backdrop-filter: blur(10px); 
        }
        .modal-content { 
            background: white; color: #333; width: 450px; margin: 80px auto; 
            padding: 40px; border-radius: 25px; position: relative; text-align: center;
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
            </div>
        </div>

        <div class="column" style="text-align: center;">
            <h2>Modify Your Journey</h2>
            <form action="process.php" method="POST" id="editForm" style="text-align: left; margin-top: 20px;">
                <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div><label>Travel Date:</label><input type="date" name="travel_date" value="<?php echo $trip['travel_date']; ?>" required></div>
                    <div><label>Departure Date:</label><input type="date" name="departure_date" value="<?php echo $trip['departure_date']; ?>" required></div>
                </div>

                <div id="itineraryContainer" style="margin-top:20px; max-height: 350px; overflow-y: auto;">
                    <?php 
                    $places = array_filter(preg_split('/[,\n]+/', $trip['itinerary']));
                    foreach($places as $place): $place = trim($place);
                    ?>
                    <div class="itinerary-item">
                        <span>📍 <?php echo htmlspecialchars($place); ?></span>
                        <input type="hidden" name="itinerary_items[]" class="itin-input" value="<?php echo htmlspecialchars($place); ?>">
                        <button type="button" onclick="removeItem(this)" style="background:none; border:none; color:#ff4d4d; cursor:pointer;">✕</button>
                    </div>
                    <?php endforeach; ?>
                </div>

                <a href="select_places.php?destination=<?php echo urlencode($trip['destination']); ?>&edit_id=<?php echo $trip_id; ?>" style="color: #f39c12; text-decoration: none; font-weight: bold; display: block; margin-top: 15px; text-align: center;">+ Add More Places</a>
                <a href="vehicles_guides.php?location=<?php echo urlencode($trip['destination']); ?>&trip_id=<?php echo $trip_id; ?>&context=trip" style="display:block; text-align:center; margin-top:10px; color:#aaa; text-decoration:none; font-size:0.9rem;">🚗 Change Vehicle & Guide</a>
                
                <button type="submit" name="update_trip" class="save-btn">Save All Changes</button>
            </form>
        </div>

        <div class="column">
            <h3>Cost Analysis</h3>
            <div class="bill-box">
                <div id="liveBillItems"></div>
                <div id="vehicleBillRow" style="display: none;">
                    <div class="bill-row" style="color: #2ecc71; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 5px;">
                        <span><?php echo $vehicle_name; ?></span>
                        <span id="vPriceDisplay">$<?php echo number_format($vehicle_cost, 2); ?></span>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: bold; color: #f39c12; margin-top: 10px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px; font-size: 1.1rem;">
                    <span>Total:</span><span id="liveTotalDisplay">$0.00</span>
                </div>
            </div>

            <button class="save-btn" style="background: #27ae60; margin-top: 25px;" onclick="openPaymentModal()">
                Pay Now & Confirm
            </button>
        </div>
    </div>

    <div id="paymentModal" class="modal-overlay">
        <div class="modal-content">
            <span style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;" onclick="closePaymentModal()">&times;</span>
            <h3 style="color: #27ae60;">Scan to Confirm Trip</h3>
            <p style="margin-bottom: 20px;">Total Amount: <strong id="modalTotal">$0.00</strong></p>
            
            <img id="paymentQR" src="" style="border: 8px solid #f9f9f9; border-radius: 15px; margin-bottom: 15px;">
            
            <p style="font-size: 0.8rem; color: #777;">Scanning confirms payment for Trip #<?php echo $trip_id; ?>.</p>
            
            <a href="" id="pdfLink" class="save-btn" style="background: #34495e; margin-top: 20px;">Download Paid Receipt (PDF)</a>
        </div>
    </div>

    <script>
    let globalTotal = 0;

    async function updateLiveBill() {
        const inputs = document.querySelectorAll('.itin-input');
        const billContainer = document.getElementById('liveBillItems');
        const totalDisplay = document.getElementById('liveTotalDisplay');
        const vehicleRow = document.getElementById('vehicleBillRow');
        
        const vehiclePrice = <?php echo $vehicle_cost; ?>;
        globalTotal = vehiclePrice;
        let html = "";

        if(vehiclePrice > 0) vehicleRow.style.display = 'block';

        for (let input of inputs) {
            const placeName = input.value;
            try {
                const response = await fetch(`get_place_info.php?name=${encodeURIComponent(placeName)}`);
                const data = await response.json();
                if (data.success) {
                    const price = parseFloat(data.price || 0);
                    globalTotal += price;
                    html += `<div class="bill-row"><span>${data.name}</span><span>$${price.toFixed(2)}</span></div>`;
                }
            } catch (e) { console.error(e); }
        }

        billContainer.innerHTML = html || '<p style="font-size:12px; opacity:0.5;">No places selected.</p>';
        totalDisplay.innerText = `$${globalTotal.toFixed(2)}`;
    }

    function openPaymentModal() {
        document.getElementById('paymentModal').style.display = 'block';
        document.getElementById('modalTotal').innerText = `$${globalTotal.toFixed(2)}`;
        
        // Generate QR and PDF link
        const qrText = `Payment Completed for Trip #<?php echo $trip_id; ?>. Total: $${globalTotal.toFixed(2)}`;
        document.getElementById('paymentQR').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrText)}`;
        document.getElementById('pdfLink').href = `generate_pdf.php?pkg=Custom_Trip_<?php echo urlencode($trip['destination']); ?>&total=${globalTotal}&trip_id=<?php echo $trip_id; ?>`;
    }

    function closePaymentModal() { document.getElementById('paymentModal').style.display = 'none'; }
    function removeItem(btn) { btn.parentElement.remove(); updateLiveBill(); }

    document.addEventListener('DOMContentLoaded', updateLiveBill);
    </script>
</body>
</html>