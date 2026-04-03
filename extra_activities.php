<?php 
session_start();
if (!isset($_SESSION['user'])) { header("Location: index.php"); exit(); }

$pkg = isset($_GET['pkg']) ? htmlspecialchars($_GET['pkg']) : 'the trip';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Extra Activities - <?php echo $pkg; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .extra-container { padding: 50px; max-width: 1100px; margin: auto; color: white; height: 100vh; overflow-y: auto; }
        .activity-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 30px; }
        
        .activity-card { 
            background: var(--glass); border-radius: 15px; overflow: hidden; 
            border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; 
        }
        .activity-card:hover { transform: scale(1.03); border-color: var(--primary); }
        .activity-card img { width: 100%; height: 200px; object-fit: cover; }
        .activity-info { padding: 20px; }
        .activity-info h3 { color: var(--primary); margin-bottom: 10px; }
        .activity-info p { font-size: 14px; line-height: 1.6; opacity: 0.9; }
    </style>
</head>
<body>
    <div class="extra-container">
        <a href="javascript:history.back()" style="color: var(--primary); text-decoration: none; font-weight: 600;">← Back to Itinerary</a>
        
        <h1 style="margin-top: 20px;">Explore Extra Activities for <?php echo $pkg; ?></h1>
        <p>Enhance your free time with these highly recommended experiences.</p>

        <div class="activity-grid">
            <div class="activity-card">
                <img src="yatch.jpg" alt="Activity">
                <div class="activity-info">
                    <h3>Sunset Yacht Cruise</h3>
                    <p>Sail along the coast as the sun sets. Includes dinner, music, and breathtaking views of the city skyline.</p>
                </div>
            </div>

            <div class="activity-card">
                <img src="foodtour.png" alt="Activity">
                <div class="activity-info">
                    <h3>Local Food Street Tour</h3>
                    <p>Discover hidden culinary gems. Taste authentic dishes led by a local guide through the busiest food markets.</p>
                </div>
            </div>

            <div class="activity-card">
                <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=500" alt="Activity">
                <div class="activity-info">
                    <h3>Museum & Gallery Pass</h3>
                    <p>Access the top 3 museums in the area. Perfect for those who want to dive deep into the local culture and history.</p>
                </div>
            </div>

            <div class="activity-card">
                <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=500" alt="Activity">
                <div class="activity-info">
                    <h3>Night City Photography</h3>
                    <p>A guided tour for photography enthusiasts. Capture the city lights from the best vantage points at night.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>