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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated background circles */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -250px;
            left: -250px;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            bottom: -200px;
            right: -200px;
            z-index: 0;
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
        }

        .auth-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            min-height: 600px;
        }

        /* Left Side - Branding */
        .auth-left {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -150px;
            right: -150px;
        }

        .auth-left-content {
            position: relative;
            z-index: 2;
        }

        .logo-box {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .logo-box i {
            width: 40px;
            height: 40px;
            color: white;
        }

        .auth-left h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .auth-left p {
            font-size: 1rem;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .feature-list {
            text-align: left;
            display: grid;
            gap: 15px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .feature-item i {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        /* Right Side - Forms */
        .auth-right {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-tabs {
            display: none;
        }

        .auth-tab {
            padding: 12px 20px;
            background: transparent;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            color: #64748b;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s;
            text-align: center;
        }

        .auth-tab.active {
            background: white;
            color: #3b82f6;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
        }

        .auth-form {
            display: block;
        }

        .auth-form.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: #f8f7ff;
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            width: 20px;
            height: 20px;
            color: #94a3b8;
            pointer-events: none;
        }

        .input-group input {
            padding-left: 45px !important;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #64748b;
        }

        .form-footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .alert-message {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            animation: slideIn 0.3s ease-in;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fee2e2;
            color: #7f1d1d;
            border: 1px solid #fca5a5;
        }

        .back-to-site {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            justify-content: center;
        }

        .back-to-site:hover {
            gap: 12px;
        }

        .otp-input {
            letter-spacing: 1rem !important;
            text-align: center !important;
            font-size: 2rem !important;
            font-weight: 700 !important;
            height: auto !important;
            padding: 1rem !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-container {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .auth-left {
                padding: 40px 30px;
                min-height: 300px;
            }

            .auth-right {
                padding: 40px 30px;
            }

            .auth-left h1 {
                font-size: 2rem;
            }

            .feature-list {
                display: none;
            }

            body::before,
            body::after {
                display: none;
            }
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .divider::before {
            content: '';
            display: block;
            height: 1px;
            background: #e2e8f0;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <!-- Left Side - Branding -->
            <div class="auth-left">
                <div class="auth-left-content">
                    <div class="logo-box">
                        <i data-lucide="shield-check" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h1>IMARKETPH Admin</h1>
                    <p>Manage your marketplace with confidence</p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <i data-lucide="zap" style="color: #fbbf24;"></i>
                            <span>Real-time Analytics</span>
                        </div>
                        <div class="feature-item">
                            <i data-lucide="users" style="color: #60a5fa;"></i>
                            <span>Customer Management</span>
                        </div>
                        <div class="feature-item">
                            <i data-lucide="package" style="color: #34d399;"></i>
                            <span>Order Tracking</span>
                        </div>
                        <div class="feature-item">
                            <i data-lucide="shield" style="color: #f87171;"></i>
                            <span>Secure Dashboard</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="auth-right">
                <?php if ($is_otp_page): ?>
                    <!-- OTP Verification Section -->
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 10px;">Verify Your Identity</h2>
                    <p style="color: #64748b; margin-bottom: 30px; font-size: 0.95rem;">Enter the 6-digit code sent to your email</p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="otp_verify">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['temp_admin_id'] ?? ''); ?>">

                        <?php if ($message): ?>
                            <div class="alert-message <?php echo (stripos($message, 'error') !== false || stripos($message, 'invalid') !== false || stripos($message, 'expired') !== false) ? 'alert-error' : 'alert-success'; ?>">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert-message alert-success" style="background: #ecfdf5; color: #065f46; border: 1px solid #86efac; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                                <span>Verification code sent to your email, Check your inbox</span>
                            </div>
                        <?php endif; ?>

                        <div class="form-group" style="margin-bottom: 25px;">
                            <label>6-Digit OTP Code</label>
                            <div class="input-group">
                                <i data-lucide="shield-check" class="input-icon"></i>
                                <input type="text" placeholder="000000" name="otp_code" maxlength="6" inputmode="numeric" required style="text-align: center; letter-spacing: 0.5rem; font-size: 1.5rem; font-weight: 700; padding: 12px 16px;">
                            </div>
                        </div>

                        <button type="submit" class="btn-primary">
                            <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                            Verify OTP Code
                        </button>
                    </form>

                    <div class="form-footer" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 0.85rem;">
                        <p style="margin-bottom: 10px; color: #64748b;">Didn't receive the code?</p>
                        <a href="login.php?action=return_to_login" style="color: #3b82f6; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                            Back to Sign In
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Admin Login Form -->
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 10px;">Admin Login</h2>
                    <p style="color: #64748b; margin-bottom: 30px; font-size: 0.95rem;">Sign in to access the admin dashboard</p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="login">

                        <?php if ($message): ?>
                            <div class="alert-message <?php echo (stripos($message, 'error') !== false || stripos($message, 'failed') !== false) ? 'alert-error' : 'alert-success'; ?>">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Email or Username</label>
                            <div class="input-group">
                                <i data-lucide="mail" class="input-icon"></i>
                                <input type="text" placeholder="admin@example.com or username" name="username" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <i data-lucide="lock" class="input-icon"></i>
                                <input type="password" placeholder="••••••••" name="password" required>
                            </div>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" name="remember_me" id="remember">
                            <label for="remember" style="margin: 0; cursor: pointer;">Remember me</label>
                        </div>

                        <button type="submit" class="btn-primary">
                            <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                            Sign In
                        </button>
                    </form>

                    <div class="form-footer" style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
                        <a href="forgot_password.php" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #f0f4ff; border: 2px solid #3b82f6; border-radius: 8px; color: #3b82f6; font-weight: 600; transition: all 0.3s;" onmouseover="this.style.background='#3b82f6'; this.style.color='white'" onmouseout="this.style.background='#f0f4ff'; this.style.color='#3b82f6'">
                            <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                            Reset Password
                        </a>
                    </div>

                    <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #e2e8f0; text-align: center;">
                        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 15px;">Don't have an admin account?</p>
                        <a href="register.php" class="btn-primary" style="width: 100%; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                            Create Admin Account
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
