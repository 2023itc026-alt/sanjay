<?php
session_start();
require_once 'db_config.php';

// 1. Get parameters from URL
$dest = isset($_GET['destination']) ? $_GET['destination'] : 'Dubai';
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;

// 2. Fetch Dynamic Places
$stmt = $conn->prepare("SELECT name, cat, description, image FROM explore_places WHERE target_destination = ?");
$stmt->bind_param("s", $dest);
$stmt->execute();
$result = $stmt->get_result();

$db_places = [];
while($row = $result->fetch_assoc()) {
    $db_places[] = $row;
}

// 3. Static Items (Identical to your planning_details list)
// 3. Static Items (Updating extensions to match your files)
$static_items = [];
if ($dest === "Dubai") {
    $static_items = [
        ['name' => "Burj Khalifa", 'cat' => "Hotel", 'description' => "7-star luxury stay", 'image' => "bur.jpg"],
        ['name' => "Palm Jumeirah", 'cat' => "Culture", 'description' => "ultra-luxury vacations", 'image' => "palm.webp"],
        ['name' => "Dubai Mall", 'cat' => "Mall", 'description' => "Massive shopping center", 'image' => "mall.webp"],
        ['name' => "Museum of the Future", 'cat' => "Museum", 'description' => "Tech enthusiasts paradise", 'image' => "museum.jpg"],
        ['name' => "Desert Safari", 'cat' => "Nature", 'description' => "Essential 4–7 hour experience", 'image' => "safari.jfif"],
        ['name' => "Ras Al Khor Wildlife Sanctuary", 'cat' => "Nature", 'description' => "wildlife sightseeing", 'image' => "ras al khor.jpg"]
    ];
}
// 4. Merge the lists
$final_selection_list = array_merge($db_places, $static_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Places to <?php echo htmlspecialchars($dest); ?> | Odyssey Travels</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --primary: #ffa500;
            --glass: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
            --bg-dark: #1a1a2e;
        }

          
        body { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            color: white;
        }



        .selection-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        /* Grid Layout: 3 cards in a row */
        .selection-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            align-items: start; /* Prevents cards from stretching vertically */
        }

        .place-card-modern {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.3s ease, border-color 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .place-card-modern:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
        }

        .card-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .card-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .cat-badge {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 10px;
            border-radius: 50px;
            color: var(--primary);
            width: fit-content;
            margin-bottom: 12px;
        }

        .place-title {
            margin: 0 0 10px 0;
            font-size: 1.25rem;
            color: #fff;
        }

        .place-desc {
            font-size: 0.9rem;
            opacity: 0.7;
            line-height: 1.6;
            margin-bottom: 20px;
            /* Limits description height to keep cards relatively uniform */
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Action Button */
        .add-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: #1a1a2e;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            border-radius: 12px;
            transition: 0.3s ease;
            margin-top: auto; /* Pushes button to bottom of card */
        }

        .add-btn:hover {
            background: #ff8c00;
            box-shadow: 0 8px 20px rgba(255, 165, 0, 0.2);
        }

        .back-link {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            opacity: 0.8;
            transition: 0.3s;
        }

        .back-link:hover {
            opacity: 1;
            color: var(--primary);
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .selection-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 650px) {
            .selection-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="selection-container">
        <div class="header-section">
            <div>
                <h1 style="margin: 0;">Explore <?php echo htmlspecialchars($dest); ?></h1>
                <p style="opacity: 0.6; margin: 5px 0 0 0;">Add new adventures to your itinerary</p>
            </div>
            <a href="edit_trip.php?id=<?php echo $edit_id; ?>" class="back-link">
                ← Return to Editor
            </a>
        </div>

      <div class="selection-grid">
    <?php if (!empty($final_selection_list)): ?>
        <?php foreach($final_selection_list as $row): ?>
            <div class="place-card-modern">
                <img src="images/<?php echo htmlspecialchars($row['image']); ?>" 
                     alt="<?php echo htmlspecialchars($row['name']); ?>" 
                     class="card-image"
                     onerror="this.src='images/default-place.jpg'">

                <div class="card-content">
                    <span class="cat-badge"><?php echo htmlspecialchars($row['cat']); ?></span>
                    <h3 class="place-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="place-desc">
                        <?php echo htmlspecialchars($row['description']); ?>
                    </p>
                    <button onclick="addPlace('<?php echo $edit_id; ?>', '<?php echo urlencode($row['name']); ?>')" class="add-btn">
                        Add to Trip
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: span 3; text-align: center; padding: 60px; background: var(--glass); border-radius: 20px;">
            <h3 style="opacity: 0.5;">No places found for <?php echo htmlspecialchars($dest); ?>.</h3>
        </div>
    <?php endif; ?>
</div>
    </div>
<script>
function addPlace(id, name) {
    // Redirects to edit_trip.php with the specific trip ID and the place name to add
    if(id && id != 0) {
        window.location.href = `edit_trip.php?id=${id}&add_place=${name}`;
    } else {
        alert("Error: Trip ID not found.");
    }
}
</script>
</body>

</html>