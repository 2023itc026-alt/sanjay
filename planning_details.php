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
$destination = isset($_GET['place']) ? htmlspecialchars($_GET['place']) : "Unknown City";

// 3. Fetch dynamic places from Database (including price)
$stmt = $conn->prepare("SELECT name, cat, description as 'desc', image as 'img', price FROM explore_places WHERE target_destination = ?");
$stmt->bind_param("s", $destination);
$stmt->execute();
$result = $stmt->get_result();

$combined_places = [];
while($row = $result->fetch_assoc()) {
    $row['img'] = "images/" . $row['img']; 
    $combined_places[] = $row;
}

// 4. Static Items for Dubai (with prices)
$static_items = [];
if ($destination === "Dubai") {
    $static_items = [
        ['name' => "Burj Khalifa", 'cat' => "Hotel", 'desc' => "7-star luxury stay", 'img' => "bur.jpg", 'price' => 150.00],
        ['name' => "Palm Jumeirah", 'cat' => "Culture", 'desc' => "ultra-luxury vacations", 'img' => "palm.webp", 'price' => 80.00],
        ['name' => "Dubai Mall", 'cat' => "Mall", 'desc' => "Massive shopping center", 'img' => "mall.webp", 'price' => 0.00],
        ['name' => "Museum of the Future", 'cat' => "Museum", 'desc' => "Tech enthusiasts paradise", 'img' => "museum.jpg", 'price' => 40.00],
        ['name' => "Desert Safari", 'cat' => "Nature", 'desc' => "Essential 4–7 hour experience", 'img' => "safari.jfif", 'price' => 60.00],
	 ['name' => "Ras Al Khor Wildlife Sanctuary", 'cat' => "Nature", 'desc' => "wildlife sightseeing", 'img' => "ras al khor.jpg", 'price' => 40.00]
    ];
}
if ($destination === "Tokyo") {
    $static_items = [
	 ['name' => "Shibuya crossing", 'cat' => "Culture", 'desc' => "Famous crossing", 'img' => "shibuya.jpg", 'price' => 20.00],

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
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            color: white;
        }

        .planner-layout { 
            display: grid; 
            grid-template-columns: 350px 1fr 350px; 
            gap: 20px; 
            padding: 30px; 
            align-items: start;
        }

        .column { 
            background: rgba(255,255,255,0.1); 
            backdrop-filter: blur(15px);
            border-radius: 20px; 
            padding: 25px; 
            border: 1px solid rgba(255,255,255,0.1); 
            height: auto; 
        }
        
        /* --- CARDS & UI --- */
        .search-box { width: 100%; padding: 12px; border-radius: 25px; border: none; margin-bottom: 15px; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.1); }
        .cat-btn { background: none; border: 1px solid #f39c12; color: white; padding: 6px 12px; border-radius: 15px; font-size: 12px; cursor: pointer; margin: 2px; transition: 0.3s; }
        .cat-btn.active { background: #f39c12; }

        .place-card { background: rgba(0,0,0,0.3); padding: 15px; border-radius: 15px; margin-bottom: 15px; transition: 0.3s; border: 1px solid transparent; }
        .place-card:hover { border: 1px solid #f39c12; transform: translateY(-5px); }
        .place-card img { width: 100%; height: 120px; object-fit: cover; border-radius: 10px; }
        .add-btn { background: #f39c12; border: none; color: white; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; float: right; font-weight: bold; }

        .itinerary-item { background: rgba(255,255,255,0.05); padding: 12px; border-radius: 10px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #f39c12; }

        /* --- BUTTONS --- */
        .add-itinerary-btn {
            background-color: #f39c12; 
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease; 
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.2);
            margin-top: 15px;
        }

        .add-itinerary-btn:hover {
            background-color: #e67e22; 
            transform: translateY(-3px); 
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4); 
        }

        /* --- BILLING BOX --- */
        .bill-box {
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .bill-item { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; opacity: 0.8; }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; color: #f39c12; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="planner-layout">
        <div class="column">
            <h3 style="margin-bottom:15px;">Explore <?php echo $destination; ?></h3>
            <input type="text" id="searchInput" class="search-box" placeholder="Search places..." onkeyup="filterPlaces()">
            
            <div class="category-filters" style="margin-bottom: 20px;">
                <button class="cat-btn active" onclick="filterCat('all', this)">All</button>
                <button class="cat-btn" onclick="filterCat('Hotel', this)">Hotels</button>
                <button class="cat-btn" onclick="filterCat('Restaurant', this)">Food</button>
                <button class="cat-btn" onclick="filterCat('Museum', this)">Culture</button>
                <button class="cat-btn" onclick="filterCat('Park', this)">Nature</button>
            </div>
            <div id="suggestionsBox"></div>
        </div>

        <div class="column" id="detailsColumn" style="text-align: center;">
            <h2 id="viewHeader">Trip to <?php echo $destination; ?></h2>
            <p id="viewSub">Customize your dream vacation</p>
            <hr style="opacity: 0.2; margin: 20px 0;">
            
            <div id="placeDetailsView">
                <div id="itineraryBox">
                    <p style="opacity: 0.5;">No items added to your list yet.</p>
                </div>
            </div>
        </div>

        <div class="column">
            <h3>Final Plan</h3>
           <form action="vehicles_guides.php" method="GET" style="margin-top:15px;">
    <input type="hidden" name="location" value="<?php echo $destination; ?>">
    
    <label style="font-size:13px; opacity:0.8;">Arrival Date:</label>
    <input type="date" name="travel_date" class="search-box" required>
    
    <label style="font-size:13px; opacity:0.8;">Departure Date:</label>
    <input type="date" name="departure_date" class="search-box" required>

    <div class="bill-box">
        <h4 style="font-size:14px; margin-bottom:10px; color:#f39c12;">Bill Calculation</h4>
        <div id="billItemsList">
            <p style="font-size:12px; opacity:0.5;">Select places to calculate cost.</p>
        </div>
        <div class="total-row">
            <span>Total:</span>
            <span id="totalDisplay">$0.00</span>
        </div>
    </div>

    <textarea name="itinerary" id="hiddenData" style="display:none;"></textarea>
    <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
    
    <button type="submit" class="add-itinerary-btn" style="width: 100%;">Save Trip to Profile</button>
</form>
        </div>
    </div>

    <script>
    const places = <?php echo json_encode($final_list); ?>;
    let myPlan = []; // Now stores objects: {name, price}

    function displayPlaces(data) {
        const box = document.getElementById('suggestionsBox');
        box.innerHTML = data.map(p => {
            const price = parseFloat(p.price || 0);
            return `
                <div class="place-card">
                    <img src="${p.img}" alt="${p.name}" onclick="showDetails('${p.name}')" style="cursor:pointer;">
                    <div style="margin-top:10px;">
                        <button class="add-btn" onclick="addToPlan('${p.name}')">+</button>
                        <h4 onclick="showDetails('${p.name}')" style="cursor:pointer;">${p.name}</h4>
                        <small style="color:#f39c12;">$${price.toFixed(2)}</small>
                    </div>
                </div>
            `;
        }).join('');
    }

    displayPlaces(places);

    function filterPlaces() {
        const val = document.getElementById('searchInput').value.toLowerCase();
        const filtered = places.filter(p => p.name.toLowerCase().includes(val));
        displayPlaces(filtered);
    }

    function filterCat(cat, btn) {
        document.querySelectorAll('.cat-btn').forEach(button => button.classList.remove('active'));
        btn.classList.add('active');
        const filtered = (cat === 'all') ? places : places.filter(p => p.cat === cat);
        displayPlaces(filtered);
    }

    function addToPlan(name) {
        const place = places.find(p => p.name === name);
        myPlan.push({ 
            name: place.name, 
            price: parseFloat(place.price || 0) 
        });
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

        // Update Central Itinerary List
        if (myPlan.length === 0) {
            if (itineraryBox) itineraryBox.innerHTML = '<p style="opacity: 0.5;">No items added to your list yet.</p>';
            billBox.innerHTML = '<p style="font-size:12px; opacity:0.5;">Select places to calculate cost.</p>';
            totalDisplay.innerText = "$0.00";
            totalInput.value = "0";
        } else {
            // Itinerary HTML
            const itineraryHtml = myPlan.map((item, index) => `
                <div class="itinerary-item">
                    <span>${item.name}</span>
                    <button onclick="removeFromPlan(${index})" style="background:none; border:none; color:#e74c3c; cursor:pointer; font-weight:bold;">✕</button>
                </div>
            `).join('');
            if (itineraryBox) itineraryBox.innerHTML = `<h4 style="margin-bottom:15px; text-align:left;">Your Selected Places:</h4>${itineraryHtml}`;

            // Bill Calculation HTML
            billBox.innerHTML = myPlan.map(item => {
                total += item.price;
                return `
                    <div class="bill-item">
                        <span>${item.name}</span>
                        <span>$${item.price.toFixed(2)}</span>
                    </div>
                `;
            }).join('');

            totalDisplay.innerText = `$${total.toFixed(2)}`;
            totalInput.value = total.toFixed(2);
        }

        hiddenData.value = myPlan.map(p => p.name).join(', ');
    }

    function showDetails(name) {
        const place = places.find(p => p.name === name);
        if (!place) return;

        const detailsBox = document.getElementById('placeDetailsView');
        detailsBox.innerHTML = `
            <div style="animation: fadeIn 0.5s; text-align: left;">
                <button onclick="resetCenterView()" style="background:none; border:none; color:#f39c12; cursor:pointer; margin-bottom:10px; font-weight:bold;">← Back to Summary</button>
                <img src="${place.img}" style="width:100%; border-radius:15px; margin-bottom:20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <h2 style="color:#f39c12; margin-bottom:10px;">${place.name}</h2>
                <p style="font-size: 15px; line-height: 1.6; color: #ddd; margin-bottom:20px;">${place.desc || place.description}</p>
                
                <button class="add-itinerary-btn" onclick="addToPlan('${place.name}')">
                    Add to My Itinerary - $${parseFloat(place.price).toFixed(2)}
                </button>
                
                <hr style="opacity: 0.1; margin: 25px 0;">
                <div id="itineraryBox"></div>
            </div>
        `;
        updateUI(); // Ensure list persists below details
    }

    function resetCenterView() {
        document.getElementById('placeDetailsView').innerHTML = `<div id="itineraryBox"></div>`;
        updateUI();
    }
    </script>
</body>
</html>