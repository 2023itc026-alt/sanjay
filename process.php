<?php
// session_start must be the absolute first line
session_start();
require_once 'db_config.php';

if (isset($_POST['login_submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Make sure 'is_admin' is included in the SELECT query
    $stmt = $conn->prepare("SELECT fullname, email, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            
            // SAVE THE ADMIN STATUS TO THE SESSION
            $_SESSION['is_admin'] = $user['is_admin']; 
            
            // REDIRECT based on admin status
            if ($user['is_admin'] == 1) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    }
    echo "<script>alert('Invalid login'); window.location='index.php';</script>";
}if (isset($_POST['forgot_submit'])) {
    $email = $_POST['email'];
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));

        // Update user with OTP
        $upd = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?");
        $upd->bind_param("sss", $otp, $expiry, $email);
        
        if ($upd->execute()) {
            // REDIRECT is what prevents the blank screen
            header("Location: verify_otp.php?email=" . urlencode($email));
            exit();
        }
    } else {
        echo "<script>alert('Email not found.'); window.location='index.php';</script>";
    }
}
// --- VERIFY OTP LOGIC ---
if (isset($_POST['verify_otp_submit'])) {
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    
    // Check if the email and OTP match and are not expired
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND otp_code = ? AND otp_expiry > NOW()");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        // Redirect to the reset password page with a verification flag
        header("Location: reset_password.php?email=" . urlencode($email) . "&verified=true");
        exit(); // Always use exit() after a header redirect
    } else {
        $stmt->close();
        echo "<script>alert('Invalid or expired OTP. Please try again.'); window.location='verify_otp.php?email=" . urlencode($email) . "';</script>";
    }
}

// --- UPDATE PASSWORD LOGIC ---
if (isset($_POST['update_password_submit'])) {
    $email = $_POST['email'];
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    // Update the password and clear the OTP code for security
    $stmt = $conn->prepare("UPDATE users SET password = ?, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $new_pass, $email);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>alert('Password updated successfully! You can now login.'); window.location='index.php';</script>";
        exit();
    } else {
        echo "Error updating password: " . $conn->error;
    }
}	

// --- SAVE TRIP LOGIC ---
if (isset($_POST['save_trip'])) {
    if (!isset($_SESSION['email'])) {
        die("Error: You must be logged in to save a trip.");
    }

    $email = $_SESSION['email'];
    $dest = $_POST['destination'];
    $arrival = $_POST['travel_date'];
    $departure = $_POST['departure_date']; // Captured from your new form input
    $plan = $_POST['itinerary'];

    // This query now matches the 5 columns in your table
    $stmt = $conn->prepare("INSERT INTO trips (user_email, destination, travel_date, departure_date, itinerary) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $dest, $arrival, $departure, $plan);
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: dashboard.php?msg=success");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
}
if (isset($_POST['update_trip'])) {
    $trip_id = $_POST['trip_id'];
    $t_date = $_POST['travel_date'];
    $d_date = $_POST['departure_date'];
    
    // 1. Capture the array of items
    $items_array = isset($_POST['itinerary_items']) ? $_POST['itinerary_items'] : [];
    
    // 2. Convert the array back into a comma-separated string for the database
    $itinerary_string = implode(', ', $items_array);

    // 3. Update the database
    $stmt = $conn->prepare("UPDATE trips SET travel_date = ?, departure_date = ?, itinerary = ? WHERE id = ?");
    $stmt->bind_param("sssi", $t_date, $d_date, $itinerary_string, $trip_id);
    
    if ($stmt->execute()) {
        // Redirect back to dashboard on success
        header("Location: dashboard.php?msg=success");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}// --- DELETE SAVED TRIP ---
if (isset($_GET['delete_trip'])) {
    $trip_id = $_GET['delete_trip'];
    $user_email = $_SESSION['email']; // Safety: Only allow users to delete their own trips

    // 1. Prepare and execute the delete
    $stmt = $conn->prepare("DELETE FROM trips WHERE id = ? AND user_email = ?");
    $stmt->bind_param("is", $trip_id, $user_email);
    
    if ($stmt->execute()) {
        $stmt->close();
        // 2. REDIRECT back to dashboard
        header("Location: dashboard.php?msg=deleted");
        exit(); // 3. CRITICAL: Prevents the "empty page" by stopping execution here
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
// --- ADMIN: ADD NEW PLACE (UPDATED WITH PRICE) ---
if (isset($_POST['admin_add_place'])) {
    $target = $_POST['target_destination']; 
    $name = $_POST['p_name'];
    $cat = $_POST['p_cat']; 
    $desc = $_POST['p_desc']; 
    $price = $_POST['p_price']; // CAPTURED FROM ADMIN FORM
    $img = $_FILES['p_img']['name'];
    $tmp_path = $_FILES['p_img']['tmp_name'];
        
    if (move_uploaded_file($tmp_path, "images/" . $img)) {
        // Updated query to include price column
        $stmt = $conn->prepare("INSERT INTO explore_places (name, cat, target_destination, description, image, price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssd", $name, $cat, $target, $desc, $img, $price);
        $stmt->execute();
        header("Location: manage_planning.php?status=added");
        exit();
    } else {
        die("Error: move_uploaded_file failed.");
    }
}
// --- ADMIN: DELETE PLACE ---
if (isset($_GET['delete_place'])) {
    $id = $_GET['delete_place'];
    $stmt = $conn->prepare("DELETE FROM explore_places WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_planning.php?status=deleted");
}
// --- ADMIN: ADD NEW VEHICLE ---
if (isset($_POST['admin_add_vehicle'])) {
    $driver = $_POST['v_driver'];
    $model = $_POST['v_model'];
    $price = $_POST['v_price'];
    $img = $_FILES['v_img']['name'];
    
    if (move_uploaded_file($_FILES['v_img']['tmp_name'], "images/".$img)) {
        $stmt = $conn->prepare("INSERT INTO vehicles (driver_name, car_model, price_per_day, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $driver, $model, $price, $img);
        $stmt->execute();
        header("Location: manage_vehicles.php?status=added");
    }
}

// --- ADMIN: DELETE VEHICLE ---
if (isset($_GET['delete_vehicle'])) {
    $id = $_GET['delete_vehicle'];
    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_vehicles.php?status=deleted");
}
// --- ADMIN: ADD NEW PACKAGE ---
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
        header("Location: manage_packages.php?status=added");
    }
}

// --- ADMIN: DELETE PACKAGE ---
if (isset($_GET['delete_package'])) {
    $id = $_GET['delete_package'];
    $stmt = $conn->prepare("DELETE FROM trip_packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_packages.php?status=deleted");
}
?>