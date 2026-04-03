<?php
session_start();
require_once 'db_config.php';

// Hardcoded security gatekeeper for your specific email
$admin_email = "sanjayprasath297@gmail.com"; 

if (!isset($_SESSION['email']) || $_SESSION['email'] !== $admin_email) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | My Travel Planner</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Admin-specific styling to overlay buttons on your existing cards */
        .admin-edit-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 165, 0, 0.9); /* Your theme's orange */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 10;
        }
        
        .card { position: relative; } /* Ensure buttons align to the card corners */
        
        .admin-banner {
            background: #e74c3c;
            color: white;
            text-align: center;
            padding: 5px;
            font-size: 12px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="admin-banner">DEVELOPER CONTROL PANEL ACTIVE</div>

    <header style="display: flex; justify-content: space-between; align-items: center; padding: 20px 50px;">
        <div class="logo" style="color: #ffa500; font-weight: bold; font-size: 24px;">MY TRIP PLANNER</div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 40px; height: 40px; background: #ddd; border-radius: 50%;"></div>
            <a href="process.php?logout=true" style="background: #e74c3c; color: white; padding: 8px 20px; border-radius: 5px; text-decoration: none;">Logout</a>
        </div>
    </header>

    <main style="text-align: center; margin-top: 50px;">
        <h1 style="color: white; font-size: 3rem; margin-bottom: 40px;">Where to next, Adventurer?</h1>
        
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding: 0 50px;">
            
            <div class="card" style="background: rgba(255,255,255,0.1); padding: 40px 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1);">
                <button class="admin-edit-btn" onclick="location.href='manage_planning.php'">⚙️</button>
                <i>📍</i>
                <h3 style="color: #ffa500;">Travel Planning</h3>
                <p style="color: #ccc; font-size: 14px;">Essential itineraries and route mapping.</p>
            </div>

            <div class="card" style="background: rgba(255,255,255,0.1); padding: 40px 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1);">
                <button class="admin-edit-btn" onclick="location.href='manage_vehicles.php'">⚙️</button>
                <i>🚐</i>
                <h3 style="color: #ffa500;">Vehicles & Guides</h3>
                <p style="color: #ccc; font-size: 14px;">Meet our certified drivers and luxury fleet.</p>
            </div>

            <div class="card" style="background: rgba(255,255,255,0.1); padding: 40px 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1);">
                <button class="admin-edit-btn" onclick="location.href='manage_packages.php'">⚙️</button>
                <i>🎁</i>
                <h3 style="color: #ffa500;">Trip Packages</h3>
                <p style="color: #ccc; font-size: 14px;">Explore curated all-inclusive bundles.</p>
            </div>

            <div class="card" style="background: rgba(255,255,255,0.1); padding: 40px 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1);">
                <button class="admin-edit-btn" onclick="alert('About Us Editor Opening...')">⚙️</button>
                <i>ℹ️</i>
                <h3 style="color: #ffa500;">About Us</h3>
                <p style="color: #ccc; font-size: 14px;">The story behind our travel mission.</p>
            </div>

        </div>
    </main>
</body>
</html>