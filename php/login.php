<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$path_prefix = '../';
require_once '../Components/security.php';
// session_start(); // Handled by security.php
require '../Database/config.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$msg = "";

if (isset($_POST['resend'])) {
    verify_csrf_token();
    if (isset($_SESSION['email_to_verify'])) {
        $email = $_SESSION['email_to_verify'];
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $verification_code = $row['verification_code'];

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
                $mail->Username = 'longkinog@gmail.com';
                $mail->Password = 'krgh vcoz trow gedy';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->Timeout = 20;

                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->setFrom('longkinog@gmail.com', 'iMarket');
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

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {

            $verification_code = $row['verification_code'];

            if (empty($verification_code)) {
                $verification_code = rand(100000, 999999);
                $update_sql = "UPDATE users SET verification_code='$verification_code' WHERE email='$email'";
                mysqli_query($conn, $update_sql);

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = gethostbyname('smtp.gmail.com');
                    $mail->SMTPAuth = true;
                    $mail->Username = 'longkinog@gmail.com';
                    $mail->Password = 'ptkm lwud sfgh twdh';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->Timeout = 20;

                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );

                    $mail->setFrom('longkinog@gmail.com', 'iMarket');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Verification Code - iMarket';
                    $mail->Body = 'Your verification code is: <b>' . $verification_code . '</b>';

                    $mail->send();
                } catch (Exception $e) {
                    $msg = "<div class='alert alert-danger'>Mailer Error: {$mail->ErrorInfo}</div>";
                }
            }

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

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 20px; border-radius: 10px; width: 90%; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); text-align: left; }
        .modal-header { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; text-align: center; }
        .modal-body { height: 300px; overflow-y: auto; padding: 15px; border: 1px solid #f0f0f0; border-radius: 5px; font-size: 0.9rem; line-height: 1.6; color: #333; }
        .modal-footer { margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; }
        
        /* Checkbox in Modal */
        .modal-checkbox-container { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; font-size: 0.85rem; color: #555; }
        .modal-checkbox-container input:disabled { cursor: not-allowed; }
        
        .btn-accept-modal { 
            width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 5px; 
            cursor: pointer; font-weight: bold; opacity: 0.5; 
        }
        .btn-accept-modal:not(:disabled) { opacity: 1; }
        .scroll-hint { font-size: 0.75rem; color: #d9534f; text-align: center; margin-bottom: 10px; }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-content">
                    <a href="../Admin/login.php" style="text-decoration: none; color: inherit;">
                        <img src="../image/logo.png" alt="iMarket Logo" class="brand-logo">
                        <h1>iMarket</h1>
                        <p>Your Market, Your Choice</p>
                    </a>
                </div>
            </div>

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

                    <div class="form-actions" style="justify-content: flex-end;">
                        <a href="forget.php" class="forgot-password">Forgot Password?</a>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                         <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #555;">
                            <input type="checkbox" name="agree_terms" id="agree_terms" required style="width: auto; margin: 0;" disabled>
                            <span>I agree to the <a href="javascript:void(0)" onclick="openModal()" style="color: #007bff; text-decoration: none; font-weight: bold;">Terms & Conditions</a></span>
                        </label>
                    </div>

                    <button type="submit" name="login" id="login_btn" class="btn-login" disabled style="opacity: 0.5; cursor: not-allowed;">Log In</button>

                    <div class="divider"><span>or continue with</span></div>

                    <div class="social-login">
                        <button type="button" class="btn-social facebook"><i class="fab fa-facebook-f"></i> Facebook</button>
                        <button type="button" class="btn-social google"><i class="fab fa-google"></i> Google</button>
                    </div>

                    <div class="form-footer">
                        Don't have an account? <a href="register.php">Create an account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="termsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h3>Terms and Conditions</h3></div>
            <div id="modalBody" class="modal-body">
                <h4>iMarket: E-Commerce Core Transaction 1</h4>
                <p>By accessing and using the iMarket system, you agree to the following Terms and Conditions:</p>
                <p><strong>1. Use of the System</strong><br>iMarket is an academic e-commerce platform developed for educational and research purposes. Users agree to use the system only for lawful and intended activities.</p>
                <p><strong>2. Account Responsibility</strong><br>Users are responsible for keeping their login credentials confidential. Any activity performed using a registered account is the responsibility of the account holder.</p>
                <p><strong>3. Acceptable Use</strong><br>Users must not upload harmful content, attempt unauthorized access, or manipulate product data.</p>
                <p><strong>4. AI Features Disclaimer</strong><br>AI Image Search and Voice Search are provided to assist discovery. The system does not guarantee 100% accuracy.</p>
                <p><strong>5. Privacy</strong><br>Personal data is handled in accordance with the Data Privacy Act of 2012 (RA 10173).</p>
                <p><strong>6. Termination</strong><br>Accounts may be suspended or terminated for violations of these Terms.</p>
            </div>
            
            <div class="modal-footer">
                <label class="modal-checkbox-container">
                    <input type="checkbox" id="modal_check" disabled onchange="toggleModalBtn()"> 
                    I have read and agree to the terms.
                </label>
                <button type="button" id="modalAcceptBtn" class="btn-accept-modal" disabled onclick="acceptAndClose()">Accept</button>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        const modal = document.getElementById('termsModal');
        const modalBody = document.getElementById('modalBody');
        const modalCheck = document.getElementById('modal_check');
        const modalBtn = document.getElementById('modalAcceptBtn');
        const scrollHint = document.getElementById('scrollHint');
        const mainAgree = document.getElementById('agree_terms');
        const loginBtn = document.getElementById('login_btn');

        function openModal() {
            modal.style.display = "block";
        }

        modalBody.onscroll = function() {
            if (modalBody.scrollHeight - modalBody.scrollTop <= modalBody.clientHeight + 5) {
                modalCheck.disabled = false;
                scrollHint.style.display = "none";
            }
        };

        function toggleModalBtn() {
            modalBtn.disabled = !modalCheck.checked;
        }

        function acceptAndClose() {
            modal.style.display = "none";
            mainAgree.disabled = false;
            mainAgree.checked = true;
            
            loginBtn.removeAttribute('disabled');
            loginBtn.style.opacity = '1';
            loginBtn.style.cursor = 'pointer';
        }
    </script>
</body>
</html>