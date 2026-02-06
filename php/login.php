<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$path_prefix = '../';
require_once '../Components/security.php';
// session_start(); // Handled by security.php
include("../Database/config.php");
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$msg = "";

if (isset($_POST['resend'])) {
    verify_csrf_token();
    if (isset($_SESSION['email_to_verify'])) {
// ...
// ...
if (isset($_POST['login'])) {
    verify_csrf_token();
    $email = mysqli_real_escape_string($conn, $_POST['email']);
        $email = $_SESSION['email_to_verify'];
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $verification_code = $row['verification_code'];

            // If no code exists (rare case if here), generate one
            if (empty($verification_code)) {
                $verification_code = rand(100000, 999999);
                $update_sql = "UPDATE users SET verification_code='$verification_code' WHERE email='$email'";
                mysqli_query($conn, $update_sql);
            }

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = gethostbyname('smtp.gmail.com');
                $mail->SMTPAuth = true;
                $mail->Username = 'linbilcelestre31@gmail.com';
                $mail->Password = 'ptkm lwud sfgh twdh';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->Timeout = 20;

                // Fix for XAMPP SSL issues
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->setFrom('linbilcelestre31@gmail.com', 'iMarket');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Verification Code - iMarket';
                $mail->Body = 'Your verification code is: <b>' . $verification_code . '</b>';

                $mail->send();
                $_SESSION['resend_msg'] = "<div class='alert-success'>Verification code resent.</div>";
            } catch (Exception $e) {
                $_SESSION['resend_msg'] = "<div class='alert-error'>Mailer Error: {$mail->ErrorInfo}</div>";
            }
            header("Location: verification.php");
            exit();
        }
    }
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Use password_verify since we hashed it in register.php
        if (password_verify($password, $row['password'])) {

            $verification_code = $row['verification_code'];

            // Ensure we have a verification code (generate if missing)
            if (empty($verification_code)) {
                $verification_code = rand(100000, 999999);
                $update_sql = "UPDATE users SET verification_code='$verification_code' WHERE email='$email'";
                mysqli_query($conn, $update_sql);

                // Send email only when generating a NEW code
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = gethostbyname('smtp.gmail.com');
                    $mail->SMTPAuth = true;
                    $mail->Username = 'linbilcelestre31@gmail.com';
                    $mail->Password = 'ptkm lwud sfgh twdh';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->Timeout = 20;

                    // Fix for XAMPP SSL issues
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );

                    $mail->setFrom('linbilcelestre31@gmail.com', 'iMarket');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Verification Code - iMarket';
                    $mail->Body = 'Your verification code is: <b>' . $verification_code . '</b>';

                    $mail->send();
                } catch (Exception $e) {
                    $msg = "<div class='alert alert-danger'>Mailer Error: {$mail->ErrorInfo}</div>";
                }
            }

            // Redirect to verification page
            $_SESSION['email_to_verify'] = $email;
            header("Location: verification.php");
            exit();

        } else {
            $msg = "<div class='alert alert-danger'>Incorrect password.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Email not registered.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - iMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <link rel="stylesheet" href="../css/login-reg-forget/login.css">
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            text-align: center;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">
            <!-- Left Side: Branding -->
            <div class="brand-section">
                <div class="brand-content">
                    <a href="../Admin/login.php" style="text-decoration: none; color: inherit;">
                        <img src="../image/logo.png" alt="iMarket Logo" class="brand-logo">
                        <h1>iMarket</h1>
                        <p>Your Market, Your Choice</p>
                    </a>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="form-section">
                <div class="form-header">
                    <h2>Welcome</h2>
                    <p>Sign in to your account.</p>
                </div>

                <?php echo $msg; ?>

                <form action="" method="POST">
                    <?php echo get_csrf_input_field(); ?>
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Password" required>
                            <i class="fa fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> <span>Remember me</span>
                        </label>
                        <a href="forget.php" class="forgot-password">Forgot Password?</a>
                    </div>


                    <div class="form-group" style="margin-bottom: 1.5rem;">
                         <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #555;">
                            <input type="checkbox" name="agree_terms" required style="width: auto; margin: 0;">
                            <span>I agree to the <a href="../About/Terms & Conditions.php" target="_blank" style="color: #007bff; text-decoration: none;">Terms & Conditions</a></span>
                        </label>
                    </div>

                    <button type="submit" name="login" class="btn-login">Log In</button>

                    <div class="divider">
                        <span>or continue with</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="btn-social facebook">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </button>
                        <button type="button" class="btn-social google">
                            <i class="fab fa-google"></i> Google
                        </button>
                    </div>

                    <div class="form-footer">
                        Don't have an account? <a href="register.php">Create an account</a>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>