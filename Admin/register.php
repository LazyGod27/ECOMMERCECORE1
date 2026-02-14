<?php
// admin/register.php

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

// 4. HANDLE POST REQUEST (Register)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['password_confirm'] ?? '';
        $email = $_POST['email'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $phone_number = $_POST['phone_number'] ?? '';

        if (empty($username) || empty($password) || empty($email) || empty($full_name) || $password !== $confirm_password) {
            header("Location: register.php?msg=" . urlencode("Registration failed: All fields required and passwords must match."));
            exit();
        }

        // Logic check: Ensure files are uploaded for ID and Profile
        if (!isset($_FILES['id_verification']) || $_FILES['id_verification']['error'] !== UPLOAD_ERR_OK) {
             header("Location: register.php?msg=" . urlencode("Identity Verification (ID) is required for professional registration."));
             exit();
        }

        $reg_result = create_admin_account($pdo, $username, $password, $email, $phone_number, $full_name);

        if ($reg_result['success']) {
            $admin_id = $reg_result['user_id'];

            // Handle Profile Image Upload if provided
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                handle_profile_image_upload($_FILES['profile_image'], $admin_id);
            }

            // Handle ID Verification Upload
            if (isset($_FILES['id_verification']) && $_FILES['id_verification']['error'] === UPLOAD_ERR_OK) {
                // We'll use a specific naming convention for ID verification files
                $upload_dir = get_profile_upload_directory();
                ensure_profile_upload_directory();
                $ext = pathinfo($_FILES['id_verification']['name'], PATHINFO_EXTENSION);
                $new_name = $admin_id . "_ID_VERIFICATION." . $ext;
                $target = $upload_dir . DIRECTORY_SEPARATOR . $new_name;
                move_uploaded_file($_FILES['id_verification']['tmp_name'], $target);
            }

            // Registration SUCCESS: Initiate OTP immediately
            $otp = generate_otp();
            if (save_otp($pdo, $admin_id, $otp)) {
                $email_result = send_otp_email($reg_result['email'], $otp, $reg_result['username']);

                $_SESSION['admin_awaiting_otp'] = true;
                $_SESSION['temp_admin_id'] = $admin_id;
                $_SESSION['temp_admin_username'] = $reg_result['username'];

                header("Location: login.php?msg=" . urlencode("✓ Account created successfully! " . $email_result . " Please check your email and verify with the OTP code."));
            } else {
                header("Location: register.php?msg=" . urlencode("Error: OTP could not be generated. Please try again."));
            }
        } else {
            header("Location: register.php?msg=" . urlencode($reg_result['message']));
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account | IMARKETPH</title>
    <link rel="icon" type="image/png" href="../image/logo.png?v=3.5">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

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
            overflow-x: hidden;
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
            max-width: 1200px;
        }

        .auth-container {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 0;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            min-height: 700px;
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

        /* Right Side - Form */
        .auth-right {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow-y: auto;
            max-height: 700px;
        }

        .auth-right::-webkit-scrollbar {
            width: 6px;
        }

        .auth-right::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .auth-right::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .form-subtitle {
            color: #64748b;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .section-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            font-weight: 700;
            margin: 20px 0 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-title i {
            width: 18px;
            height: 18px;
            color: #3b82f6;
        }

        .grid-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
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

        .upload-field {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            background: #fbfcfe;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100px;
        }

        .upload-field:hover {
            border-color: #3b82f6;
            background: #f0f4ff;
        }

        .upload-field input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-icon {
            width: 32px;
            height: 32px;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .upload-text {
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .upload-text strong {
            color: #3b82f6;
            font-weight: 600;
        }

        .file-preview {
            display: none;
            font-size: 0.8rem;
            color: #059669;
            font-weight: 600;
            margin-top: 8px;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.85rem;
            color: #64748b;
            margin: 15px 0;
            padding: 12px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3b82f6;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-size: 0.85rem;
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
            margin-top: 20px;
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
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 0.9rem;
            color: #64748b;
        }

        .form-footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
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

        .info-box {
            background: #f0f4ff;
            border: 1px solid #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #1e40af;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .info-box i {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 2px;
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
                max-height: none;
            }

            .auth-left h1 {
                font-size: 2rem;
            }

            .feature-list {
                display: none;
            }

            .grid-inputs {
                grid-template-columns: 1fr;
            }

            body::before,
            body::after {
                display: none;
            }
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
                        <i data-lucide="user-check" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h1>IMARKETPH Admin</h1>
                    <p>Join our admin team and manage the marketplace</p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <i data-lucide="shield-check" style="color: #86efac;"></i>
                            <span>Verified Admin Access</span>
                        </div>
                        <div class="feature-item">
                            <i data-lucide="lock" style="color: #60a5fa;"></i>
                            <span>Secure Registration</span>
                        </div>
                        <div class="feature-item">
                            <i data-lucide="zap" style="color: #fbbf24;"></i>
                            <span>Instant Activation</span>
                        </div>
                        <div class="feature-item">
                            <i data-lucide="users" style="color: #f87171;"></i>
                            <span>Full Dashboard Access</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Registration Form -->
            <div class="auth-right">
                <h2 class="form-title">Create Admin Account</h2>
                <p class="form-subtitle">Set up your admin credentials to access the dashboard</p>

                <?php if ($message): ?>
                    <div class="alert-message <?php echo (stripos($message, 'error') !== false || stripos($message, 'failed') !== false) ? 'alert-error' : 'alert-success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="info-box">
                    <i data-lucide="info"></i>
                    <span>Complete all required fields to create your admin account. Email verification (OTP) will be sent immediately after registration.</span>
                </div>

                <form method="POST" action="register.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="register">

                    <!-- Personal Information Section -->
                    <div class="section-title">
                        <i data-lucide="user"></i>
                        Personal Information
                    </div>

                    <div class="grid-inputs">
                        <div class="form-group">
                            <label>Full Name</label>
                            <div class="input-group">
                                <i data-lucide="user" class="input-icon"></i>
                                <input type="text" placeholder="Juan Dela Cruz" name="full_name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <div class="input-group">
                                <i data-lucide="at-sign" class="input-icon"></i>
                                <input type="text" placeholder="admin_juan" name="username" required>
                            </div>
                        </div>
                    </div>

                    <div class="grid-inputs">
                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-group">
                                <i data-lucide="mail" class="input-icon"></i>
                                <input type="email" placeholder="juan@imarketph.com" name="email" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Phone Number (Optional)</label>
                            <div class="input-group">
                                <i data-lucide="phone" class="input-icon"></i>
                                <input type="tel" placeholder="+63917xxxxxxx" name="phone_number">
                            </div>
                        </div>
                    </div>

                    <!-- Account Security Section -->
                    <div class="section-title">
                        <i data-lucide="lock"></i>
                        Account Security
                    </div>

                    <div class="grid-inputs">
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <i data-lucide="lock" class="input-icon"></i>
                                <input type="password" placeholder="••••••••" name="password" required minlength="8">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <div class="input-group">
                                <i data-lucide="lock-open" class="input-icon"></i>
                                <input type="password" placeholder="••••••••" name="password_confirm" required minlength="8">
                            </div>
                        </div>
                    </div>

                    <!-- Professional Verification Section -->
                    <div class="section-title">
                        <i data-lucide="file-check"></i>
                        Professional Verification
                    </div>

                    <div class="grid-inputs">
                        <div class="form-group">
                            <label>Identity Verification (ID) *</label>
                            <div class="upload-field" id="id-upload-field">
                                <i data-lucide="file-up" class="upload-icon"></i>
                                <div class="upload-text"><strong>Upload ID</strong> or drag & drop</div>
                                <div class="upload-text" style="font-size: 0.75rem;">JPG, PNG or PDF (Max 5MB)</div>
                                <input type="file" name="id_verification" required onchange="handleFileSelect(this, 'id-preview')">
                                <div id="id-preview" class="file-preview"><i data-lucide="check" style="width: 14px; height: 14px;"></i> <span>File Selected</span></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Profile Picture</label>
                            <div class="upload-field" id="profile-upload-field">
                                <i data-lucide="camera" class="upload-icon"></i>
                                <div class="upload-text"><strong>Upload Photo</strong> (Headshot)</div>
                                <div class="upload-text" style="font-size: 0.75rem;">JPG, PNG (Square preferred)</div>
                                <input type="file" name="profile_image" onchange="handleFileSelect(this, 'profile-preview')">
                                <div id="profile-preview" class="file-preview"><i data-lucide="check" style="width: 14px; height: 14px;"></i> <span>File Selected</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions Section -->
                    <div class="section-title" style="margin-top: 20px;">
                        <i data-lucide="file-text"></i>
                        Terms & Conditions
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" name="accept_terms" id="accept_terms" required>
                        <label for="accept_terms">I have read and agree to the Terms & Conditions and acknowledge that I will handle all platform data responsibly and in compliance with applicable laws.</label>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                        Create Admin Account
                    </button>
                </form>

                <div class="form-footer">
                    Already have an account? <a href="login.php">Sign In Here</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function handleFileSelect(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                const span = preview.querySelector('span');
                span.textContent = fileName.length > 25 ? fileName.substring(0, 22) + '...' : fileName;
                preview.style.display = 'flex';
                lucide.createIcons();
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>

</html>
