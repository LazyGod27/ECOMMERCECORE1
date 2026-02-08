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

    <div class="service-container">
        <!-- Sidebar -->
        <div class="service-sidebar">
            <h3>Customer Service</h3>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="?tab=faq" class="<?php echo $tab == 'faq' ? 'active' : ''; ?>"><i class="fas fa-question-circle"></i> FAQs</a></li>
                    <li><a href="?tab=chat" class="<?php echo $tab == 'chat' ? 'active' : ''; ?>"><i class="fas fa-comments"></i> Live Support</a></li>
                    <li><a href="?tab=submit" class="<?php echo $tab == 'submit' ? 'active' : ''; ?>"><i class="fas fa-edit"></i> Submit a Ticket</a></li>
                    <li><a href="?tab=history" class="<?php echo $tab == 'history' || $tab == 'view' ? 'active' : ''; ?>"><i class="fas fa-history"></i> My Tickets</a></li>
                    <li><a href="Contact Us.php"><i class="fas fa-envelope"></i> Contact Us</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="service-content">
            <?php echo $msg; ?>

            <!-- FAQ Tab -->
            <?php if ($tab == 'faq'): ?>
                <div class="section-header">
                    <h2>Frequently Asked Questions</h2>
                    <p>Find quick answers to common questions.</p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-shipping-fast"></i> How long does shipping take?</h4>
                    <p>Standard shipping takes 3-5 business days within Metro Manila and 5-10 business days for provincial areas.</p>
                </div>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                <div class="faq-item">
                    <h4><i class="fas fa-undo"></i> What is the return policy?</h4>
                    <p>You can return items within 7 days of receipt if they are defective or damaged.</p>
                </div>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                <div class="faq-item">
                    <h4><i class="fas fa-user-lock"></i> How do I reset my password?</h4>
                    <p>Go to the login page and click on "Forgot Password". Follow the instructions sent to your email.</p>
                </div>

            <!-- Live Chat Tab -->
            <?php elseif ($tab == 'chat'): ?>
                <div class="section-header">
                    <h2>Live Support Chat</h2>
                    <p>Chat with our support agents in real-time.</p>
                </div>
                <?php if ($user_id): ?>
                    <div class="chat-welcome">
                        <i class="fas fa-headset" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                        <strong>Welcome to Live Support!</strong><br>
                        Our agents are online and ready to help you with your inquiries.
                    </div>
                    <div class="chat-container">
                        <div id="chat-messages" class="chat-messages">
                            <div class="chat-bubble bubble-support">
                                Hello! I'm your I-Market support assistant. How can I help you today?
                                <span class="msg-time">Just now</span>
                            </div>
                        </div>
                        <div class="chat-input-area">
                            <input type="text" id="chat-input" class="chat-input" placeholder="Type your message here..." autocomplete="off">
                            <button onclick="sendMessage()" class="btn-send">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                    <script>
                        const chatInput = document.getElementById('chat-input');
                        const chatMessages = document.getElementById('chat-messages');
                        
                        // Load History
                        async function loadHistory() {
                            try {
                                const res = await fetch('../php/get_chat_history.php?store_name=Customer Support');
                                const data = await res.json();
                                if (data.success) {
                                    chatMessages.innerHTML = '';
                                    if (data.messages.length === 0) {
                                        chatMessages.innerHTML = `
                                            <div class="chat-bubble bubble-support">
                                                Hello! This is I-Market Live Support. How can we assist you today?
                                                <span class="msg-time">System</span>
                                            </div>`;
                                    }
                                    data.messages.forEach(m => {
                                        const side = m.sender_type === 'customer' ? 'customer' : 'support';
                                        chatMessages.innerHTML += `
                                            <div class="chat-bubble bubble-${side}">
                                                ${m.message}
                                                <span class="msg-time">${m.timestamp}</span>
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

                            // Optimistic UI
                            const now = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                            chatMessages.innerHTML += `
                                <div class="chat-bubble bubble-customer">
                                    ${text}
                                    <span class="msg-time">${now}</span>
                                </div>`;
                            chatMessages.scrollTop = chatMessages.scrollHeight;

                            const formData = new FormData();
                            formData.append('message', text);
                            formData.append('store_name', 'Customer Support');

                            try {
                                await fetch('../php/send_chat_message.php', { method: 'POST', body: formData });
                                loadHistory(); // Refresh
                            } catch (e) { console.error(e); }
                        }

                        chatInput.addEventListener('keypress', (e) => {
                            if (e.key === 'Enter') sendMessage();
                        });

                        loadHistory();
                        setInterval(loadHistory, 5000); // Polling every 5s
                    </script>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-lock"></i>
                        <h3>Login Required</h3>
                        <p>Please log in to start a live support session.</p>
                    </div>
                <?php endif; ?>

            <!-- Submit Ticket Tab -->
            <?php elseif ($tab == 'submit'): ?>
                <div class="section-header">
                    <h2>Submit a Support Ticket</h2>
                    <p>Tell us about your issue and we'll help you resolve it.</p>
                </div>
                <?php if ($user_id): ?>
                    <form action="?tab=submit" method="POST" class="ticket-form">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="">Select a Category</option>
                                <option value="Order Issue">Order Issue</option>
                                <option value="Product Inquiry">Product Inquiry</option>
                                <option value="Account & Login">Account & Login</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" placeholder="Brief summary" required>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" rows="6" placeholder="Details..." required></textarea>
                        </div>
                        <button type="submit" name="submit_ticket" class="btn-submit">Submit Ticket <i class="fas fa-paper-plane"></i></button>
                    </form>
                <?php else: ?>
                    <div class="empty-state"><i class="fas fa-lock"></i><h3>Login Required</h3></div>
                <?php endif; ?>

            <!-- Ticket History Tab -->
            <?php elseif ($tab == 'history'): ?>
                <?php if ($selected_ticket): ?>
                    <div style="margin-bottom: 2rem;">
                        <a href="?tab=history" style="color: #2A3B7E; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                            <i class="fas fa-arrow-left"></i> Back to My Tickets
                        </a>
                        
                        <div style="background: white; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
                            <div style="padding: 20px; background: #fafafa; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="margin: 0; color: #1e293b;">Ticket #<?php echo $selected_ticket['ticket_number']; ?></h3>
                                    <span style="font-size: 13px; color: #64748b;"><?php echo date('F d, Y h:i A', strtotime($selected_ticket['created_at'])); ?></span>
                                </div>
                                <span class="badge status-<?php echo strtolower(str_replace(' ', '', $selected_ticket['status'])); ?>" style="padding: 6px 12px; font-size: 13px;">
                                    <?php echo $selected_ticket['status']; ?>
                                </span>
                            </div>
                            
                            <div style="padding: 25px;">
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Subject</label>
                                    <div style="font-size: 16px; font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($selected_ticket['subject']); ?></div>
                                </div>

                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Initial Message</label>
                                    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #f1f5f9; color: #334155; line-height: 1.6;">
                                        <?php echo nl2br(htmlspecialchars($selected_ticket['message'])); ?>
                                    </div>
                                </div>

                                <!-- THREADED REPLIES -->
                                <?php if (!empty($selected_ticket['admin_reply'])): ?>
                                    <div style="margin-bottom: 20px;">
                                        <div style="display: flex; gap: 12px; margin-bottom: 15px;">
                                            <div style="width: 35px; height: 35px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><i class="fas fa-headset"></i></div>
                                            <div style="background: #eff6ff; padding: 15px; border-radius: 0 12px 12px 12px; border: 1px solid #dbeafe; color: #1e40af; line-height: 1.6; position: relative; flex: 1;">
                                                <strong>Support Team:</strong><br>
                                                <?php echo nl2br(htmlspecialchars($selected_ticket['admin_reply'])); ?>
                                                <div style="margin-top: 5px; font-size: 11px; color: #60a5fa; text-align: right;">
                                                    <?php echo date('M d, h:i A', strtotime($selected_ticket['updated_at'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php foreach ($ticket_replies as $reply): ?>
                                    <?php 
                                        $is_support = ($reply['sender_type'] == 'admin');
                                        $bg = $is_support ? '#eff6ff' : '#f8fafc';
                                        $border = $is_support ? '#dbeafe' : '#f1f5f9';
                                        $color = $is_support ? '#1e40af' : '#334155';
                                        $radius = $is_support ? '0 12px 12px 12px' : '12px 0 12px 12px';
                                        $icon = $is_support ? 'fa-headset' : 'fa-user';
                                        $align = $is_support ? 'flex-start' : 'flex-end';
                                    ?>
                                    <div style="display: flex; gap: 12px; margin-bottom: 15px; flex-direction: <?php echo $is_support ? 'row' : 'row-reverse'; ?>;">
                                        <div style="width: 35px; height: 35px; border-radius: 50%; background: <?php echo $bg; ?>; color: <?php echo $is_support ? '#3b82f6' : '#94a3b8'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><i class="fas <?php echo $icon; ?>"></i></div>
                                        <div style="background: <?php echo $bg; ?>; padding: 15px; border-radius: <?php echo $radius; ?>; border: 1px solid <?php echo $border; ?>; color: <?php echo $color; ?>; line-height: 1.6; position: relative; max-width: 80%;">
                                            <strong><?php echo $is_support ? 'Support Team' : 'You'; ?>:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($reply['message'])); ?>
                                            <div style="margin-top: 5px; font-size: 11px; color: #94a3b8; text-align: right;">
                                                <?php echo date('M d, h:i A', strtotime($reply['created_at'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- REPLY FORM -->
                                <?php if ($selected_ticket['status'] != 'Closed'): ?>
                                    <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #f1f5f9;">
                                        <h4 style="margin: 0 0 15px 0; font-size: 15px; color: #1e293b;"><i class="fas fa-reply"></i> Send a Reply</h4>
                                        <form action="?tab=history&view=<?php echo $selected_ticket['ticket_number']; ?>" method="POST">
                                            <input type="hidden" name="ticket_id" value="<?php echo $selected_ticket['id']; ?>">
                                            <textarea name="reply_message" rows="4" placeholder="Type your reply here..." style="width: 100%; padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; font-family: inherit; resize: vertical;" required></textarea>
                                            <button type="submit" name="submit_reply" class="btn-submit" style="width: auto; padding: 10px 25px;">Send Reply</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div style="margin-top: 30px; text-align: center; padding: 20px; background: #f8fafc; border-radius: 8px; color: #64748b; font-size: 14px;">
                                        This ticket is closed and cannot be replied to.
                                    </div>
                                <?php endif; ?>

                                <?php if ($selected_ticket['status'] != 'Resolved' && $selected_ticket['status'] != 'Closed'): ?>
                                    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: center;">
                                        <a href="?tab=chat" style="display: flex; align-items: center; gap: 8px; padding: 8px 15px; background: transparent; border: 1px solid #2A3B7E; color: #2A3B7E; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 13px;">
                                            <i class="fas fa-comments"></i> Switch to Live Chat
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="section-header"><h2>My Support Tickets</h2></div>
                    <?php if ($user_id && count($my_tickets) > 0): ?>
                        <table class="ticket-list">
                            <thead><tr><th>ID</th><th>Date</th><th>Category</th><th>Subject</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($my_tickets as $ticket): ?>
                                    <tr class="ticket-row" onclick="window.location.href='?tab=history&view=<?php echo $ticket['ticket_number']; ?>'">
                                        <td>#<?php echo $ticket['ticket_number']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['category']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                                        <td><span class="badge status-<?php echo strtolower(str_replace(' ', '', $ticket['status'])); ?>"><?php echo $ticket['status']; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state"><i class="fas fa-ticket-alt"></i><p>No tickets found.</p></div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer style="margin-top: 50px;">
        <?php include '../Components/footer.php'; ?>
    </footer>
</body>
</html>
