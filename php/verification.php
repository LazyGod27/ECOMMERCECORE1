<?php
include("../Components/security.php"); // Handles session_start()
include("../Database/config.php");

// Import PHPMailer for OTP email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("../PHPMailer/src/PHPMailer.php");
require_once("../PHPMailer/src/Exception.php");
require_once("../PHPMailer/src/SMTP.php");

$msg = "";
$email = "";
$otp_sent = false;

if (isset($_SESSION['email_to_verify'])) {
    $email = $_SESSION['email_to_verify'];
} else {
    // If no email in session, redirect to login
    header("Location: login.php");
    exit();
}

// Check if OTP was just sent
if (isset($_SESSION['otp_just_sent'])) {
    $otp_sent = true;
    unset($_SESSION['otp_just_sent']);
}

if (isset($_POST['verify'])) {
    $verification_code = mysqli_real_escape_string($conn, $_POST['verification_code']);

    // Use prepared statement for security
    $query = "SELECT * FROM users WHERE email=? AND verification_code=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Login the user
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_name'] = $row['fullname'];

        // Clear temp session
        unset($_SESSION['email_to_verify']);

        header("Location: ../Content/Dashboard.php");
        exit();
    } else {
        $msg = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Invalid verification code. Please try again.</div>";
    }
    $stmt->close();
}

// Resend OTP
if (isset($_POST['resend'])) {
    // Generate new OTP
    $new_otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Update OTP in database
    $update_query = "UPDATE users SET verification_code=? WHERE email=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $new_otp, $email);
    $stmt->execute();
    $stmt->close();
    
    // Send OTP email
    try {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'longkinog@gmail.com';
        $mail->Password = 'ssau zscp bbzr vrkh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('longkinog@gmail.com', 'iMarket Verification');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = '🔐 Your iMarket Verification Code - ' . $new_otp;
        $mail->Body = '
        <html>
        <head>
            <style>
                body { font-family: "Segoe UI", Arial; line-height: 1.6; color: #1e293b; }
                .container { max-width: 500px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2A3B7E 0%, #3b82f6 100%); color: white; padding: 30px; border-radius: 12px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; }
                .content { background: #f8fafc; padding: 30px; border-radius: 12px; margin-top: 20px; }
                .otp-box { 
                    background: white; 
                    border: 2px solid #3b82f6; 
                    padding: 20px; 
                    border-radius: 10px; 
                    text-align: center; 
                    margin: 20px 0;
                }
                .otp-code { 
                    font-size: 36px; 
                    font-weight: 900; 
                    letter-spacing: 8px; 
                    color: #2A3B7E; 
                    font-family: "Courier New", monospace;
                }
                .note { background: #fff3cd; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .note strong { color: #f59e0b; }
                .footer { text-align: center; color: #64748b; font-size: 12px; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔐 Account Verification</h1>
                </div>
                
                <div class="content">
                    <p style="font-size: 16px; color: #2A3B7E;"><strong>Hello,</strong></p>
                    <p>Thank you for signing up with iMarket! To complete your registration and secure your account, please verify your email address using the code below:</p>
                    
                    <div class="otp-box">
                        <div class="otp-code">' . $new_otp . '</div>
                        <p style="margin: 10px 0 0 0; color: #64748b; font-size: 12px;">This code expires in 15 minutes</p>
                    </div>
                    
                    <div class="note">
                        <strong>⚠️ Important:</strong> 
                        <ul style="margin: 10px 0; padding-left: 20px;">
                            <li>Never share this code with anyone</li>
                            <li>iMarket staff will never ask for your verification code</li>
                            <li>This code is valid for 15 minutes only</li>
                        </ul>
                    </div>
                    
                    <p style="margin-top: 20px; color: #64748b;">
                        <strong>Didn\'t create this account?</strong> Please ignore this email or contact our support team immediately.
                    </p>
                </div>
                
                <div class="footer">
                    <p>© 2025 iMarket. All rights reserved.</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $mail->send();
        $msg = "<div class=\"alert alert-success\"><i class=\"fas fa-check-circle\"></i> Verification code resent successfully! Check your email.</div>";
        $_SESSION['otp_just_sent'] = true;
        
    } catch (Exception $e) {
        $msg = "<div class=\"alert alert-danger\"><i class=\"fas fa-exclamation-circle\"></i> Could not resend code. Please try again later.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account - iMarket</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/login-reg-forget/login.css">
    <style>
        :root {
            --primary-navy: #2A3B7E;
            --primary-dark: #1a2657;
            --accent-blue: #3b82f6;
            --success-green: #10b981;
            --danger-red: #ef4444;
            --warning-orange: #f59e0b;
            --soft-gray: #f8fafc;
            --light-border: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        .alert {
            padding: 16px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 0.95rem;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInDown 0.3s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert i {
            font-size: 20px;
            flex-shrink: 0;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-info {
            background-color: #dbeafe;
            color: #0c2d6b;
            border: 1px solid #bfdbfe;
        }

        .form-section .form-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .form-section h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 6px;
        }

        .form-section .form-header p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .info-card {
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            border: 1px solid var(--light-border);
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .info-card strong {
            color: var(--accent-blue);
        }

        .info-card p {
            margin: 4px 0;
            font-size: 12px;
            color: #0c2d6b;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--light-border);
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
            letter-spacing: 4px;
            font-weight: 700;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--accent-blue) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(42, 59, 126, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 10px;
            text-align: center;
            font-size: 12px;
        }

        .form-footer a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--primary-navy);
            text-decoration: underline;
        }

        .resend-container {
            text-align: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--light-border);
        }

        .resend-container p {
            margin-bottom: 6px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .resend-container button {
            background: none;
            border: none;
            color: var(--accent-blue);
            cursor: pointer;
            text-decoration: none;
            font-weight: 700;
            font-size: 12px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .resend-container button:hover {
            color: var(--primary-navy);
            text-decoration: underline;
        }

        .timer {
            color: var(--warning-orange);
            font-weight: 700;
            margin-top: 15px;
        }

        .security-note {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-left: 4px solid var(--warning-orange);
            padding: 10px 12px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 11px;
            color: #92400e;
        }

        .security-note strong {
            color: var(--warning-orange);
        }

        .security-note ul {
            margin: 6px 0 0 18px;
            padding: 0;
        }

        .security-note li {
            margin: 3px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">
            <!-- Left Side: Branding -->
            <div class="brand-section">
                <div class="brand-content">
                    <img src="../image/logo.png" alt="iMarket Logo" class="brand-logo">
                    <h1>iMarket</h1>
                    <p>Verify Your Identity</p>
                    <p style="margin-top: 20px; font-size: 13px; color: rgba(255,255,255,0.8);">🔐 Your account security is our priority</p>
                </div>
            </div>

            <!-- Right Side: Verify Form -->
            <div class="form-section">
                <div class="form-header">
                    <h2>Email Verification</h2>
                    <p>We've sent a 6-digit verification code to<br><strong><?php echo htmlspecialchars($email); ?></strong></p>
                </div>

                <!-- Display Messages -->
                <?php
                if (!empty($msg)) {
                    echo $msg;
                }
                if ($otp_sent) {
                    echo "<div class=\"alert alert-success\">
                        <i class=\"fas fa-check-circle\"></i>
                        <span>Verification code sent to your email! Check your inbox or spam folder.</span>
                    </div>";
                }
                ?>

                <!-- Info Card -->
                <div class="info-card">
                    <p><strong><i class="fas fa-info-circle"></i> How it works:</strong></p>
                    <p>✓ Check your email for a 6-digit verification code</p>
                    <p>✓ Enter the code below to verify your account</p>
                    <p>✓ Your code will expire in 15 minutes</p>
                </div>

                <!-- Verification Form -->
                <form action="" method="post">
                    <?php echo get_csrf_input_field(); ?>
                    
                    <div class="input-group">
                        <label for="verification_code">
                            <i class="fas fa-key"></i> Verification Code
                        </label>
                        <input 
                            type="text" 
                            id="verification_code" 
                            name="verification_code"
                            placeholder="000000" 
                            required 
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            autocomplete="off">
                        <small style="color: var(--text-secondary); display: block; margin-top: 4px; font-size: 11px;">
                            <i class="fas fa-info-circle"></i> Enter only the 6 digits
                        </small>
                    </div>

                    <button type="submit" name="verify" class="btn-login">
                        <i class="fas fa-check"></i> Verify Account
                    </button>

                    <div class="form-footer">
                        <a href="login.php">← Back to Login</a>
                    </div>
                </form>

                <!-- Security Note -->
                <div class="security-note">
                    <strong>🔒 Security Notice:</strong>
                    <ul>
                        <li>Never share your verification code with anyone</li>
                        <li>iMarket support will never ask for your code</li>
                        <li>This code is valid for 15 minutes only</li>
                    </ul>
                </div>

                <!-- Resend Code -->
                <div class="resend-container">
                    <p><i class="fas fa-question-circle"></i> Didn't receive the code?</p>
                    <form action="verification.php" method="post" style="display: inline;">
                        <?php echo get_csrf_input_field(); ?>
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <button type="submit" name="resend">
                            <i class="fas fa-redo"></i> Resend Verification Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
