<?php
// 1. Clear any previous output to prevent corruption
ob_end_clean(); 

// 2. Set headers for a high-compatibility document
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=My_Travel_Planner_Details.txt");
header("Pragma: no-cache");
header("Expires: 0");

// 3. Get details from the URL
$pkg = isset($_GET['pkg']) ? $_GET['pkg'] : 'Trip Package';
$total = isset($_GET['total']) ? $_GET['total'] : '0';

// 4. Generate the Receipt Content
echo "==============================================\n";
echo "       MY TRAVEL PLANNER - OFFICIAL RECEIPT     \n";
echo "==============================================\n\n";

echo "Package Name: " . htmlspecialchars($pkg) . "\n";
echo "Booking Status: CONFIRMED\n";
echo "Total Amount Paid: $" . number_format($total, 2) . "\n\n";

echo "----------------------------------------------\n";
echo "               TRIP SUMMARY                   \n";
echo "----------------------------------------------\n";
echo "Accommodation: Luxury Stay Included\n";
echo "Transport: Assigned Private Executive Shuttle\n\n";

echo "----------------------------------------------\n";
echo "PAYMENT COMPLETED - THANK YOU\n"; // Your requested message
echo "----------------------------------------------\n\n";

echo "Generated on: " . date("Y-m-d H:i:s") . "\n";
echo "Odyssey Travels - Explore the World with Us.";
exit();
?>