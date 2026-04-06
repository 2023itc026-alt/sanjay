<?php 
session_start(); 
require_once 'db_config.php'; // Required for database connection

$user_data = null;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user_data = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Trip Planner | Explore the World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- LETTER AVATAR STYLES --- */
        .avatar-circle {
            width: 40px; 
            height: 40px; 
            background: #ffa500; 
            border-radius: 50%; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border: 2px solid white;
            transition: 0.3s;
            overflow: hidden;
        }
        .avatar-circle:hover { transform: scale(1.1); background: #ffb833; }
        .avatar-letter { color: #1a1a2e; font-weight: bold; font-size: 1.2rem; }
        
        /* Modal Avatar Style */
        .avatar-large {
            width: 100px; height: 100px; background: #ffa500; border-radius: 50%; 
            margin: 0 auto 20px; border: 3px solid white; display: flex; 
            align-items: center; justify-content: center;
        }
    </style>
</head>
<body>

   <nav>
    <div class="logo">MY TRAVEL PLANNER</div>
  <div class="auth-buttons">
    <?php if(isset($_SESSION['user']) && $user_data): 
        // Extract the first letter
        $first_letter = strtoupper(substr($user_data['fullname'], 0, 1));
    ?>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div onclick="toggleProfileModal()" class="avatar-circle">
                <?php if(!empty($user_data['profile_pic']) && $user_data['profile_pic'] !== 'default-avatar.png'): ?>
                    <img src="images/<?php echo htmlspecialchars($user_data['profile_pic']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <span class="avatar-letter"><?php echo $first_letter; ?></span>
                <?php endif; ?>
            </div>
            
            <a href="logout.php" style="text-decoration: none;">
                <button type="button" style="background: #e74c3c; border: none; color: white; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-size: 14px;">
                    Logout
                </button>
            </a>
        </div>
    <?php else: ?>
        <button class="login-btn" onclick="openModal('loginModal')">Log In</button>
        <button onclick="openModal('signupModal')">Sign Up</button>
    <?php endif; ?>
</div>
</nav>

    <div class="hero">
        <h1>Where to next, Adventurer?</h1>
        <div class="grid-container">
            <a href="planning.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <i>📍</i>
                    <h3>Travel Planning</h3>
                    <p>Essential itineraries and route mapping.</p>
                </div>
            </a>
            <a href="vehicles_guides.php?context=cab" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <i>🚐</i>
                    <h3>Vehicles & Guides</h3>
                    <p>Meet our certified drivers and luxury fleet.</p>
                </div>
            </a>
            <a href="packages.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <i>🎁</i>
                    <h3>Trip Packages</h3>
                    <p>Explore curated all-inclusive bundles.</p>
                </div>
            </a>
            <a href="about.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <i>ℹ️</i>
                    <h3>About Us</h3>
                    <p>The story behind our travel mission.</p>
                </div>
            </a>
        </div>
    </div>

    <div id="loginModal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('loginModal')">&times;</span>
            <h2>Login</h2>
            <form action="process.php" method="POST" onsubmit="return validateForm('login')">
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <div style="text-align: right;">
                    <span class="modal-link" onclick="closeModal('loginModal'); openModal('forgotModal')">Forgot Password?</span>
                </div>
                <button type="submit" name="login_submit">Sign In</button>
            </form>
            <div class="modal-footer">
                Don't have an account? <span class="modal-link" onclick="closeModal('loginModal'); openModal('signupModal')">Register Here</span>
            </div>
        </div>
    </div>

    <div id="signupModal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('signupModal')">&times;</span>
            <h2>Create Account</h2>
            <form action="process.php" method="POST" onsubmit="return validateForm('signup')">
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Create Password (Min 8 chars)" minlength="8" required>
                <button type="submit" name="signup_submit">Register</button>
            </form>
            <div class="modal-footer">
                Already a member? <span class="modal-link" onclick="closeModal('signupModal'); openModal('loginModal')">Login</span>
            </div>
        </div>
    </div>

    <div id="forgotModal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('forgotModal')">&times;</span>
            <h2>Reset Password</h2>
            <p style="font-size: 13px; margin-bottom: 10px;">Enter your email to receive a reset link.</p>
            <form action="process.php" method="POST">
                <input type="email" name="email" placeholder="Email Address" required>
                <button type="submit" name="forgot_submit">Send Link</button>
            </form>
        </div>
    </div>

<?php if($user_data): ?>
<div id="profileModal" class="modal-overlay" style="display: none; z-index: 3000; background: rgba(0,0,0,0.85); backdrop-filter: blur(5px);">
    <div style="background: #1a1a2e; margin: 10% auto; padding: 40px; border: 1px solid #ffa500; width: 400px; border-radius: 20px; color: white; text-align: center; position: relative;">
        <span onclick="toggleProfileModal()" style="position: absolute; right: 25px; top: 15px; cursor: pointer; font-size: 28px; color: #ffa500;">&times;</span>
        
        <div class="avatar-large">
            <?php if(!empty($user_data['profile_pic']) && $user_data['profile_pic'] !== 'default-avatar.png'): ?>
                 <img src="images/<?php echo htmlspecialchars($user_data['profile_pic']); ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
            <?php else: ?>
                 <span style="font-size: 3rem; color: #1a1a2e; font-weight: bold;"><?php echo $first_letter; ?></span>
            <?php endif; ?>
        </div>
        
        <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($user_data['fullname']); ?></h2>
        <p style="color: #ffa500; font-weight: bold; margin-bottom: 25px;"><?php echo htmlspecialchars($user_data['email']); ?></p>
        
        <div style="text-align: left; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; font-size: 14px; line-height: 1.8;">
            <p><strong>Role:</strong> <?php echo ($user_data['is_admin'] == 1) ? "System Administrator" : "Verified Traveler"; ?></p>
            <p><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($user_data['created_at'])); ?></p>
        </div>
        
        <button onclick="window.location.href='dashboard.php'" style="width: 100%; margin-top: 25px; background: #ffa500; color: #1a1a2e; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer;">View Trip History</button>
    </div>
</div>
<?php endif; ?>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        
        window.onload = function() {
            <?php if(!isset($_SESSION['user'])): ?>
                openModal('loginModal');
            <?php endif; ?>
        };

        function validateForm(formType) {
            const form = document.querySelector(`#${formType}Modal form`);
            const email = form.email.value.trim();
            const password = form.password.value.trim();

            if (email === "" || password === "") {
                alert("Please fill in all fields.");
                return false;
            }
            if (formType === 'signup' && password.length < 8) {
                alert("Security Alert: Your password must be at least 8 characters long.");
                return false;
            }
            return true;
        }

        function toggleProfileModal() {
            const modal = document.getElementById('profileModal');
            modal.style.display = (modal.style.display === "none" || modal.style.display === "") ? "block" : "none";
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>