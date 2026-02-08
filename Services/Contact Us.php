<?php
session_start();
include("../Database/config.php");

$msg = "";

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Optional: Get User ID if logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';

    $sql = "INSERT INTO contact_messages (user_id, full_name, email, subject, message) 
            VALUES ($user_id, '$full_name', '$email', '$subject', '$message')";

    // Creates a support ticket for Admin Dashboard notification
    $ticket_num = 'TKT-' . date('Y') . '-' . mt_rand(1000, 9999);
    $ticket_sql = "INSERT INTO support_tickets (ticket_number, customer_id, category, subject, message, status, priority, created_at, is_read) 
                   VALUES ('$ticket_num', $user_id, 'Contact Form', '$subject', '$message', 'Open', 'Medium', NOW(), 0)";
    mysqli_query($conn, $ticket_sql);

    if (mysqli_query($conn, $sql)) {
        // Send Email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'linbilcelestre31@gmail.com';               // SMTP username
            $mail->Password = 'erdrvfcuoeibstxo';                  // SMTP password (App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
            $mail->Port = 587;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom('no-reply@imarketph.com', 'IMarket PH');
            $mail->addAddress($email, $full_name);                      // Send to User
            $mail->addCC('linbilcelestre31@gmail.com', 'Admin');      // Send a copy to Admin

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'New Contact Message: ' . $subject;
            $mail->Body = "<h3>New Contact Message Received</h3>
                           <p><b>From:</b> $full_name ($email)</p>
                           <p><b>Subject:</b> $subject</p>
                           <p><b>Message:</b></p>
                           <p>$message</p>
                           <hr>
                           <p><i>This is an automated notification from iMarket PH.</i></p>";
            $mail->AltBody = "New Contact Message Received\n\nFrom: $full_name ($email)\nSubject: $subject\nMessage:\n$message";

            $mail->send();
            $msg = "<div class='alert alert-success'>Thank you for reaching out! We received your message and sent a confirmation email.</div>";
        } catch (Exception $e) {
            // Even if email fails, we saved it to DB and created a Support Ticket for admin.
            // So we show success to the user to avoid confusion.
            $msg = "<div class='alert alert-success'>Thank you for reaching out! We received your message.</div>";
            // error_log("Mailer Error: {$mail->ErrorInfo}"); // Log silently
        }
    } else {
        $msg = "<div class='alert alert-error'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTACT US | IMARKET PH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/services/contact.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Fix for Header Styles if necessary -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <nav>
        <?php
        $path_prefix = '../';
        include '../Components/header.php';
        ?>
    </nav>

    <div class="contact-container">
        <!-- Left Side: Information -->
        <div class="contact-info">
            <div>
                <h2>Get in Touch</h2>
                <p>Have questions about our products, support, or just want to say hello? We'd love to hear from you.
                </p>

                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Taguig City, Metro Manila, Philippines</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span>support@imarketph.com</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone-alt"></i>
                    <span>+63 912 345 6789</span>
                </div>
            </div>

            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>

        <!-- Right Side: Contact Form -->
        <div class="contact-form-wrapper">
            <h2>Send us a Message</h2>
            <?php echo $msg; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required placeholder="Enter your full name"
                        value="<?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="example@email.com"
                        value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" required placeholder="How can we help you?">
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" required placeholder="Write your message here..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Send Message <i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    <footer>
        <?php include '../Components/footer.php'; ?>
    </footer>
</body>

</html>
