<?php
// mobile_receipt.php
$pkg = $_GET['pkg'] ?? 'Trip';
$total = $_GET['total'] ?? '0';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odyssey Digital Pass</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; text-align: center; }
        .receipt-card { 
            background: white; border-radius: 20px; padding: 30px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-top: 8px solid #2ecc71; 
        }
        .status-badge { 
            background: #2ecc71; color: white; padding: 5px 15px; 
            border-radius: 20px; font-size: 0.8rem; font-weight: bold; 
        }
        h2 { color: #333; margin: 20px 0 10px; }
        .price { font-size: 2rem; color: #2ecc71; font-weight: bold; margin: 10px 0; }
        .details { text-align: left; margin-top: 25px; border-top: 1px dashed #ddd; padding-top: 20px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="receipt-card">
        <span class="status-badge">PAYMENT SUCCESSFUL</span>
        <h2><?php echo htmlspecialchars($pkg); ?></h2>
        <div class="price">$<?php echo number_format($total, 2); ?></div>
        
        <div class="details">
            <div class="row"><span>Agency:</span><strong>Odyssey Travels</strong></div>
            <div class="row"><span>Booking ID:</span><strong>#OD-<?php echo rand(1000, 9999); ?></strong></div>
            <div class="row"><span>Status:</span><strong style="color: #2ecc71;">PAID</strong></div>
        </div>

        <p style="margin-top:30px; color:#888; font-size:0.8rem;">Show this screen to your guide upon arrival.</p>
    </div>
</body>
</html>