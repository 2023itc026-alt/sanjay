<?php
session_start();
require_once 'db_config.php'; 

// 1. Check Authentication
if (!isset($_SESSION['user'])) { 
    header("Location: index.php"); 
    exit(); 
}

// 2. Capture parameters
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : null;
$destination = isset($_GET['place']) ? htmlspecialchars($_GET['place']) : "Dubai";

// 3. Fetch dynamic places with Hybrid Protection
$stmt = $conn->prepare("SELECT name, cat, description as 'desc', image as 'img', price FROM explore_places WHERE target_destination = ?");
$stmt->bind_param("s", $destination);
$stmt->execute();
$result = $stmt->get_result();

$combined_places = [];
while($row = $result->fetch_assoc()) {
    // Hybrid Image Logic: Ensures dynamic images have the correct path
    $row['img'] = "images/" . $row['img']; 
    $combined_places[] = $row;
}

// 4. Static Items with Prices
$static_items = [];
if ($destination === "Dubai") {
    $static_items = [
       
               
    ];
} elseif ($destination === "Tokyo") {
    $static_items = [
       
    ];
}

$final_list = array_merge($combined_places, $static_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plan Trip to <?php echo $destination; ?></title>
    <style>
        /* --- LAYOUT & GLASS STYLES --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('bg.jpg');
            background-size: cover; background-position: center; background-attachment: fixed;
            min-height: 100vh; color: white;
        }

        .planner-layout { 
            display: grid; grid-template-columns: 350px 1fr 350px; 
            gap: 20px; padding: 30px; align-items: start;
        }

        .column { 
            background: rgba(255,255,255,0.1); backdrop-filter: blur(15px);
            border-radius: 20px; padding: 25px; border: 1px solid rgba(255,255,255,0.1); 
            transform: translateZ(0); /* Flicker Prevention */
        }
        
        /* --- UI COMPONENTS --- */
        .search-box { width: 100%; padding: 12px; border-radius: 25px; border: none; margin-bottom: 15px; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.1); }
        .cat-btn { background: none; border: 1px solid #f39c12; color: white; padding: 6px 12px; border-radius: 15px; font-size: 12px; cursor: pointer; margin: 2px; transition: 0.3s; }
        .cat-btn.active { background: #f39c12; }

        .place-card { background: rgba(0,0,0,0.3); padding: 15px; border-radius: 15px; margin-bottom: 15px; transition: 0.3s; border: 1px solid transparent; }
        .place-card:hover { border: 1px solid #f39c12; transform: translateY(-5px); }
        .place-card img { width: 100%; height: 120px; object-fit: cover; border-radius: 10px; background: #111; }
        
        .add-btn { background: #f39c12; border: none; color: white; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; float: right; font-weight: bold; }
        .itinerary-item { background: rgba(255,255,255,0.05); padding: 12px; border-radius: 10px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #f39c12; }

        .save-trip-btn {
            background-color: #f39c12; color: white; border: none;
            padding: 15px; width: 100%; border-radius: 30px; font-weight: bold;
            text-transform: uppercase; cursor: pointer; transition: 0.3s;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3); margin-top: 15px;
        }
        .save-trip-btn:hover { background-color: #e67e22; transform: translateY(-3px); }

        .bill-box { background: rgba(0,0,0,0.3); border-radius: 15px; padding: 15px; margin: 20px 0; border: 1px solid rgba(255,255,255,0.1); }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; color: #f39c12; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px; }
    </style>
</head>
<body>
    <div class="planner-layout">
        <div class="column">
            <h3>Explore <?php echo $destination; ?></h3>
            <input type="text" id="searchInput" class="search-box" placeholder="Search places..." onkeyup="filterPlaces()">
            
            <div class="category-filters" style="margin-bottom: 20px;">
                <button class="cat-btn active" onclick="filterCat('all', this)">All</button>
                <button class="cat-btn" onclick="filterCat('Hotel', this)">Hotels</button>
                <button class="cat-btn" onclick="filterCat('Restaurant', this)">Food</button>
                <button class="cat-btn" onclick="filterCat('Museum', this)">Culture</button>
            </div>
            <div id="suggestionsBox"></div>
        </div>

        <div class="column" style="text-align: center; min-height: 500px;">
            <div id="placeDetailsView">
                <h2>Trip to <?php echo $destination; ?></h2>
                <p>Customize your dream vacation</p>
                <hr style="opacity: 0.2; margin: 20px 0;">
                <div id="itineraryBox">
                    <p style="opacity: 0.5;">No items added yet.</p>
                </div>
            </div>
        </div>

        <div class="column">
            <h3>Final Plan</h3>
          <form action="process.php" method="POST" style="margin-top:15px;">
    <input type="hidden" name="destination" value="<?php echo $destination; ?>">
    
    <label style="font-size:12px; opacity:0.8;">Arrival Date:</label>
    <input type="date" name="travel_date" class="search-box" required>
    
    <label style="font-size:12px; opacity:0.8;">Departure Date:</label>
    <input type="date" name="departure_date" class="search-box" required>

    <div class="bill-box">
        <h4 style="color:#f39c12; margin-bottom:10px;">Cost Summary</h4>
        <div id="billItemsList"><p style="font-size:12px; opacity:0.5;">Select places to calculate.</p></div>
        <div class="total-row">
            <span>Total:</span><span id="totalDisplay">$0.00</span>
        </div>
    </div>

    <textarea name="itinerary" id="hiddenData" style="display:none;"></textarea>
    <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
    
    <button type="submit" name="save_trip" class="save-trip-btn">Save Trip </button>
</form>
        </div>
    </div>

    <script>
    const places = <?php echo json_encode($final_list); ?>;
    let myPlan = [];

    function displayPlaces(data) {
        const box = document.getElementById('suggestionsBox');
        box.innerHTML = data.map(p => `
            <div class="place-card">
                <img src="${p.img}" onerror="this.src='images/default-place.jpg'" onclick="showDetails('${p.name}')" style="cursor:pointer;">
                <div style="margin-top:10px;">
                    <button class="add-btn" onclick="addToPlan('${p.name}')">+</button>
                    <h4 onclick="showDetails('${p.name}')" style="cursor:pointer;">${p.name}</h4>
                    <small style="color:#f39c12;">$${parseFloat(p.price || 0).toFixed(2)}</small>
                </div>
            </div>
        `).join('');
    }

    function showDetails(name) {
        const p = places.find(item => item.name === name);
        const view = document.getElementById('placeDetailsView');
        view.innerHTML = `
            <div style="text-align: left; animation: fadeIn 0.4s;">
                <button onclick="resetView()" style="background:none; border:none; color:#f39c12; cursor:pointer; font-weight:bold;">← Back to Summary</button>
                <img src="${p.img}" onerror="this.src='images/default-place.jpg'" style="width:100%; border-radius:15px; margin:15px 0; height:250px; object-fit:cover;">
                <h2 style="color:#f39c12;">${p.name}</h2>
                <p style="margin:15px 0; line-height:1.6; color:#ddd;">${p.desc}</p>
                <button class="save-trip-btn" onclick="addToPlan('${p.name}')" style="margin-top:0;">Add to Itinerary</button>
                <hr style="opacity: 0.1; margin: 25px 0;">
                <div id="itineraryBox"></div>
            </div>
        `;
        updateUI(); 
    }

    function resetView() {
        document.getElementById('placeDetailsView').innerHTML = `<h2>Trip to <?php echo $destination; ?></h2><hr style="opacity:0.2; margin:20px 0;"><div id="itineraryBox"></div>`;
        updateUI();
    }

    function addToPlan(name) {
        const p = places.find(item => item.name === name);
        myPlan.push({ name: p.name, price: parseFloat(p.price || 0) });
        updateUI();
    }

    function removeFromPlan(index) {
        myPlan.splice(index, 1);
        updateUI();
    }

    function updateUI() {
        const itineraryBox = document.getElementById('itineraryBox');
        const billBox = document.getElementById('billItemsList');
        const totalDisplay = document.getElementById('totalDisplay');
        const totalInput = document.getElementById('totalAmountInput');
        const hiddenData = document.getElementById('hiddenData');

        let total = 0;

        if (itineraryBox) {
            itineraryBox.innerHTML = myPlan.length === 0 ? '<p style="opacity:0.5;">No items added yet.</p>' : 
                myPlan.map((item, idx) => `
                    <div class="itinerary-item">
                        <span>${item.name}</span>
                        <button onclick="removeFromPlan(${idx})" style="background:none; border:none; color:#ff4d4d; cursor:pointer;">✕</button>
                    </div>
                `).join('');
        }

        billBox.innerHTML = myPlan.map(item => {
            total += item.price;
            return `<div class="bill-item"><span>${item.name}</span><span>$${item.price.toFixed(2)}</span></div>`;
        }).join('');

        totalDisplay.innerText = `$${total.toFixed(2)}`;
        totalInput.value = total.toFixed(2);
        hiddenData.value = myPlan.map(p => p.name).join(', ');
    }

    function filterPlaces() {
        const val = document.getElementById('searchInput').value.toLowerCase();
        displayPlaces(places.filter(p => p.name.toLowerCase().includes(val)));
    }

    function filterCat(cat, btn) {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        displayPlaces(cat === 'all' ? places : places.filter(p => p.cat === cat));
    }

    displayPlaces(places);
    </script>
</body>
</html>