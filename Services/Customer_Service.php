<?php
session_start();
include("../Database/config.php");

// Clean input helper
function clean_input($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

$msg = "";
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'faq';
$view_ticket_id = isset($_GET['view']) ? $_GET['view'] : null;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Handle Ticket Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_ticket'])) {
    if (!$user_id) {
        $msg = "<div class='alert alert-error'>You must be logged in to submit a ticket.</div>";
    } else {
        $category = clean_input($_POST['category']);
        $subject = clean_input($_POST['subject']);
        $message = clean_input($_POST['message']);

        if (!empty($category) && !empty($subject) && !empty($message)) {
            $ticket_number = 'TKT-' . date('Y') . '-' . mt_rand(1000, 9999);
            $sql = "INSERT INTO support_tickets (ticket_number, customer_id, category, subject, message, status) VALUES ('$ticket_number', '$user_id', '$category', '$subject', '$message', 'Open')";
            if (mysqli_query($conn, $sql)) {
                $msg = "<div class='alert alert-success'>Ticket <strong>#$ticket_number</strong> submitted successfully! You can track it in 'My Tickets'.</div>";
                $tab = 'history'; 
            } else {
                $msg = "<div class='alert alert-error'>Error: " . mysqli_error($conn) . "</div>";
            }
        } else {
            $msg = "<div class='alert alert-error'>All fields are required.</div>";
        }
    }
}

// Handle Reply Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_reply'])) {
    $t_id = intval($_POST['ticket_id']);
    $reply_msg = clean_input($_POST['reply_message']);
    
    if (!empty($reply_msg) && $user_id) {
        $sql_reply = "INSERT INTO ticket_replies (ticket_id, sender_id, sender_type, message) VALUES ('$t_id', '$user_id', 'customer', '$reply_msg')";
        if (mysqli_query($conn, $sql_reply)) {
            // Reset is_read for admin to see new activity
            mysqli_query($conn, "UPDATE support_tickets SET status='Open', is_read=0 WHERE id='$t_id'");
            $msg = "<div class='alert alert-success'>Reply sent successfully!</div>";
        }
    }
}

// Fetch User Tickets
$my_tickets = [];
$selected_ticket = null;
$ticket_replies = [];

if ($user_id) {
    if ($view_ticket_id) {
        $sql_select = "SELECT * FROM support_tickets WHERE customer_id = '$user_id' AND ticket_number = '$view_ticket_id'";
        $res_select = mysqli_query($conn, $sql_select);
        if ($res_select && mysqli_num_rows($res_select) > 0) {
            $selected_ticket = mysqli_fetch_assoc($res_select);
            $st_id = $selected_ticket['id'];
            
            // Fetch replies
            $res_replies = mysqli_query($conn, "SELECT * FROM ticket_replies WHERE ticket_id = '$st_id' ORDER BY created_at ASC");
            if ($res_replies) {
                $ticket_replies = mysqli_fetch_all($res_replies, MYSQLI_ASSOC);
            }

            // Mark as read by user
            if (isset($selected_ticket['user_read']) && $selected_ticket['user_read'] == 0) {
                mysqli_query($conn, "UPDATE support_tickets SET user_read = 1 WHERE id = '$st_id'");
                $selected_ticket['user_read'] = 1;
            }
        }
    }
    $sql_tickets = "SELECT * FROM support_tickets WHERE customer_id = '$user_id' ORDER BY created_at DESC";
    $result_tickets = mysqli_query($conn, $sql_tickets);
    if ($result_tickets) {
        $my_tickets = mysqli_fetch_all($result_tickets, MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUSTOMER SERVICE | IMARKET PH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/services/customer_service.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .ticket-row:hover { background-color: #f8fafc !important; cursor: pointer; }
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
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 60px 20px; color: white; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1;">
            <div style="position: absolute; width: 300px; height: 300px; background: white; border-radius: 50%; top: -100px; left: -100px;"></div>
            <div style="position: absolute; width: 200px; height: 200px; background: white; border-radius: 50%; bottom: -50px; right: -80px;"></div>
        </div>
        <div style="position: relative; z-index: 2; max-width: 800px; margin: 0 auto;">
            <h1 style="font-size: 2.8rem; font-weight: 800; margin: 0 0 15px 0;">How Can We Help?</h1>
            <p style="font-size: 1.1rem; opacity: 0.95; margin: 0; line-height: 1.6;">We're here to support you 24/7. Get instant answers or connect with our expert team.</p>
        </div>
    </div>

    <!-- Service Navigation Tabs -->
    <div style="background: white; padding: 0; border-bottom: 2px solid #f1f5f9; position: sticky; top: 0; z-index: 100;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; overflow-x: auto;">
            <a href="?tab=faq" style="flex: 1; text-align: center; padding: 20px 15px; text-decoration: none; color: <?php echo $tab == 'faq' ? '#667eea' : '#64748b'; ?>; border-bottom: 3px solid <?php echo $tab == 'faq' ? '#667eea' : 'transparent'; ?>; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 150px; background: <?php echo $tab == 'faq' ? '#f3f0ff' : 'transparent'; ?>; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-lightbulb"></i> FAQs
            </a>
            <a href="?tab=chat" style="flex: 1; text-align: center; padding: 20px 15px; text-decoration: none; color: <?php echo $tab == 'chat' ? '#667eea' : '#64748b'; ?>; border-bottom: 3px solid <?php echo $tab == 'chat' ? '#667eea' : 'transparent'; ?>; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 150px; background: <?php echo $tab == 'chat' ? '#f3f0ff' : 'transparent'; ?>; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-headset"></i> Live Chat
            </a>
            <a href="?tab=submit" style="flex: 1; text-align: center; padding: 20px 15px; text-decoration: none; color: <?php echo $tab == 'submit' ? '#667eea' : '#64748b'; ?>; border-bottom: 3px solid <?php echo $tab == 'submit' ? '#667eea' : 'transparent'; ?>; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 150px; background: <?php echo $tab == 'submit' ? '#f3f0ff' : 'transparent'; ?>; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-ticket-alt"></i> New Ticket
            </a>
            <a href="?tab=history" style="flex: 1; text-align: center; padding: 20px 15px; text-decoration: none; color: <?php echo ($tab == 'history' || $tab == 'view') ? '#667eea' : '#64748b'; ?>; border-bottom: 3px solid <?php echo ($tab == 'history' || $tab == 'view') ? '#667eea' : 'transparent'; ?>; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 150px; background: <?php echo ($tab == 'history' || $tab == 'view') ? '#f3f0ff' : 'transparent'; ?>; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-history"></i> My Tickets
            </a>
            <a href="Contact Us.php" style="flex: 1; text-align: center; padding: 20px 15px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 150px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-envelope"></i> Contact
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
        <?php if (!empty($msg)): ?>
            <div style="margin-bottom: 30px;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- FAQ Tab -->
        <?php if ($tab == 'faq'): ?>
            <div>
                <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 30px; font-weight: 800;">Frequently Asked Questions</h2>
                
                <div style="display: grid; gap: 15px;">
                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" onmouseover="this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.15)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                        <div style="padding: 25px; display: flex; gap: 15px; align-items: flex-start; cursor: pointer;" onclick="this.parentElement.querySelector('.faq-answer').style.display = this.parentElement.querySelector('.faq-answer').style.display === 'none' ? 'block' : 'none'; this.querySelector('i').style.transform = this.querySelector('i').style.transform === 'rotate(180deg)' ? 'rotate(0)' : 'rotate(180deg)'">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-shipping-fast" style="color: white;"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; color: #1e293b; font-size: 1.05rem; font-weight: 700;">How long does shipping take?</h4>
                                <i class="fas fa-chevron-down" style="color: #667eea; float: right; transition: transform 0.3s;"></i>
                            </div>
                        </div>
                        <div class="faq-answer" style="padding: 0 25px 25px 55px; color: #64748b; line-height: 1.6; display: none;">
                            Standard shipping takes 3-5 business days within Metro Manila and 5-10 business days for provincial areas. Express shipping is available for orders placed before 2 PM.
                        </div>
                    </div>

                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" onmouseover="this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.15)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                        <div style="padding: 25px; display: flex; gap: 15px; align-items: flex-start; cursor: pointer;" onclick="this.parentElement.querySelector('.faq-answer').style.display = this.parentElement.querySelector('.faq-answer').style.display === 'none' ? 'block' : 'none'; this.querySelector('i').style.transform = this.querySelector('i').style.transform === 'rotate(180deg)' ? 'rotate(0)' : 'rotate(180deg)'">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #834d9b, #d64545); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-undo" style="color: white;"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; color: #1e293b; font-size: 1.05rem; font-weight: 700;">What is the return policy?</h4>
                                <i class="fas fa-chevron-down" style="color: #667eea; float: right; transition: transform 0.3s;"></i>
                            </div>
                        </div>
                        <div class="faq-answer" style="padding: 0 25px 25px 55px; color: #64748b; line-height: 1.6; display: none;">
                            You can return items within 7 days of receipt if they are defective or damaged. Items must be in original packaging. Refunds are processed within 5-7 business days after inspection.
                        </div>
                    </div>

                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" onmouseover="this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.15)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                        <div style="padding: 25px; display: flex; gap: 15px; align-items: flex-start; cursor: pointer;" onclick="this.parentElement.querySelector('.faq-answer').style.display = this.parentElement.querySelector('.faq-answer').style.display === 'none' ? 'block' : 'none'; this.querySelector('i').style.transform = this.querySelector('i').style.transform === 'rotate(180deg)' ? 'rotate(0)' : 'rotate(180deg)'">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-key" style="color: white;"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; color: #1e293b; font-size: 1.05rem; font-weight: 700;">How do I reset my password?</h4>
                                <i class="fas fa-chevron-down" style="color: #667eea; float: right; transition: transform 0.3s;"></i>
                            </div>
                        </div>
                        <div class="faq-answer" style="padding: 0 25px 25px 55px; color: #64748b; line-height: 1.6; display: none;">
                            Go to the login page and click on "Forgot Password". Enter your email address and follow the instructions sent to your inbox. The reset link expires after 24 hours.
                        </div>
                    </div>

                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" onmouseover="this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.15)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                        <div style="padding: 25px; display: flex; gap: 15px; align-items: flex-start; cursor: pointer;" onclick="this.parentElement.querySelector('.faq-answer').style.display = this.parentElement.querySelector('.faq-answer').style.display === 'none' ? 'block' : 'none'; this.querySelector('i').style.transform = this.querySelector('i').style.transform === 'rotate(180deg)' ? 'rotate(0)' : 'rotate(180deg)'">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-credit-card" style="color: white;"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; color: #1e293b; font-size: 1.05rem; font-weight: 700;">What payment methods do you accept?</h4>
                                <i class="fas fa-chevron-down" style="color: #667eea; float: right; transition: transform 0.3s;"></i>
                            </div>
                        </div>
                        <div class="faq-answer" style="padding: 0 25px 25px 55px; color: #64748b; line-height: 1.6; display: none;">
                            We accept credit cards (Visa, Mastercard), debit cards, GCash, PayMaya, and bank transfers. All payments are processed securely with 256-bit encryption.
                        </div>
                    </div>
                </div>
            </div>

        <!-- Live Chat Tab -->
        <?php elseif ($tab == 'chat'): ?>
            <?php if ($user_id): ?>
                <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 25px;">
                    <!-- Sidebar -->
                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 25px; height: fit-content; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <h3 style="margin: 0 0 20px 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">Support Status</h3>
                        <div style="text-align: center; margin-bottom: 25px;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                <i class="fas fa-comment-dots" style="font-size: 28px; color: white;"></i>
                            </div>
                            <div style="background: #dcfce7; color: #059669; padding: 8px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; display: inline-block; margin-bottom: 15px;">
                                <i class="fas fa-circle" style="font-size: 8px; margin-right: 6px;"></i> Online Now
                            </div>
                            <p style="margin: 0; color: #64748b; font-size: 14px;">Our team is available to help you 24/7</p>
                        </div>
                        <div style="border-top: 1px solid #f1f5f9; padding-top: 20px;">
                            <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin-bottom: 10px;">Response time</div>
                            <div style="background: #f3f0ff; padding: 12px; border-radius: 6px; text-align: center; color: #667eea; font-weight: 600; font-size: 14px;">
                                ~2 Minutes
                            </div>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div style="display: flex; flex-direction: column; background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <div id="chat-messages" class="chat-messages" style="flex: 1; overflow-y: auto; padding: 25px; display: flex; flex-direction: column; gap: 15px; background: linear-gradient(to bottom, #fafafa, #ffffff);">
                            <div style="display: flex; gap: 12px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-headset" style="color: white; font-size: 16px;"></i>
                                </div>
                                <div style="background: #f3f0ff; padding: 12px 16px; border-radius: 12px 12px 0 12px; color: #667eea; font-size: 14px; line-height: 1.5;">
                                    <strong>Support Team</strong><br>
                                    Hello! I'm your I-Market support assistant. How can I help you today?<br>
                                    <span style="font-size: 12px; opacity: 0.7; margin-top: 6px; display: block;">Just now</span>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 20px; background: #fafafa; border-top: 1px solid #f1f5f9; display: flex; gap: 12px;">
                            <input type="text" id="chat-input" class="chat-input" placeholder="Type your message..." autocomplete="off" style="flex: 1; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; outline: none; transition: all 0.3s;" onkeypress="if(event.key === 'Enter') sendMessage()">
                            <button onclick="sendMessage()" style="padding: 14px 24px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    const chatInput = document.getElementById('chat-input');
                    const chatMessages = document.getElementById('chat-messages');
                    
                    async function loadHistory() {
                        try {
                            const res = await fetch('../php/get_chat_history.php?store_name=Customer Support');
                            const data = await res.json();
                            if (data.success) {
                                chatMessages.innerHTML = '';
                                if (data.messages.length === 0) {
                                    chatMessages.innerHTML = `
                                        <div style="display: flex; gap: 12px;">
                                            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <i class="fas fa-headset" style="color: white; font-size: 16px;"></i>
                                            </div>
                                            <div style="background: #f3f0ff; padding: 12px 16px; border-radius: 12px 12px 0 12px; color: #667eea; font-size: 14px;">Hello! This is I-Market Live Support. How can we assist you today?</div>
                                        </div>`;
                                }
                                data.messages.forEach(m => {
                                    const isCustomer = m.sender_type === 'customer';
                                    chatMessages.innerHTML += `
                                        <div style="display: flex; gap: 12px; flex-direction: ${isCustomer ? 'row-reverse' : 'row'};">
                                            <div style="width: 36px; height: 36px; background: ${isCustomer ? '#e0e7ff' : 'linear-gradient(135deg, #667eea, #764ba2)'}; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;font-size: 16px;">
                                                <i class="fas ${isCustomer ? 'fa-user' : 'fa-headset'}" style="color: ${isCustomer ? '#667eea' : 'white'};"></i>
                                            </div>
                                            <div style="background: ${isCustomer ? '#e0e7ff' : '#f3f0ff'}; padding: 12px 16px; border-radius: ${isCustomer ? '12px 12px 12px 0' : '12px 12px 0 12px'}; color: ${isCustomer ? '#1e40af' : '#667eea'}; font-size: 14px; max-width: 70%;">
                                                ${m.message}<br><span style="font-size: 11px; opacity: 0.7; display: block; margin-top: 5px;">${m.timestamp}</span>
                                            </div>
                                        </div>`;
                                });
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        } catch (e) { console.error(e); }
                    }

                    async function sendMessage() {
                        const text = chatInput.value.trim();
                        if (!text) return;
                        chatInput.value = '';

                        const now = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        chatMessages.innerHTML += `
                            <div style="display: flex; gap: 12px; flex-direction: row-reverse;">
                                <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-user" style="color: #667eea;"></i>
                                </div>
                                <div style="background: #e0e7ff; padding: 12px 16px; border-radius: 12px 12px 12px 0; color: #1e40af; font-size: 14px; max-width: 70%;">
                                    ${text}<br><span style="font-size: 11px; opacity: 0.7; display: block; margin-top: 5px;">${now}</span>
                                </div>
                            </div>`;
                        chatMessages.scrollTop = chatMessages.scrollHeight;

                        const formData = new FormData();
                        formData.append('message', text);
                        formData.append('store_name', 'Customer Support');

                        try {
                            await fetch('../php/send_chat_message.php', { method: 'POST', body: formData });
                            setTimeout(loadHistory, 1000);
                        } catch (e) { console.error(e); }
                    }

                    loadHistory();
                    setInterval(loadHistory, 5000);
                </script>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);">
                        <i class="fas fa-lock" style="font-size: 40px; color: white;"></i>
                    </div>
                    <h3 style="margin: 0 0 10px 0; font-size: 1.4rem; color: #1e293b; font-weight: 700;">Login Required</h3>
                    <p style="color: #64748b; margin: 0 0 25px 0; font-size: 1.05rem;">Please log in to start a live support session with our team.</p>
                    <a href="../Admin/login.php" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;">Login Now</a>
                </div>
            <?php endif; ?>

        <!-- Submit Ticket Tab -->
        <?php elseif ($tab == 'submit'): ?>
            <?php if ($user_id): ?>
                <div style="max-width: 700px;">
                    <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 30px; font-weight: 800;">Submit a Support Ticket</h2>
                    
                    <form action="?tab=submit" method="POST" style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 35px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px; font-size: 1rem;">Category <span style="color: #ef4444;">*</span></label>
                            <select name="category" required style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; background: white; color: #1e293b; cursor: pointer; transition: all 0.3s;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                <option value="">Select a Category</option>
                                <option value="Order Issue">Order Issue</option>
                                <option value="Product Inquiry">Product Inquiry</option>
                                <option value="Account & Login">Account & Login</option>
                                <option value="Delivery">Delivery</option>
                                <option value="Returns & Refunds">Returns & Refunds</option>
                                <option value="Technical Issue">Technical Issue</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px; font-size: 1rem;">Subject <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="subject" placeholder="Brief summary of your issue" required style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; transition: all 0.3s; box-sizing: border-box;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px; font-size: 1rem;">Message <span style="color: #ef4444;">*</span></label>
                            <textarea name="message" rows="7" placeholder="Describe your issue in detail. Include order numbers, product names, or any relevant information..." required style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; transition: all 0.3s; box-sizing: border-box;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"></textarea>
                        </div>

                        <button type="submit" name="submit_ticket" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(102, 126, 234, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <i class="fas fa-paper-plane"></i> Submit Ticket
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);">
                        <i class="fas fa-lock" style="font-size: 40px; color: white;"></i>
                    </div>
                    <h3 style="margin: 0 0 10px 0; font-size: 1.4rem; color: #1e293b; font-weight: 700;">Login Required</h3>
                    <p style="color: #64748b; margin: 0 0 25px 0; font-size: 1.05rem;">Please log in to submit a support ticket.</p>
                    <a href="../Admin/login.php" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;">Login Now</a>
                </div>
            <?php endif; ?>

        <!-- Ticket History Tab -->
        <?php elseif ($tab == 'history'): ?>
            <?php if ($selected_ticket): ?>
                <div style="margin-bottom: 2rem;">
                    <a href="?tab=history" style="color: #667eea; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 2rem; transition: all 0.3s;" onmouseover="this.style.gap='12px'" onmouseout="this.style.gap='8px'">
                        <i class="fas fa-arrow-left"></i> Back to My Tickets
                    </a>
                    
                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <div style="padding: 25px; background: linear-gradient(135deg, #f3f0ff 0%, #fafafa 100%); border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2 style="margin: 0 0 8px 0; color: #1e293b; font-size: 1.4rem; font-weight: 700;">Ticket #<?php echo $selected_ticket['ticket_number']; ?></h2>
                                <span style="font-size: 14px; color: #64748b;">Category: <strong><?php echo htmlspecialchars($selected_ticket['category']); ?></strong></span>
                            </div>
                            <span class="badge status-<?php echo strtolower(str_replace(' ', '', $selected_ticket['status'])); ?>" style="padding: 10px 18px; font-size: 13px; font-weight: 600; background: <?php echo $selected_ticket['status'] == 'Open' ? '#dcfce7' : ($selected_ticket['status'] == 'Resolved' ? '#d1f4ff' : '#fed7aa'); ?>; color: <?php echo $selected_ticket['status'] == 'Open' ? '#059669' : ($selected_ticket['status'] == 'Resolved' ? '#0369a1' : '#b45309'); ?>; border-radius: 6px;">
                                <?php echo $selected_ticket['status']; ?>
                            </span>
                        </div>
                        
                        <div style="padding: 30px;">
                            <div class="ticket-section" style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #f1f5f9;">
                                <h4 style="margin: 0 0 15px 0; font-size: 0.9rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Subject</h4>
                                <div style="font-size: 1.2rem; font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($selected_ticket['subject']); ?></div>
                                <div style="font-size: 13px; color: #64748b; margin-top: 8px;"><?php echo date('F d, Y \a\t h:i A', strtotime($selected_ticket['created_at'])); ?></div>
                            </div>

                            <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #f1f5f9;">
                                <h4 style="margin: 0 0 15px 0; font-size: 0.9rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Your Message</h4>
                                <div style="background: #f8fafc; padding: 18px; border-radius: 8px; border-left: 4px solid #667eea; color: #334155; line-height: 1.7;">
                                    <?php echo nl2br(htmlspecialchars($selected_ticket['message'])); ?>
                                </div>
                            </div>

                            <?php if (!empty($selected_ticket['admin_reply']) || !empty($ticket_replies)): ?>
                                <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #f1f5f9;">
                                    <h4 style="margin: 0 0 20px 0; font-size: 0.9rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Conversation</h4>
                                    
                                    <?php if (!empty($selected_ticket['admin_reply'])): ?>
                                        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <i class="fas fa-headset" style="color: white; font-size: 18px;"></i>
                                            </div>
                                            <div style="flex: 1; background: #f3f0ff; padding: 18px; border-radius: 0 12px 12px 12px; border: 1px solid #e9d5ff;">
                                                <strong style="color: #667eea;">Support Team</strong><br>
                                                <div style="margin-top: 8px; color: #5a67d8; line-height: 1.6;">
                                                    <?php echo nl2br(htmlspecialchars($selected_ticket['admin_reply'])); ?>
                                                </div>
                                                <div style="margin-top: 10px; font-size: 12px; color: #94a3b8;">
                                                    <?php echo date('M d, Y h:i A', strtotime($selected_ticket['updated_at'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php foreach ($ticket_replies as $reply): ?>
                                        <?php $is_support = ($reply['sender_type'] == 'admin'); ?>
                                        <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-direction: <?php echo $is_support ? 'row' : 'row-reverse'; ?>;">
                                            <div style="width: 40px; height: 40px; background: <?php echo $is_support ? 'linear-gradient(135deg, #667eea, #764ba2)' : '#e0e7ff'; ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <i class="fas <?php echo $is_support ? 'fa-headset' : 'fa-user'; ?>" style="color: <?php echo $is_support ? 'white' : '#667eea'; ?>; font-size: 18px;"></i>
                                            </div>
                                            <div style="flex: 1; background: <?php echo $is_support ? '#f3f0ff' : '#e0e7ff'; ?>; padding: 18px; border-radius: <?php echo $is_support ? '0 12px 12px 12px' : '12px 12px 12px 0'; ?>; border: 1px solid <?php echo $is_support ? '#e9d5ff' : '#bfdbfe'; ?>;">
                                                <strong style="color: <?php echo $is_support ? '#667eea' : '#1e40af'; ?>"><?php echo $is_support ? 'Support Team' : 'You'; ?></strong><br>
                                                <div style="margin-top: 8px; color: <?php echo $is_support ? '#5a67d8' : '#1e40af'; ?>; line-height: 1.6;">
                                                    <?php echo nl2br(htmlspecialchars($reply['message'])); ?>
                                                </div>
                                                <div style="margin-top: 10px; font-size: 12px; color: #94a3b8;">
                                                    <?php echo date('M d, Y h:i A', strtotime($reply['created_at'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($selected_ticket['status'] != 'Closed'): ?>
                                <div style="padding-top: 30px;">
                                    <h4 style="margin: 0 0 20px 0; font-size: 0.9rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Send a Reply</h4>
                                    <form action="?tab=history&view=<?php echo $selected_ticket['ticket_number']; ?>" method="POST">
                                        <input type="hidden" name="ticket_id" value="<?php echo $selected_ticket['id']; ?>">
                                        <textarea name="reply_message" rows="5" placeholder="Type your reply here..." required style="width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 14px; resize: vertical; margin-bottom: 15px; transition: all 0.3s; box-sizing: border-box;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"></textarea>
                                        <button type="submit" name="submit_reply" style="padding: 12px 28px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(102, 126, 234, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                            <i class="fas fa-reply"></i> Send Reply
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #94a3b8; color: #64748b; font-size: 14px; text-align: center;">
                                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i> This ticket is closed and cannot be replied to.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 30px; font-weight: 800;">My Support Tickets</h2>
                <?php if ($user_id && count($my_tickets) > 0): ?>
                    <div style="display: grid; gap: 15px;">
                        <?php foreach ($my_tickets as $ticket): ?>
                            <div class="ticket-card" onclick="window.location.href='?tab=history&view=<?php echo $ticket['ticket_number']; ?>'" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" onmouseover="this.style.boxShadow='0 12px 30px rgba(102, 126, 234, 0.15)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                                <div style="display: flex; justify-content: space-between; align-items: start; gap: 15px;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                            <span style="font-size: 13px; color: #667eea; font-weight: 700; text-transform: uppercase;">Ticket #<?php echo $ticket['ticket_number']; ?></span>
                                            <span style="font-size: 12px; background: #f3f0ff; color: #667eea; padding: 4px 10px; border-radius: 4px; font-weight: 600;"><?php echo htmlspecialchars($ticket['category']); ?></span>
                                        </div>
                                        <h3 style="margin: 0 0 8px 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($ticket['subject']); ?></h3>
                                        <p style="margin: 0; color: #64748b; font-size: 14px;"><?php echo date('F d, Y', strtotime($ticket['created_at'])); ?></p>
                                    </div>
                                    <span style="padding: 8px 14px; font-size: 12px; font-weight: 600; background: <?php echo $ticket['status'] == 'Open' ? '#dcfce7' : ($ticket['status'] == 'Resolved' ? '#d1f4ff' : '#fed7aa'); ?>; color: <?php echo $ticket['status'] == 'Open' ? '#059669' : ($ticket['status'] == 'Resolved' ? '#0369a1' : '#b45309'); ?>; border-radius: 6px; white-space: nowrap;">
                                        <?php echo $ticket['status']; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);">
                            <i class="fas fa-inbox" style="font-size: 40px; color: white;"></i>
                        </div>
                        <h3 style="margin: 0 0 10px 0; font-size: 1.4rem; color: #1e293b; font-weight: 700;">No Tickets Yet</h3>
                        <p style="color: #64748b; margin: 0 0 25px 0; font-size: 1rem;">You haven't submitted any support tickets yet. If you need help, feel free to submit one!</p>
                        <a href="?tab=submit" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;">Submit a Ticket</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <footer style="margin-top: 80px;">
        <?php include '../Components/footer.php'; ?>
    </footer>
</body>
</html>
