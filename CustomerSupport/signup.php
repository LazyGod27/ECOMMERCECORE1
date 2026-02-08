<?php
// CustomerSupport/signup.php

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/functions.php';

// Initialize Database Connection
try {
    $pdo = get_db_connection();
} catch (RuntimeException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');

    if (empty($username) || empty($password) || empty($email) || empty($fullname)) {
        $message = "All fields are required.";
    } else {
        $result = register_support_user($pdo, $username, $password, $email, $fullname);
        if ($result['success']) {
            header("Location: login.php?msg=" . urlencode($result['message']));
            exit();
        } else {
            $message = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUPPORT PORTAL | REGISTER</title>
    <link rel="icon" type="image/png" href="../image/logo.png?v=3.5">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../css/admin/auth.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap');
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                    <i data-lucide="user-plus" style="width: 24px; height: 24px; color: #3b82f6;"></i>
                </div>
                <span style="font-size: 1.25rem; font-weight: 800; color: #1e293b;">CREATE ACCOUNT</span>
            </div>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.75rem;">
                Join the support team.
            </p>
        </div>

        <?php if ($message): ?>
            <div class="alert-message alert-error">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="signup.php">
            <div class="form-group">
                <label>Full Name</label>
                <div class="input-group">
                    <i data-lucide="user" class="input-icon"></i>
                    <input type="text" placeholder="John Doe" name="fullname" required value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-group">
                    <i data-lucide="mail" class="input-icon"></i>
                    <input type="email" placeholder="john@example.com" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Username</label>
                <div class="input-group">
                    <i data-lucide="at-sign" class="input-icon"></i>
                    <input type="text" placeholder="johndoe" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <i data-lucide="lock" class="input-icon"></i>
                    <input type="password" placeholder="••••••••" name="password" required>
                </div>
            </div>

            <button type="submit" class="btn-base btn-primary w-full">Create Account</button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <p style="color: #64748b; font-size: 0.875rem;">
                Already have an account? 
                <a href="login.php" style="color: #3b82f6; text-decoration: none; font-weight: 500;">Sign In</a>
            </p>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
