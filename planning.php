<?php 
session_start();
if (!isset($_SESSION['user'])) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Destination | Odyssey Travels</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .planning-container { padding: 50px; max-width: 1200px; margin: auto; color: white; }
        .dest-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-top: 30px; }
        .dest-card { 
            background: var(--glass); border-radius: 15px; overflow: hidden; 
            border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; cursor: pointer;
        }
        .dest-card:hover { transform: translateY(-10px); border-color: var(--primary); }
        .dest-card img { width: 100%; height: 200px; object-fit: cover; }
        .dest-info { padding: 20px; text-align: center; }
        .dest-info h3 { margin: 0; font-size: 1.5rem; }
.nav-container {
    position: absolute;
    top: 20px;
    right: 40px;
    z-index: 1000;
}

.dash-btn {
    background: #ffa500; /* Your orange color */
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}
    </style>
</head>
<body>
<div class="nav-container">
    <a href="dashboard.php" class="dash-btn">🏠 My Dashboard</a>
</div>
    <div class="planning-container">
        <a href="index.php" style="color: var(--primary); text-decoration: none;">← Back to Dashboard</a>
        <h1 style="margin-top: 20px;">Where to next?</h1>
        <p>Select a destination to start building your custom itinerary.</p>

        <div class="dest-grid">
            <a href="planning_details.php?place=Dubai" style="text-decoration: none; color: inherit;">
                <div class="dest-card">
                    <img src="dubai.jpg" alt="Dubai">
                    <div class="dest-info"><h3>Dubai</h3></div>
                </div>
            </a>

            <a href="planning_details.php?place=Paris" style="text-decoration: none; color: inherit;">
                <div class="dest-card">
                    <img src="paris.jpg" alt="Paris">
                    <div class="dest-info"><h3>Paris</h3></div>
                </div>
            </a>

            <a href="planning_details.php?place=Tokyo" style="text-decoration: none; color: inherit;">
                <div class="dest-card">
                    <img src="tokyo.jpg" alt="Tokyo">
                    <div class="dest-info"><h3>Tokyo</h3></div>
                </div>
            </a>
          <a href="planning_details.php?place=malaysia" style="text-decoration: none; color: inherit;">
                <div class="dest-card">
                    <img src="malaysia.jpg" alt="malaysia">
                    <div class="dest-info"><h3>malaysia</h3></div>
                </div>
            </a>
         <a href="planning_details.php?place=goa" style="text-decoration: none; color: inherit;">
                <div class="dest-card">
                    <img src="goa.avif" alt="goa">
                    <div class="dest-info"><h3>goa</h3></div>
                </div>
            </a>
           <a href="planning_details.php?place=manali" style="text-decoration: none; color: inherit;">
                <div class="dest-card">
                    <img src="manali.jpg" alt="manali">
                    <div class="dest-info"><h3>manali</h3></div>
                </div>
            </a>
   <a href="planning_details.php?place=russia" style="text-decoration: none; color: inherit;">
                <div class="dest-card">
                    <img src="russia.jpg" alt="russia">
                    <div class="dest-info"><h3>russia</h3></div>
                </div>
            </a>
        </div>
    </div>
</body>
</html>