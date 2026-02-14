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
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_return'])) {
    if (!$user_id) {
        $msg = "<div class='alert alert-error' style='background:#f8d7da; color:#721c24; padding:15px; margin-bottom:20px; border-radius:8px;'>Please login to request a return.</div>";
    } else {
        $order_id = clean_input($_POST['order_id']);
        $product_name = clean_input($_POST['product_name']);
        $reason = clean_input($_POST['reason']);
        $details = clean_input($_POST['details']);

        // Handle File Upload
        $image_proof = NULL;
        if (isset($_FILES['image_proof']) && $_FILES['image_proof']['error'] == 0) {
            $target_dir = "../image/Returns/";
            // Create dir if not exists
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES["image_proof"]["name"], PATHINFO_EXTENSION));
            $new_filename = "return_" . time() . "_" . $user_id . "." . $file_extension;
            $target_file = $target_dir . $new_filename;

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_extension, $allowed_types)) {
                if (move_uploaded_file($_FILES["image_proof"]["tmp_name"], $target_file)) {
                    $image_proof = 'image/Returns/' . $new_filename; // Store relative path for DB
                } else {
                    $msg = "<div class='alert alert-error'>Failed to upload image.</div>";
                }
            } else {
                $msg = "<div class='alert alert-error'>Invalid file type. Only JPG, PNG, GIF are allowed.</div>";
            }
        }

        if (empty($msg)) {
            $sql = "INSERT INTO return_refund_requests (user_id, order_id, product_name, reason, details, image_proof) 
                    VALUES ('$user_id', '$order_id', '$product_name', '$reason', '$details', '$image_proof')";

            if (mysqli_query($conn, $sql)) {
                $msg = "<div class='alert alert-success' style='background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border-radius:8px;'>Return Request Submitted Successfully!</div>";
            } else {
                $msg = "<div class='alert alert-error' style='background:#f8d7da; color:#721c24; padding:15px; margin-bottom:20px; border-radius:8px;'>Error: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}

// Fetch Previous Requests
$my_requests = [];
if ($user_id) {
    // Check if table exists first to avoid error if SQL not imported yet
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'return_refund_requests'");
    if (mysqli_num_rows($check_table) > 0) {
        $sql_requests = "SELECT * FROM return_refund_requests WHERE user_id = '$user_id' ORDER BY created_at DESC";
        $result_requests = mysqli_query($conn, $sql_requests);
        if ($result_requests) {
            $my_requests = mysqli_fetch_all($result_requests, MYSQLI_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return & Refund | IMARKET PH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/services/return_refund.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <nav>
        <?php
        $path_prefix = '../';
        include '../Components/header.php';
        ?>
    </nav>

    <!-- Modern Hero Section -->
    <div style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%); padding: 70px 20px; color: white; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1;">
            <div style="position: absolute; width: 400px; height: 400px; background: white; border-radius: 50%; top: -150px; right: -150px;"></div>
            <div style="position: absolute; width: 300px; height: 300px; background: white; border-radius: 50%; bottom: -100px; left: -100px;"></div>
        </div>
        <div style="position: relative; z-index: 2; max-width: 900px; margin: 0 auto;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 20px;">
                <i class="fas fa-undo-alt" style="font-size: 2.5rem;"></i>
                <h1 style="font-size: 3rem; font-weight: 800; margin: 0;">Easy Returns & Refunds</h1>
            </div>
            <p style="font-size: 1.15rem; opacity: 0.95; margin: 0; line-height: 1.6;">We make returns hassle-free. If you're not satisfied, we'll help you get a refund quickly and easily.</p>
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
            <a href="Return & Refund.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #06b6d4; border-bottom: 3px solid #06b6d4; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap; background: #ecf8fa;">
                <i class="fas fa-undo-alt" style="font-size: 16px;"></i> Returns
            </a>
            <a href="Contact Us.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-envelope" style="font-size: 16px;"></i> Contact
            </a>
            <a href="Customer_Service.php?tab=history" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-headset" style="font-size: 16px;"></i> Support
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 50px 20px;">
        <?php if (!empty($msg)): ?>
            <div style="margin-bottom: 30px;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- Return Policy Cards -->
        <div style="margin-bottom: 60px;">
            <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 12px; font-weight: 800;">Return Policy</h2>
            <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 35px;">Everything you need to know about returns and refunds</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
                <div style="background: white; border-radius: 16px; border: 2px solid #cffafe; overflow: hidden; transition: all 0.3s; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.08);" onmouseover="this.style.boxShadow='0 15px 40px rgba(6, 182, 212, 0.2)'; this.style.transform='translateY(-8px)'; this.style.borderColor='#06b6d4'" onmouseout="this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.08)'; this.style.transform='translateY(0)'; this.style.borderColor='#cffafe'">
                    <div style="background: linear-gradient(135deg, #ecf8fa 0%, #cffafe 100%); padding: 25px; text-align: center;">
                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3);">
                            <i class="fas fa-calendar" style="color: white; font-size: 32px;"></i>
                        </div>
                        <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 700;">7-Day Window</h3>
                    </div>
                    <div style="padding: 25px;">
                        <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;">Return items within 7 days of delivery for a full refund. Items must be unused and in original condition.</p>
                    </div>
                </div>

                <div style="background: white; border-radius: 16px; border: 2px solid #cffafe; overflow: hidden; transition: all 0.3s; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.08);" onmouseover="this.style.boxShadow='0 15px 40px rgba(6, 182, 212, 0.2)'; this.style.transform='translateY(-8px)'; this.style.borderColor='#06b6d4'" onmouseout="this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.08)'; this.style.transform='translateY(0)'; this.style.borderColor='#cffafe'">
                    <div style="background: linear-gradient(135deg, #ecf8fa 0%, #cffafe 100%); padding: 25px; text-align: center;">
                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3);">
                            <i class="fas fa-box" style="color: white; font-size: 32px;"></i>
                        </div>
                        <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 700;">Original Packaging</h3>
                    </div>
                    <div style="padding: 25px;">
                        <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;">Items must come in original packaging with all tags attached and in unused condition.</p>
                    </div>
                </div>

                <div style="background: white; border-radius: 16px; border: 2px solid #cffafe; overflow: hidden; transition: all 0.3s; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.08);" onmouseover="this.style.boxShadow='0 15px 40px rgba(6, 182, 212, 0.2)'; this.style.transform='translateY(-8px)'; this.style.borderColor='#06b6d4'" onmouseout="this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.08)'; this.style.transform='translateY(0)'; this.style.borderColor='#cffafe'">
                    <div style="background: linear-gradient(135deg, #ecf8fa 0%, #cffafe 100%); padding: 25px; text-align: center;">
                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3);">
                            <i class="fas fa-wallet" style="color: white; font-size: 32px;"></i>
                        </div>
                        <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 700;">Quick Refunds</h3>
                    </div>
                    <div style="padding: 25px;">
                        <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;">Refunds processed within 5-10 business days to your original payment method after approval.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Request Form -->
        <?php if ($user_id): ?>
            <div style="max-width: 800px; margin: 0 auto 60px;">
                <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 30px; font-weight: 800;">Submit Return Request</h2>
                
                <form action="" method="POST" enctype="multipart/form-data" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                        <div>
                            <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Order ID <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="order_id" placeholder="e.g., ORD-12345" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s;" onfocus="this.style.borderColor='#06b6d4'; this.style.boxShadow='0 0 0 3px rgba(6, 182, 212, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Product Name / SKU <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="product_name" placeholder="Item to return" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s;" onfocus="this.style.borderColor='#06b6d4'; this.style.boxShadow='0 0 0 3px rgba(6, 182, 212, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Reason for Return <span style="color: #ef4444;">*</span></label>
                        <select name="reason" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; background: white;" onfocus="this.style.borderColor='#06b6d4'; this.style.boxShadow='0 0 0 3px rgba(6, 182, 212, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <option value="">Select a Reason</option>
                            <option value="Damaged">Damaged / Defective</option>
                            <option value="Wrong Item">Received Wrong Item</option>
                            <option value="Incomplete">Incomplete / Missing Parts</option>
                            <option value="Changed Mind">Changed Mind (Unopened)</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Additional Details <span style="color: #ef4444;">*</span></label>
                        <textarea name="details" rows="5" required placeholder="Please provide more details about the issue..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; font-family: inherit; resize: vertical;" onfocus="this.style.borderColor='#06b6d4'; this.style.boxShadow='0 0 0 3px rgba(6, 182, 212, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"></textarea>
                    </div>

                    <div style="margin-bottom: 30px;">
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 14px;">Upload Proof (Image)</label>
                        <input type="file" name="image_proof" accept="image/*" style="display: block; width: 100%; padding: 12px; border: 2px dashed #cffafe; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <small style="color: #64748b; display: block; margin-top: 8px;">Recommended for damaged or wrong items</small>
                    </div>

                    <button type="submit" name="submit_return" style="width: 100%; padding: 14px 20px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(6, 182, 212, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.2)'">
                        <i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Submit Return Request
                    </button>
                </form>
            </div>

            <!-- Return History -->
            <?php if (!empty($my_requests)): ?>
                <div style="margin-bottom: 60px;">
                    <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 30px; font-weight: 800;">My Return Requests</h2>
                    
                    <div style="display: grid; gap: 20px;">
                        <?php foreach ($my_requests as $req): ?>
                            <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 24px rgba(6, 182, 212, 0.15)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                                <div style="display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: start;">
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                            <h3 style="margin: 0; color: #1e293b; font-size: 1.1rem; font-weight: 700;">#<?php echo $req['request_id']; ?> - <?php echo htmlspecialchars($req['product_name']); ?></h3>
                                        </div>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                                            <div>
                                                <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Order ID</span>
                                                <p style="margin: 5px 0 0 0; color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($req['order_id']); ?></p>
                                            </div>
                                            <div>
                                                <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Reason</span>
                                                <p style="margin: 5px 0 0 0; color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($req['reason']); ?></p>
                                            </div>
                                            <div>
                                                <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Date</span>
                                                <p style="margin: 5px 0 0 0; color: #1e293b; font-weight: 600;"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <?php
                                        $statusColor = '#059669';
                                        $statusBg = '#dcfce7';
                                        $statusText = $req['status'];
                                        if ($req['status'] == 'Pending') {
                                            $statusColor = '#f59e0b';
                                            $statusBg = '#fef3c7';
                                        } elseif ($req['status'] == 'Rejected') {
                                            $statusColor = '#ef4444';
                                            $statusBg = '#fee2e2';
                                        } elseif ($req['status'] == 'Refunded') {
                                            $statusColor = '#3b82f6';
                                            $statusBg = '#dbeafe';
                                        }
                                        ?>
                                        <div style="background: <?php echo $statusBg; ?>; border: 2px solid <?php echo $statusColor; ?>; color: <?php echo $statusColor; ?>; padding: 10px 16px; border-radius: 8px; text-align: center; font-weight: 700; font-size: 13px; white-space: nowrap;">
                                            <?php echo $statusText; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 8px 25px rgba(6, 182, 212, 0.2);">
                    <i class="fas fa-lock" style="color: white; font-size: 40px;"></i>
                </div>
                <h3 style="margin: 0 0 10px 0; font-size: 1.4rem; color: #1e293b; font-weight: 700;">Login Required</h3>
                <p style="color: #64748b; margin: 0 0 25px 0; font-size: 1.05rem;">Sign in to your account to submit return requests and track their status.</p>
                <a href="../Admin/login.php" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(6, 182, 212, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">Login Now</a>
            </div>
        <?php endif; ?>
    </div>

    <footer style="margin-top: 80px;">
        <?php include '../Components/footer.php'; ?>
    </footer>
</body>

</html>



