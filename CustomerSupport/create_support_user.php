<?php
// CustomerSupport/create_support_user.php
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/functions.php';

$message = "";
$status = "info"; 

try {
    $pdo = get_db_connection();
    
    // Credentials
    $username = 'support_admin';
    $password = 'admin123'; // Updated as requested
    $email = 'linbilcelestre31@gmail.com'; 
    $fullname = 'Support Admin';

    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        // Update existing user
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Also update email just in case
        $updateStmt = $pdo->prepare("UPDATE admin_users SET password_hash = ?, email = ? WHERE username = ?");
        $updateStmt->execute([$hash, $email, $username]);
        
        $message = "Account updated successfully!<br>Username: <strong>$username</strong><br>Password: <strong>$password</strong>";
        $status = "success";
    } else {
        // Create new
        $result = register_support_user($pdo, $username, $password, $email, $fullname);
        if ($result['success']) {
            $message = "User created successfully!<br>Username: <strong>$username</strong><br>Password: <strong>$password</strong>";
            $status = "success";
        } else {
            $message = "Error: " . $result['message'];
            $status = "error";
        }
    }

} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $status = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Support Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 90%; }
        .btn { display: inline-block; margin-top: 1rem; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Account Setup</h2>
        <p class="<?php echo $status; ?>"><?php echo $message; ?></p>
        <p style="font-size: 0.9em; color: #666;">Use these credentials to login.</p>
        <a href="login.php" class="btn">Go to Login</a>
    </div>
</body>
</html>
