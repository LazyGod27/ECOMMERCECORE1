<?php
// Simple Email Test Script for iMarket

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("PHPMailer/src/PHPMailer.php");
require_once("PHPMailer/src/Exception.php");
require_once("PHPMailer/src/SMTP.php");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Test - iMarket</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .container { background: #f8fafc; padding: 30px; border-radius: 10px; }
        h1 { color: #2A3B7E; }
        .result { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        code { background: #fff; padding: 2px 5px; border-radius: 3px; }
        button { 
            background: linear-gradient(135deg, #2A3B7E 0%, #3b82f6 100%);
            color: white; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 iMarket Email Test</h1>
        
        <?php
        // Check if test button was clicked
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_test'])) {
            
            echo '<div class="result info">';
            echo '<strong>⏳ Sending test email...</strong><br>';
            echo 'From: <code>longkinog@gmail.com</code><br>';
            echo 'To: <code>longkinog@gmail.com</code>';
            echo '</div>';
            
            try {
                $mail = new PHPMailer(true);
                
                // Enable debug output
                $mail->SMTPDebug = 2;
                ob_start();
                
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'longkinog@gmail.com';
                $mail->Password = 'ssau zscp bbzr vrkh';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Recipients
                $mail->setFrom('longkinog@gmail.com', 'iMarket Test');
                $mail->addAddress('longkinog@gmail.com');
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = '✓ iMarket Test Email - ' . date('Y-m-d H:i:s');
                $mail->Body = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial; }
                        .container { background: #f8fafc; padding: 20px; border-radius: 10px; }
                        .header { background: linear-gradient(135deg, #2A3B7E 0%, #3b82f6 100%); color: white; padding: 20px; border-radius: 5px; }
                        h1 { margin: 0; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>✓ Email System Working!</h1>
                        </div>
                        <p style="margin-top: 20px; font-size: 16px;">Congratulations! Your email system is properly configured.</p>
                        <p>This is a test email sent from your iMarket e-commerce store.</p>
                        <p><strong>Email sent at:</strong> ' . date('F d, Y g:i A') . '</p>
                        <hr>
                        <p style="color: #64748b; font-size: 12px;">iMarket © 2025</p>
                    </div>
                </body>
                </html>';
                
                // Send
                $mail->send();
                
                $debug_output = ob_get_clean();
                
                echo '<div class="result success">';
                echo '<strong>✓ Email Sent Successfully!</strong><br><br>';
                echo 'Check your Gmail inbox or spam folder for the test email.<br>';
                echo 'Subject: <code>✓ iMarket Test Email - ' . date('Y-m-d H:i:s') . '</code>';
                echo '</div>';
                
                // Show debug info
                echo '<div class="result info">';
                echo '<strong>Debug Information:</strong><br>';
                echo '<pre style="background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto; font-size: 12px;">';
                echo htmlspecialchars($debug_output);
                echo '</pre>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="result error">';
                echo '<strong>✗ Error Sending Email:</strong><br><br>';
                echo '<code>' . htmlspecialchars($e->getMessage()) . '</code><br><br>';
                
                // Provide helpful suggestions
                echo '<strong>Possible Issues:</strong><br>';
                echo '• Gmail credentials might be incorrect<br>';
                echo '• Gmail security blocked the connection<br>';
                echo '• Internet connection issue<br>';
                echo '• SMTP port 587 is blocked<br><br>';
                
                echo '<strong>Solutions:</strong><br>';
                echo '1. Double-check Gmail & app password are correct<br>';
                echo '2. Check Gmail account security: <a href="https://myaccount.google.com/security" target="_blank">myaccount.google.com/security</a><br>';
                echo '3. Allow less secure apps or verify app password was generated correctly<br>';
                echo '4. Try running this test again in a moment<br>';
                
                echo '</div>';
                
                // Show full error details
                echo '<div class="result info">';
                echo '<details><summary>📋 Full Error Details</summary>';
                echo '<pre style="background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto; font-size: 12px;">';
                echo htmlspecialchars($e->getMessage() . "\n\n" . $e->getTraceAsString());
                echo '</pre></details>';
                echo '</div>';
            }
        } else {
            // Show initial instructions
            echo '<div class="result info">';
            echo '<strong>ℹ️ Test Configuration:</strong><br>';
            echo 'Email: <code>longkinog@gmail.com</code><br>';
            echo 'SMTP: <code>smtp.gmail.com</code><br>';
            echo 'Port: <code>587</code><br>';
            echo 'Encryption: <code>STARTTLS</code>';
            echo '</div>';
            
            echo '<p>Click the button below to send a test email to your Gmail account.</p>';
        }
        ?>
        
        <form method="POST">
            <button type="submit" name="send_test">Send Test Email Now</button>
        </form>
        
        <div class="result info" style="margin-top: 30px;">
            <strong>📌 Instructions:</strong>
            <ol>
                <li>Click the button above to send a test email</li>
                <li>Check your Gmail inbox (and spam folder just in case)</li>
                <li>If email arrives = ✓ System working perfectly!</li>
                <li>If email doesn't arrive = Check error message above</li>
            </ol>
        </div>
        
        <div class="result" style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">
            <strong>⚠️ Note:</strong> First test emails may take 5-10 seconds to arrive. If nothing shows up after a minute, check spam folder or review the error message.
        </div>
    </div>
</body>
</html>