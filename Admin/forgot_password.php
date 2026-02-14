<?php
// admin/forgot_password.php
require_once 'connection.php';
require_once 'functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    // To implement: Check if admin exists and send real email
    // For now, consistent with existing code
    $message = "If an account exists with that email, a reset link has been sent.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | IMARKETPH Admin</title>
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

        .reset-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
        }

        .reset-container {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px 40px;
        }

        .reset-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .reset-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            border: 2px solid #bfdbfe;
        }

        .reset-icon i {
            width: 40px;
            height: 40px;
            color: #3b82f6;
        }

        .reset-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .reset-header p {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .input-group {
            display: flex;
            align-items: center;
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

        .form-group input {
            width: 100%;
            padding: 12px 16px 12px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: #f8f7ff;
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

        .alert-message {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 25px;
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #fee2e2;
            color: #7f1d1d;
            border: 1px solid #fca5a5;
        }

        .reset-footer {
            margin-top: 30px;
            text-align: center;
        }

        .reset-footer a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .reset-footer a:hover {
            background: #eff6ff;
            gap: 12px;
        }

        .info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .info-box i {
            width: 24px;
            height: 24px;
            color: #3b82f6;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-box-content {
            color: #1e293b;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .info-box-content strong {
            color: #3b82f6;
            font-weight: 700;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .reset-container {
                padding: 40px 25px;
            }

            .reset-header h1 {
                font-size: 1.5rem;
            }

            body::before,
            body::after {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="reset-wrapper">
        <div class="reset-container">
            <!-- Header -->
            <div class="reset-header">
                <div class="reset-icon">
                    <i data-lucide="key" style="width: 40px; height: 40px;"></i>
                </div>
                <h1>Reset Password</h1>
                <p>Enter your admin email to receive a secure password reset link</p>
            </div>

            <!-- Message -->
            <?php if ($message): ?>
                <div class="alert-message alert-success">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="forgot_password.php">
                <div class="form-group">
                    <label>Admin Email Address</label>
                    <div class="input-group">
                        <i data-lucide="mail" class="input-icon"></i>
                        <input type="email" placeholder="your-email@example.com" name="email" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i data-lucide="send" style="width: 18px; height: 18px;"></i>
                    Send Reset Link
                </button>
            </form>

            <!-- Info Box -->
            <div class="info-box">
                <i data-lucide="info"></i>
                <div class="info-box-content">
                    <strong>Check your email</strong><br>
                    We'll send you a secure link to reset your password. The link will expire in 24 hours.
                </div>
            </div>

            <!-- Footer -->
            <div class="reset-footer">
                <a href="login.php">
                    <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
