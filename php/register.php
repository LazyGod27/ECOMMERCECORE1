<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../Components/security.php';
include("../Database/config.php");
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$msg = "";

if (isset($_POST['submit'])) {
    verify_csrf_token();
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        $msg = "<div class='alert-error'>Passwords do not match.</div>";
    } else {
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $msg = "<div class='alert-error'>Email already exists.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = rand(100000, 999999);

            // Assuming 'verification_code' column exists
            $sql = "INSERT INTO users (fullname, email, password, verification_code, name) VALUES ('$fullname', '$email', '$hashed_password', '$verification_code', '$fullname')";

            if (mysqli_query($conn, $sql)) {
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

                    $mail->setFrom('linbilcelestre31@gmail.com', 'iMarket Support');
                    $mail->addAddress($email, $fullname);

                    // Attach Logo
                    $mail->addEmbeddedImage('../image/logo.png', 'logo_img');

                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Account - iMarket';

                    $mail_content = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f4f4f4; padding: 20px;'>
                        <div style='background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center;'>
                            <img src='cid:logo_img' alt='iMarket Logo' style='max-width: 150px; margin-bottom: 20px;'>
                            <h2 style='color: #333; margin-bottom: 10px;'>Verify Your Account</h2>
                            <p style='color: #666; font-size: 16px; line-height: 1.5; margin-bottom: 30px;'>
                                Thank you for registering with iMarket. Please use the verification code below to complete your registration.
                            </p>
                            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; display: inline-block; margin-bottom: 30px;'>
                                <span style='font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #007bff;'>$verification_code</span>
                            </div>
                            <p style='color: #999; font-size: 14px;'>
                                If you did not create an account, please ignore this email.
                            </p>
                        </div>
                        <div style='text-align: center; margin-top: 20px; color: #888; font-size: 12px;'>
                            &copy; " . date('Y') . " iMarket. All rights reserved.
                        </div>
                    </div>";

                    $mail->Body = $mail_content;

                    // Redirect to verification page

                    $_SESSION['email_to_verify'] = $email;
                    header("Location: verification.php");
                    exit();

                } catch (Exception $e) {
                    $msg = "<div class='alert-error'>Registration successful, but email could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
                }
            } else {
                $msg = "<div class='alert-error'>Error: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - iMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="../css/login-reg-forget/register.css">
    <style>
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="register-card">
            <!-- Left Side: Branding -->
            <div class="brand-section">
                <div class="brand-content">
                    <img src="../image/logo.png" alt="iMarket Logo" class="brand-logo">
                    <h1>iMarket</h1>
                    <p>Create your account</p>
                </div>
            </div>

            <!-- Right Side: Register Form -->
            <div class="form-section">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Join iMarket in a few seconds.</p>
                </div>

                <?php echo $msg; ?>

                <form action="" method="post">
                    <?php echo get_csrf_input_field(); ?>
                    <div class="input-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" placeholder="Your name" required>
                    </div>

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="password">Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password"
                                    placeholder="At least 8 character(s)" required>
                                <i class="fa fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="confirm-password">Confirm Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="confirm-password" name="confirm-password"
                                    placeholder="Repeat password" required>
                                <i class="fa fa-eye toggle-password"
                                    onclick="togglePassword('confirm-password', this)"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="submit" class="btn-register">Create Account</button>

                    <div class="form-footer">
                        Already have an account? <a href="login.php">Log in</a>
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
