<?php
// admin/login.php

// 1. DATABASE CONNECTION & FUNCTIONS
require_once 'connection.php';
require_once 'functions.php';

// Initialize Database Connection
try {
    $pdo = get_db_connection();
} catch (RuntimeException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

// 2. START SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 3. CHECK IF ALREADY LOGGED IN
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$message = $_GET['msg'] ?? '';
$is_otp_page = isset($_SESSION['admin_awaiting_otp']) && $_SESSION['admin_awaiting_otp'] === true;

// 4. HANDLE POST REQUESTS (Login & OTP)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- RETURN TO LOGIN (Clear OTP state) ---
    if ($action === 'return_to_login') {
        unset($_SESSION['admin_awaiting_otp']);
        unset($_SESSION['temp_admin_id']);
        unset($_SESSION['temp_admin_username']);
        header("Location: login.php");
        exit();
    }

    // --- LOGIN ACTION ---
    if ($action === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $auth_result = authenticate_admin($pdo, $username, $password);

        if ($auth_result['success'] && $auth_result['redirect_view'] === 'otp') {
            // OTP initiated, refresh page to show OTP form
            header("Location: login.php?msg=" . urlencode($auth_result['message']));
            exit();
        } else {
            // Failed
            header("Location: login.php?msg=" . urlencode($auth_result['message']));
            exit();
        }
    }

    // --- OTP VERIFY ACTION ---
    if ($action === 'otp_verify') {
        $otp_input = isset($_POST['otp_code']) ? trim(strval($_POST['otp_code'])) : '';
        $user_id = isset($_SESSION['temp_admin_id']) ? intval($_SESSION['temp_admin_id']) : (isset($_POST['user_id']) ? intval($_POST['user_id']) : null);

        if (empty($otp_input)) {
            header("Location: login.php?msg=" . urlencode("Error: Please enter the OTP code."));
            exit();
        }

        $otp_input = preg_replace('/[^0-9]/', '', $otp_input);
        if (strlen($otp_input) !== 6) {
            header("Location: login.php?msg=" . urlencode("Error: OTP must be 6 digits."));
            exit();
        }

        if (!$user_id || $user_id <= 0) {
            // Session lost
            unset($_SESSION['admin_awaiting_otp']);
            header("Location: login.php?msg=" . urlencode("Session error. Please log in again."));
            exit();
        }

        $verification_result = verify_otp_and_login($pdo, $user_id, $otp_input);
        
        if (stripos($verification_result, 'successful') !== false || stripos($verification_result, 'welcome') !== false) {
            header("Location: dashboard.php?msg=" . urlencode($verification_result));
        } else {
            header("Location: login.php?msg=" . urlencode($verification_result));
        }
        exit();
    }
}

// 5. HANDLE GET ACTIONS
if (isset($_GET['action']) && $_GET['action'] === 'return_to_login') {
    unset($_SESSION['admin_awaiting_otp']);
    unset($_SESSION['temp_admin_id']);
    unset($_SESSION['temp_admin_username']);
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_otp_page ? 'OTP Verification | IMARKETPH' : 'ADMIN LOGIN | IMARKETPH'; ?></title>
    <link rel="icon" type="image/png" href="../image/logo.png?v=3.5">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Auth CSS -->
    <link rel="stylesheet" href="../css/admin/auth.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap');

        /* Simplified loader styles for elegance */
        #loader-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #ffffff; z-index: 9999; display: flex;
            justify-content: center; align-items: center;
            transition: opacity 0.4s ease-out, visibility 0.4s ease-out;
        }

        .loader-spinner {
            width: 40px; height: 40px; border: 3px solid #f3f4f6;
            border-top: 3px solid #4f46e5; border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .hidden-loader { opacity: 0; visibility: hidden; }

        .back-to-site {
            display: inline-flex; align-items: center; gap: 0.5rem;
            margin-top: 2rem; color: #64748b; text-decoration: none;
            font-size: 0.875rem; font-weight: 500; transition: color 0.2s;
        }
        .back-to-site:hover { color: #4f46e5; }
    </style>
</head>

<body>
    <div id="loader-overlay">
        <div class="loader-spinner"></div>
    </div>

    <div class="login-container">
        <div class="header">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                <div style="width: 48px; height: 48px; background: #f5f3ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                    <i data-lucide="<?php echo $is_otp_page ? 'shield-check' : 'lock'; ?>" style="width: 24px; height: 24px; color: #4f46e5;"></i>
                </div>
                <span style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.01em; color: #1e293b;">IMARKETPH | ADMIN</span>
            </div>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.75rem;">
                <?php
                if ($is_otp_page) {
                    $u = htmlspecialchars($_SESSION['temp_admin_username'] ?? 'User');
                    echo "Enter the 6-digit code sent to your email to verify login for **{$u}**.";
                } else {
                    echo 'Secure Administrative Access Portal';
                }
                ?>
            </p>
        </div>

        <?php if ($message): ?>
            <div class="alert-message <?php echo strpos($message, 'successful') !== false || strpos($message, 'DEMO CODE') !== false ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($is_otp_page): ?>
            <form method="POST" action="login.php" id="otp-form">
                <input type="hidden" name="action" value="otp_verify">
                
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label style="text-align: center; display: block; margin-bottom: 1rem;">Verification Code</label>
                    <input type="text" id="otp_code" name="otp_code" required maxlength="6" 
                        placeholder="••••••" style="letter-spacing: 1rem; text-align: center; font-size: 2rem; font-weight: 700; height: auto; padding: 1rem;">
                </div>

                <button type="submit" class="btn-base btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                    Verify Account
                </button>
            </form>
            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="login.php?action=return_to_login" style="color: #64748b; text-decoration: none; font-size: 0.875rem;">
                    <i data-lucide="arrow-left" style="width: 1rem; height: 1rem; vertical-align: middle;"></i> Re-enter credentials
                </a>
            </div>
        <?php else: ?>
            <form method="POST" action="login.php">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-group">
                        <i data-lucide="user" class="input-icon"></i>
                        <input type="text" placeholder="Admin username" name="username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <i data-lucide="key-round" class="input-icon"></i>
                        <input type="password" placeholder="••••••••" name="password" required>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; color: #64748b; cursor: pointer;">
                        <input type="checkbox" name="remember_me"> Remember me
                    </label>
                    <a href="forgot_password.php" style="color: #4f46e5; font-weight: 600; text-decoration: none;">Forgot password?</a>
                </div>

                <!-- Terms & Conditions Acceptance -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: start; gap: 0.75rem; cursor: pointer;">
                        <input type="checkbox" name="accept_terms" id="accept_terms_login" required 
                            style="width: 1.1rem; height: 1.1rem; cursor: pointer; margin-top: 2px; accent-color: #4f46e5; flex-shrink: 0;">
                        <span style="font-size: 0.8125rem; color: #475569; line-height: 1.5;">
                            By logging in, you agree to our <a href="../About/Terms & Conditions.php" target="_blank" style="color: #4f46e5; font-weight: 600; text-decoration: none; border-bottom: 1px solid rgba(79, 70, 229, 0.3);">Terms & Conditions</a> and acknowledge that you will handle all platform data responsibly.
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn-base btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                    Sign In
                </button>
            </form>
        <?php endif; ?>



        <div style="text-align: center; margin-top: 1rem;">
            <a href="../Shop/index.php" class="back-to-site">
                <i data-lucide="globe" style="width: 1rem; height: 1rem;"></i>
                Back to Public Marketplace
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
        window.addEventListener('load', () => {
            setTimeout(() => {
                const loader = document.getElementById('loader-overlay');
                if (loader) loader.classList.add('hidden-loader');
            }, 600);
        });
    </script>
</body>
</html>