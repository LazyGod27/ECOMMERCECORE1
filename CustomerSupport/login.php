<?php
// CustomerSupport/login.php

// 1. DATABASE CONNECTION & FUNCTIONS
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/functions.php';

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

// 3. CHECK IF ALREADY LOGGED IN (Support Session)
if (isset($_SESSION['support_logged_in']) && $_SESSION['support_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$message = $_GET['msg'] ?? '';
$is_otp_page = isset($_SESSION['support_awaiting_otp']) && $_SESSION['support_awaiting_otp'] === true;

// 4. HANDLE POST REQUESTS (Login & OTP)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- RETURN TO LOGIN (Clear OTP state) ---
    if ($action === 'return_to_login') {
        unset($_SESSION['support_awaiting_otp']);
        unset($_SESSION['temp_support_id']);
        unset($_SESSION['temp_support_username']);
        header("Location: login.php");
        exit();
    }

    // --- LOGIN ACTION ---
    if ($action === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $auth_result = authenticate_support($pdo, $username, $password);

        if ($auth_result['success'] && $auth_result['redirect_view'] === 'otp') {
            header("Location: login.php?msg=" . urlencode($auth_result['message']));
            exit();
        } else {
            header("Location: login.php?msg=" . urlencode($auth_result['message']));
            exit();
        }
    }

    // --- OTP VERIFY ACTION ---
    if ($action === 'otp_verify') {
        $otp_input = isset($_POST['otp_code']) ? trim(strval($_POST['otp_code'])) : '';
        $user_id = isset($_SESSION['temp_support_id']) ? intval($_SESSION['temp_support_id']) : null;

        if (empty($otp_input)) {
            header("Location: login.php?msg=" . urlencode("Error: Please enter the OTP code."));
            exit();
        }

        $otp_input = preg_replace('/[^0-9]/', '', $otp_input);
        if (strlen($otp_input) !== 6) {
            header("Location: login.php?msg=" . urlencode("Error: OTP must be 6 digits."));
            exit();
        }

        if (!$user_id) {
            unset($_SESSION['support_awaiting_otp']);
            header("Location: login.php?msg=" . urlencode("Session error. Please log in again."));
            exit();
        }

        $verification_result = verify_otp_and_login_support($pdo, $user_id, $otp_input);

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
    handle_support_login_redirect();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUPPORT PORTAL | LOGIN</title>
    <link rel="icon" type="image/png" href="../image/logo.png?v=3.5">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../css/admin/auth.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap');
        
        #loader-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #4f46e5; z-index: 9999; display: flex; justify-content: center; align-items: center; transition: opacity 0.5s; }
        .hidden-loader { opacity: 0; visibility: hidden; }
        .loader-spinner { width: 40px; height: 40px; border: 4px solid rgba(255,255,255,0.3); border-top: 4px solid white; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 50; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.5); 
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: modalSlideIn 0.3s ease-out;
            text-align: center;
        }

        @keyframes modalSlideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .otp-icon-wrapper {
            width: 60px;
            height: 60px;
            background: #eff6ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            color: #3b82f6;
        }
    </style>
</head>
<body>
    <div id="loader-overlay"><div class="loader-spinner"></div></div>
    
    <!-- Main Login Container -->
    <div class="login-container">
        <div class="header">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                    <i data-lucide="life-buoy" style="width: 24px; height: 24px; color: #3b82f6;"></i>
                </div>
                <span style="font-size: 1.25rem; font-weight: 800; color: #1e293b;">SUPPORT PORTAL</span>
            </div>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.75rem;">
                Enter credentials to access support area.
            </p>
        </div>

        <?php if ($message && !$is_otp_page): ?>
            <div class="alert-message <?php echo strpos($message, 'successful') !== false ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>Username</label>
                <div class="input-group">
                    <i data-lucide="user" class="input-icon"></i>
                    <input type="text" placeholder="Username" name="username" required>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <i data-lucide="lock" class="input-icon"></i>
                    <input type="password" placeholder="••••••••" name="password" required>
                </div>
            </div>
            <button type="submit" class="btn-base btn-primary w-full">Sign In to Support</button>
            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="color: #64748b; font-size: 0.875rem;">
                    Don't have an account? 
                    <a href="signup.php" style="color: #3b82f6; text-decoration: none; font-weight: 500;">Create one</a>
                </p>
            </div>
        </form>
    </div>

    <!-- OTP Modal -->
    <div id="otpModal" class="modal <?php echo $is_otp_page ? 'show' : ''; ?>">
        <div class="modal-content">
            <div class="otp-icon-wrapper">
                <i data-lucide="shield-check" style="width: 32px; height: 32px;"></i>
            </div>
            
            <h2 style="font-size: 1.5rem; color: #1e293b; margin-bottom: 0.5rem;">Two-Step Verification</h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;">
                We sent a verification code to your email.<br>Please enter it below to continue.
            </p>

            <?php if ($message && $is_otp_page): ?>
                <div style="background-color: #fee2e2; color: #dc2626; padding: 0.75rem; border-radius: 6px; font-size: 0.875rem; margin-bottom: 1.5rem;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="action" value="otp_verify">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <input type="text" name="otp_code" required maxlength="6" placeholder="••••••" autocomplete="off"
                        style="width: 100%; text-align: center; letter-spacing: 0.75rem; font-size: 1.75rem; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-weight: 700; color: #1e293b; outline: none; transition: border-color 0.2s;">
                </div>
                <button type="submit" class="btn-base btn-primary w-full" style="justify-content: center;">Verify & Login</button>
            </form>

            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                <a href="login.php?action=return_to_login" style="color: #64748b; text-decoration: none; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        window.addEventListener('load', () => {
            setTimeout(() => { document.getElementById('loader-overlay').classList.add('hidden-loader'); }, 600);
            
            // Focus OTP input if modal is open
            <?php if ($is_otp_page): ?>
                const otpInput = document.querySelector('input[name="otp_code"]');
                if (otpInput) setTimeout(() => otpInput.focus(), 100);
            <?php endif; ?>
        });
    </script>
</body>
</html>
