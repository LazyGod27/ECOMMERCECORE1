# 📧 Email Configuration Guide for Order Confirmation

## Overview
The order confirmation email system has been integrated into your Payment → Confirmation workflow. When a customer places an order, they automatically receive a professional confirmation email with:
- Order reference number
- Tracking number
- Itemized list with prices
- Delivery address
- Total amount
- Payment method used

## Setup Instructions

### Step 1: Gmail Configuration
The system uses Gmail SMTP by default. Follow these steps:

#### A. Enable Gmail App Password
1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Enable **2-Step Verification** (if not already enabled)
3. Go to **App Passwords** 
4. Select "Mail" and "Windows Computer" (or your device)
5. Google will generate a 16-character password
6. Copy this password

#### B. Update Confirmation.php with Your Gmail Credentials
Open `Content/Confirmation.php` and find this section (around line 190-195):

```php
$mail->Username = 'your_email@gmail.com'; // Change this
$mail->Password = 'your_app_password'; // Change this to app password
```

Replace with:
```php
$mail->Username = 'your.actual.email@gmail.com'; // Your Gmail address
$mail->Password = 'xxxx xxxx xxxx xxxx'; // Your 16-char app password from Google
```

### Step 2: Update Sender Email
In the same section, update the sender information:

```php
$mail->setFrom('support@imarket.com', 'iMarket Support');
$mail->addReplyTo('support@imarket.com', 'iMarket Support');
```

Change `support@imarket.com` to your actual support email.

### Step 3: Test the Email

#### Test Method 1: Place a Test Order
1. Log in to your e-commerce site
2. Add a product to cart
3. Go to checkout and place an order
4. Check your email inbox for the confirmation

#### Test Method 2: Manual Test (Optional)
Create a test file `test_email.php` in your project root:

```php
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("PHPMailer/src/PHPMailer.php");
require_once("PHPMailer/src/Exception.php");
require_once("PHPMailer/src/SMTP.php");

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com'; // Change this
    $mail->Password = 'your_app_password'; // Change this
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('support@imarket.com', 'iMarket Support');
    $mail->addAddress('your_test_email@gmail.com'); // Test recipient
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - iMarket';
    $mail->Body = '<h2>This is a test email</h2><p>If you received this, email is working!</p>';
    
    $mail->send();
    echo "✓ Email sent successfully!";
} catch (Exception $e) {
    echo "✗ Error: " . $mail->ErrorInfo;
}
?>
```

Then visit `http://localhost/ecommerce_core1/test_email.php` to test.

---

## Email Configuration Options

### Option A: Gmail (Current Setup)
- **Pros:** Free, reliable, no server setup needed
- **Cons:** Limited to Gmail's sending limits (~500 emails/day)
- **Best For:** Small to medium stores

### Option B: Alternative SMTP Services

#### SendGrid
```php
$mail->Host = 'smtp.sendgrid.net';
$mail->Port = 587;
$mail->Username = 'apikey';
$mail->Password = 'SG.xxxxxxxxxxxxx'; // Your SendGrid API key
```

#### Mailgun
```php
$mail->Host = 'smtp.mailgun.org';
$mail->Port = 587;
$mail->Username = 'postmaster@yourdomain.com';
$mail->Password = 'your_mailgun_password';
```

#### Your Own SMTP Server
```php
$mail->Host = 'mail.yourserver.com';
$mail->Port = 587; // or 465 for SSL
$mail->Username = 'your_email@yourserver.com';
$mail->Password = 'your_password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // or ENCRYPTION_SMTPS
```

---

## Email Template Preview

The confirmation email includes:
- ✓ Professional header with success icon
- ✓ Order reference and tracking number
- ✓ Itemized product list with prices
- ✓ Delivery address details
- ✓ Payment method information
- ✓ Next steps information
- ✓ Footer with support contact

---

## Troubleshooting

### Email Not Sending?

**1. Check Error Logs**
Look for errors in `../Admin/Orders/` JSON files or PHP error logs.

**2. Gmail Authentication Failed**
- Verify you're using an **App Password**, not your regular Gmail password
- Check that 2-Step Verification is enabled
- Ensure you copied the 16-character password correctly (spaces included)

**3. SMTP Connection Error**
- Make sure your XAMPP can access external connections
- Check if port 587 is not blocked by firewall
- Try connecting with a different SMTP service

**4. Email Goes to Spam**
- Add `Mail-From` header
- Verify your sender domain
- Check SPF records if using a custom domain

### Common Error Messages

| Error | Solution |
|-------|----------|
| `535 5.7.8 Username and Password not accepted` | Gmail credentials wrong, use app password |
| `Failed to connect to server: Permission denied` | Port 587 blocked, try port 465 or 25 |
| `EHLO command failed` | SMTP server unreachable |
| `Could not instantiate mail function` | PHP mail() disabled, using SMTP is correct approach |

---

## Security Notes

⚠️ **IMPORTANT:**
- **Never** commit credentials to version control
- Store sensitive credentials in environment variables (later enhancement)
- Use App Passwords, not your actual Gmail password
- Keep PHPMailer library updated

### Future Enhancement: Environment Variables
```php
$mail->Username = getenv('SMTP_USERNAME');
$mail->Password = getenv('SMTP_PASSWORD');
```

---

## Email Content Customization

To customize the email template, edit the `$email_body` variable in `Content/Confirmation.php` (around line 180).

### Customizable Elements:
- Colors (change `#2A3B7E` navy blue)
- Company name and logo
- Support contact email
- Order instructions/policies
- Discount codes or promotions

---

## Next Steps

After implementation:
1. ✓ Configure your SMTP credentials
2. ✓ Test with a real order
3. ✓ Monitor email delivery
4. ✓ Customize template to match branding
5. ✓ Set up unsubscribe link (GDPR compliance)

---

**Needs Help?** 
- Check PHPMailer docs: https://github.com/PHPMailer/PHPMailer
- Gmail App Passwords: https://support.google.com/accounts/answer/185833
