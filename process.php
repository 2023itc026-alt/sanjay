<?php
// session_start must be the absolute first line
session_start();
require_once 'db_config.php';

if (isset($_POST['signup_submit'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // VALIDATION: Ensure password is at least 8 characters
    if (strlen($password) < 8) {
        echo "<script>alert('Error: Password must be at least 8 characters long.'); window.location.replace('index.php');</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $_SESSION['user'] = $fullname;
        $_SESSION['email'] = $email;
        echo "<script>alert('Account created successfully!'); window.location.replace('index.php');</script>";
        exit();
    } else {
        echo "<script>alert('Error: Email already exists.'); window.location.replace('index.php');</script>";
        exit();
    }
}
// --- LOGIN LOGIC ---
if (isset($_POST['login_submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT fullname, email, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin']; 
            
            $target = ($user['is_admin'] == 1) ? "admin_dashboard.php" : "index.php";
            // Use replace to clear login from history
            echo "<script>window.location.replace('$target');</script>";
            exit();
        }
    }
    echo "<script>alert('Invalid login'); window.location.replace('index.php');</script>";
}

// --- FORGOT PASSWORD / OTP LOGIC ---
if (isset($_POST['forgot_submit'])) {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));

        $upd = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?");
        $upd->bind_param("sss", $otp, $expiry, $email);
        
        if ($upd->execute()) {
            echo "<script>window.location.replace('verify_otp.php?email=" . urlencode($email) . "');</script>";
            exit();
        }
    } else {
        echo "<script>alert('Email not found.'); window.location.replace('index.php');</script>";
    }
}

// --- VERIFY OTP LOGIC ---
if (isset($_POST['verify_otp_submit'])) {
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND otp_code = ? AND otp_expiry > NOW()");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        echo "<script>window.location.replace('reset_password.php?email=" . urlencode($email) . "&verified=true');</script>";
        exit();
    } else {
        $stmt->close();
        echo "<script>alert('Invalid or expired OTP.'); window.location.replace('verify_otp.php?email=" . urlencode($email) . "');</script>";
    }
}

// --- UPDATE PASSWORD LOGIC ---
if (isset($_POST['update_password_submit'])) {
    $email = $_POST['email'];
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE users SET password = ?, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $new_pass, $email);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>alert('Password updated successfully!'); window.location.replace('index.php');</script>";
        exit();
    }
}	

// --- SAVE TRIP LOGIC ---
if (isset($_POST['save_trip'])) {
    if (!isset($_SESSION['email'])) { die("Login required."); }

    $email = $_SESSION['email'];
    $dest = $_POST['destination'];
    $arrival = $_POST['travel_date'];
    $departure = $_POST['departure_date']; 
    $plan = $_POST['itinerary'];

    $stmt = $conn->prepare("INSERT INTO trips (user_email, destination, travel_date, departure_date, itinerary) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $dest, $arrival, $departure, $plan);
    
    if ($stmt->execute()) {
        $new_trip_id = $conn->insert_id; 
        $stmt->close();
        // Skip the 'save' step in browser history
        echo "<script>window.location.replace('edit_trip.php?id=$new_trip_id');</script>";
        exit();
    }
}

// --- UPDATE TRIP LOGIC ---
if (isset($_POST['update_trip'])) {
    $trip_id = $_POST['trip_id'];
    $t_date = $_POST['travel_date'];
    $d_date = $_POST['departure_date'];
    $items_array = isset($_POST['itinerary_items']) ? $_POST['itinerary_items'] : [];
    $itinerary_string = implode(', ', $items_array);

    $stmt = $conn->prepare("UPDATE trips SET travel_date = ?, departure_date = ?, itinerary = ? WHERE id = ?");
    $stmt->bind_param("sssi", $t_date, $d_date, $itinerary_string, $trip_id);
    
    if ($stmt->execute()) {
        echo "<script>window.location.replace('dashboard.php?msg=success');</script>";
        exit();
    }
}

// --- DELETE TRIP LOGIC ---
if (isset($_GET['delete_trip'])) {
    $trip_id = $_GET['delete_trip'];
    $user_email = $_SESSION['email'];

    $stmt = $conn->prepare("DELETE FROM trips WHERE id = ? AND user_email = ?");
    $stmt->bind_param("is", $trip_id, $user_email);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>window.location.replace('dashboard.php?msg=deleted');</script>";
        exit();
    }
}

// --- CONFIRM FLEET & GUIDE LOGIC ---
if (isset($_GET['trip_id']) && isset($_GET['vehicle_id'])) {
    $trip_id = intval($_GET['trip_id']);
    $v_id = intval($_GET['vehicle_id']);
    $g_id = intval($_GET['guide_id']);

    $stmt = $conn->prepare("UPDATE trips SET vehicle_id = ?, guide_id = ? WHERE id = ?");
    $stmt->bind_param("iii", $v_id, $g_id, $trip_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>window.location.replace('edit_trip.php?id=$trip_id&booked=success');</script>";
        exit();
    }
}

// --- ADMIN: MANAGE PLACES ---
if (isset($_POST['admin_add_place'])) {
    $target = $_POST['target_destination']; 
    $name = $_POST['p_name'];
    $cat = $_POST['p_cat']; 
    $desc = $_POST['p_desc']; 
    $price = $_POST['p_price'];
    $img = $_FILES['p_img']['name'];
    $tmp_path = $_FILES['p_img']['tmp_name'];
        
    if (move_uploaded_file($tmp_path, "images/" . $img)) {
        $stmt = $conn->prepare("INSERT INTO explore_places (name, cat, target_destination, description, image, price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssd", $name, $cat, $target, $desc, $img, $price);
        $stmt->execute();
        echo "<script>window.location.replace('manage_planning.php?status=added');</script>";
        exit();
    }
}

if (isset($_GET['delete_place'])) {
    $id = $_GET['delete_place'];
    $stmt = $conn->prepare("DELETE FROM explore_places WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>window.location.replace('manage_planning.php?status=deleted');</script>";
    exit();
}

// --- ADMIN: MANAGE VEHICLES ---
if (isset($_POST['admin_add_vehicle'])) {
    $driver = $_POST['v_driver'];
    $model = $_POST['v_model'];
    $price = $_POST['v_price'];
    $img = $_FILES['v_img']['name'];
    
    if (move_uploaded_file($_FILES['v_img']['tmp_name'], "images/".$img)) {
        $stmt = $conn->prepare("INSERT INTO vehicles (driver_name, car_model, price_per_day, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $driver, $model, $price, $img);
        $stmt->execute();
        echo "<script>window.location.replace('manage_vehicles.php?status=added');</script>";
        exit();
    }
}

if (isset($_GET['delete_vehicle'])) {
    $id = $_GET['delete_vehicle'];
    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>window.location.replace('manage_vehicles.php?status=deleted');</script>";
    exit();
}

// --- ADMIN: MANAGE PACKAGES ---
if (isset($_POST['admin_add_package'])) {
    $name = $_POST['pkg_name'];
    $duration = $_POST['pkg_duration'];
    $price = $_POST['pkg_price'];
    $desc = $_POST['pkg_desc'];
    $img = $_FILES['pkg_img']['name'];
    
    if (move_uploaded_file($_FILES['pkg_img']['tmp_name'], "images/".$img)) {
        $stmt = $conn->prepare("INSERT INTO trip_packages (name, duration, price, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $duration, $price, $desc, $img);
        $stmt->execute();
        echo "<script>window.location.replace('manage_packages.php?status=added');</script>";
        exit();
    }
}

if (isset($_GET['delete_package'])) {
    $id = $_GET['delete_package'];
    $stmt = $conn->prepare("DELETE FROM trip_packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>window.location.replace('manage_packages.php?status=deleted');</script>";
    exit();
}
?>