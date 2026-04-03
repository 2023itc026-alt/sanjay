<?php
$host = "127.0.0.1";
$user = "root"; // Default XAMPP user
$pass = "";     // Default XAMPP password is empty
$dbname = "trip_planner_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Colombo'); // Change to your local timezone
?>