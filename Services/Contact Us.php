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

    <!-- Modern Hero Section -->
    <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%); padding: 70px 20px; color: white; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1;">
            <div style="position: absolute; width: 400px; height: 400px; background: white; border-radius: 50%; top: -150px; right: -150px;"></div>
            <div style="position: absolute; width: 300px; height: 300px; background: white; border-radius: 50%; bottom: -100px; left: -100px;"></div>
        </div>
        <div style="position: relative; z-index: 2; max-width: 900px; margin: 0 auto;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 20px;">
                <i class="fas fa-envelope" style="font-size: 2.5rem;"></i>
                <h1 style="font-size: 3rem; font-weight: 800; margin: 0;">Get in Touch With Us</h1>
            </div>
            <p style="font-size: 1.15rem; opacity: 0.95; margin: 0; line-height: 1.6;">Have a question? We're here to help. Reach out to our team anytime and we'll respond as soon as possible.</p>
        </div>
    </div>

    <!-- Service Navigation Tabs -->
    <div style="background: white; padding: 0; border-bottom: 2px solid #f1f5f9; position: sticky; top: 0; z-index: 100;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; overflow-x: auto;">
            <a href="Customer_Service.php?tab=faq" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-question-circle" style="font-size: 16px;"></i> FAQs
            </a>
            <a href="Shipping & Delivery.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-shipping-fast" style="font-size: 16px;"></i> Shipping
            </a>
            <a href="Return & Refund.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-undo-alt" style="font-size: 16px;"></i> Returns
            </a>
            <a href="How_to_buy.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-shopping-cart" style="font-size: 16px;"></i> How to Buy
            </a>
            <a href="Contact Us.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #3b82f6; border-bottom: 3px solid #3b82f6; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap; background: #eff6ff;">
                <i class="fas fa-envelope" style="font-size: 16px;"></i> Contact
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 50px 20px;">
        <!-- Contact Info & Form Layout -->
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 40px; margin-bottom: 60px; align-items: start;">
            <!-- Left Column: Contact Information -->
            <div>
                <h2 style="font-size: 1.8rem; color: #1e293b; margin-bottom: 30px; font-weight: 800;">Contact Information</h2>

                <!-- Address -->
                <div style="background: white; border-radius: 12px; border-left: 5px solid #3b82f6; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.1)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'">
                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt" style="color: #3b82f6; font-size: 20px;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 8px 0; color: #1e293b; font-weight: 700; font-size: 1.05rem;">Address</h3>
                            <p style="margin: 0; color: #64748b; line-height: 1.6;">Taguig City, Metro Manila, Philippines</p>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div style="background: white; border-radius: 12px; border-left: 5px solid #3b82f6; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.1)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'">
                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-envelope" style="color: #3b82f6; font-size: 20px;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 8px 0; color: #1e293b; font-weight: 700; font-size: 1.05rem;">Email</h3>
                            <p style="margin: 0; color: #64748b;"><a href="mailto:support@imarketph.com" style="color: #3b82f6; text-decoration: none; font-weight: 600;">support@imarketph.com</a></p>
                        </div>
                    </div>
                </div>

                <!-- Phone -->
                <div style="background: white; border-radius: 12px; border-left: 5px solid #3b82f6; padding: 25px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.1)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'">
                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-phone-alt" style="color: #3b82f6; font-size: 20px;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 8px 0; color: #1e293b; font-weight: 700; font-size: 1.05rem;">Phone</h3>
                            <p style="margin: 0; color: #64748b;"><a href="tel:+639123456789" style="color: #3b82f6; text-decoration: none; font-weight: 600;">+63 912 345 6789</a></p>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div>
                    <h3 style="margin: 0 0 20px 0; color: #1e293b; font-weight: 700; font-size: 1.05rem;">Follow Us</h3>
                    <div style="display: flex; gap: 15px;">
                        <a href="#" style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: all 0.3s; font-size: 18px;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 16px rgba(59, 130, 246, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: all 0.3s; font-size: 18px;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 16px rgba(59, 130, 246, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: all 0.3s; font-size: 18px;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 16px rgba(59, 130, 246, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: all 0.3s; font-size: 18px;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 16px rgba(59, 130, 246, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact Form -->
            <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <h2 style="font-size: 1.8rem; color: #1e293b; margin-bottom: 30px; font-weight: 800;">Send Us a Message</h2>
                
                <?php if (!empty($msg)): ?>
                    <div style="margin-bottom: 25px;">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Full Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="full_name" required placeholder="Enter your full name" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box;" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" value="<?php echo isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : ''; ?>">
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Email Address <span style="color: #ef4444;">*</span></label>
                        <input type="email" name="email" required placeholder="example@email.com" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box;" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Subject <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject" required placeholder="How can we help you?" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box;" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    </div>

                    <div style="margin-bottom: 30px;">
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Message <span style="color: #ef4444;">*</span></label>
                        <textarea name="message" rows="6" required placeholder="Write your message here..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box; font-family: inherit; resize: vertical;" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"></textarea>
                    </div>

                    <button type="submit" style="width: 100%; padding: 14px 20px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.2)'">
                        <i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Send Message
                    </button>
                </form>
            </div>
        </div>

        <!-- Additional Info Cards -->
        <div style="margin-bottom: 60px;">
            <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 12px; font-weight: 800;">Why Reach Out to Us?</h2>
            <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 35px;">We're committed to providing exceptional support and service</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px;">
                <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="fas fa-headset" style="color: #3b82f6; font-size: 24px;"></i>
                    </div>
                    <h3 style="margin: 0 0 12px 0; color: #1e293b; font-weight: 700; font-size: 1.1rem;">24/7 Support</h3>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Our customer support team is available around the clock to assist you with any queries or concerns.</p>
                </div>

                <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="fas fa-clock" style="color: #3b82f6; font-size: 24px;"></i>
                    </div>
                    <h3 style="margin: 0 0 12px 0; color: #1e293b; font-weight: 700; font-size: 1.1rem;">Quick Response</h3>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">We typically respond to inquiries within 24 hours, ensuring your concerns are addressed promptly.</p>
                </div>

                <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="fas fa-thumbs-up" style="color: #3b82f6; font-size: 24px;"></i>
                    </div>
                    <h3 style="margin: 0 0 12px 0; color: #1e293b; font-weight: 700; font-size: 1.1rem;">Expert Help</h3>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Our trained specialists are ready to provide expert solutions and guidance for all your needs.</p>
                </div>
            </div>
        </div>
    </div>

    <footer style="margin-top: 80px;">
        <?php include '../Components/footer.php'; ?>
    </footer>
</body>

</html>
