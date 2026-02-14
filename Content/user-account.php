    <?php
include_once('../Components/security.php'); 
include_once('../Database/config.php');
// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$view = isset($_GET['view']) ? $_GET['view'] : 'profile'; // profile, orders, tracking

// ---------------------------------------------------------
// SELF-HEALING DB: Ensure 'users' table has profile columns
// ---------------------------------------------------------
if (isset($conn)) {
    // Check for 'profile_pic' column
    $pic_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'profile_pic'");
    if (mysqli_num_rows($pic_check) == 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL");
    }

    // Check for 'address' column as a proxy for the update
    $cols_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'address'");
    if (mysqli_num_rows($cols_check) == 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN fullname VARCHAR(255) AFTER email");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN phone VARCHAR(50) AFTER fullname");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN address TEXT AFTER phone");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN city VARCHAR(100) AFTER address");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN zip VARCHAR(20) AFTER city");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN gender VARCHAR(20) DEFAULT 'Not Specified'");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN birthdate DATE NULL");
    }

    // Check for 'gender' specifically if added later
    $gender_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'gender'");
    if (mysqli_num_rows($gender_check) == 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN gender VARCHAR(20) DEFAULT 'Not Specified'");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN birthdate DATE NULL");
    }

    // Ensure 'user_addresses' table exists
    $create_addr_table = "CREATE TABLE IF NOT EXISTS user_addresses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        fullname VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        address TEXT NOT NULL,
        city VARCHAR(100) NOT NULL,
        zip VARCHAR(20) NOT NULL,
        is_default TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $create_addr_table);
}

// ---------------------------------------------------------
// HANDLE ADDRESS ACTIONS
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'save_address') {
        $addr_id = isset($_POST['address_id']) ? intval($_POST['address_id']) : 0;
        $fullname = $_POST['fullname'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $zip = $_POST['zip'];
        $is_default = isset($_POST['is_default']) ? 1 : 0;

        if ($is_default) {
            $stmt = $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }

        if ($addr_id > 0) {
            $stmt = $conn->prepare("UPDATE user_addresses SET fullname=?, phone=?, address=?, city=?, zip=?, is_default=? WHERE id=? AND user_id=?");
            $stmt->bind_param("sssssiis", $fullname, $phone, $address, $city, $zip, $is_default, $addr_id, $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO user_addresses (user_id, fullname, phone, address, city, zip, is_default) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $user_id, $fullname, $phone, $address, $city, $zip, $is_default);
        }
        
        if ($stmt->execute()) {
            $msg = "<div class='alert-success'>Address saved successfully!</div>";
        } else {
            $msg = "<div class='alert-error'>Error saving address: " . $conn->error . "</div>";
        }
        $stmt->close();
    }

    if ($action == 'delete_address') {
        $addr_id = intval($_POST['address_id']);
        $stmt = $conn->prepare("DELETE FROM user_addresses WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $addr_id, $user_id);
        if ($stmt->execute()) {
            $msg = "<div class='alert-success'>Address deleted successfully!</div>";
        }
        $stmt->close();
    }

    if ($action == 'set_default') {
        $addr_id = intval($_POST['address_id']);
        $stmt1 = $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
        $stmt1->bind_param("i", $user_id);
        $stmt1->execute();
        $stmt1->close();
        $stmt2 = $conn->prepare("UPDATE user_addresses SET is_default = 1 WHERE id = ? AND user_id = ?");
        $stmt2->bind_param("ii", $addr_id, $user_id);
        $stmt2->execute();
        $stmt2->close();
        $msg = "<div class='alert-success'>Default address updated!</div>";
    }
}

// ---------------------------------------------------------
// HANDLE FORM SUBMISSIONS (Profile Update)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : NULL;

    $update_sql = "UPDATE users SET fullname=?, phone=?, address=?, city=?, zip=?, gender=?"; 
    if ($birthdate) {
        $update_sql .= ", birthdate=?";
    }
    $update_sql .= " WHERE id=?";
    
    $stmt = $conn->prepare($update_sql);
    if ($birthdate) {
        $stmt->bind_param("sssssssi", $fullname, $phone, $address, $city, $zip, $gender, $birthdate, $user_id);
    } else {
        $stmt->bind_param("ssssssi", $fullname, $phone, $address, $city, $zip, $gender, $user_id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        // Handle Profile Picture Upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $target_dir = "../uploads/profile/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $new_filename = "user_" . $user_id . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                $pic_stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
                $pic_stmt->bind_param("si", $new_filename, $user_id);
                $pic_stmt->execute();
                $pic_stmt->close();
            }
        }
        $msg = "<div class='alert-success'><i class='fas fa-check-circle'></i> Profile updated successfully!</div>";
    } else {
        $msg = "<div class='alert-error'>Error updating profile: " . $conn->error . "</div>";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    $verify_stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $verify_stmt->bind_param("i", $user_id);
    $verify_stmt->execute();
    $verify_res = $verify_stmt->get_result();
    $user_data = $verify_res->fetch_assoc();
    $verify_stmt->close();

    if (password_verify($current_pass, $user_data['password'])) {
        if ($new_pass === $confirm_pass) {
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            if ($update_stmt->execute()) {
                $msg = "<div class='alert-success'>Password updated successfully!</div>";
            } else {
                $msg = "<div class='alert-error'>Error updating password.</div>";
            }
            $update_stmt->close();
        } else {
            $msg = "<div class='alert-error'>New passwords do not match.</div>";
        }
    } else {
        $msg = "<div class='alert-error'>Incorrect current password.</div>";
    }
}

// Handle Order Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel_order') {
    $order_id_to_cancel = intval($_POST['order_id']);
    $status = 'Cancelled';
    $cancel_stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=? AND user_id=?");
    $cancel_stmt->bind_param("sii", $status, $order_id_to_cancel, $user_id);
    if ($cancel_stmt->execute()) {
        $msg = "<div class='alert-success'>Order cancelled successfully.</div>";
    } else {
        $msg = "<div class='alert-error'>Error cancelling order.</div>";
    }
    $cancel_stmt->close();
}

// ---------------------------------------------------------
// FETCH USER DATA
// ---------------------------------------------------------
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

// ---------------------------------------------------------
// FETCH ADDRESSES
// ---------------------------------------------------------
$user_addresses = [];
if ($view == 'address') {
    $addr_stmt = $conn->prepare("SELECT * FROM user_addresses WHERE user_id=? ORDER BY is_default DESC, id DESC");
    $addr_stmt->bind_param("i", $user_id);
    $addr_stmt->execute();
    $addr_res = $addr_stmt->get_result();
    while ($row = $addr_res->fetch_assoc()) {
        $user_addresses[] = $row;
    }
    $addr_stmt->close();
}

// ---------------------------------------------------------
// FETCH ORDERS (If view is orders or tracking)
// ---------------------------------------------------------
$my_orders = [];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'All';
if ($view == 'orders' || $view == 'tracking') {
    $check_orders = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
    if (mysqli_num_rows($check_orders) > 0) {
        $filter_sql = "";
        if ($tab == 'To Pay') $filter_sql = " AND status='Pending'";
        if ($tab == 'To Ship') $filter_sql = " AND status='Paid'";
        if ($tab == 'To Receive') $filter_sql = " AND status='Shipped'";
        if ($tab == 'Completed') $filter_sql = " AND status IN ('Delivered', 'Completed')";
        if ($tab == 'Cancelled') $filter_sql = " AND status='Cancelled'";

        $order_sql = "SELECT * FROM orders WHERE user_id=? " . $filter_sql . " ORDER BY created_at DESC";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("i", $user_id);
        $order_stmt->execute();
        $order_res = $order_stmt->get_result();
        while ($r = $order_res->fetch_assoc()) {
            $my_orders[] = $r;
        }
        $order_stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | IMARKET PH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/components/user-account.css?v=<?php echo time(); ?>">

    <style>
        :root {
            --primary-navy: #2A3B7E;
            --primary-dark: #1a2657;
            --accent-blue: #3b82f6;
            --success-green: #10b981;
            --warning-orange: #f59e0b;
            --danger-red: #ef4444;
            --soft-gray: #f8fafc;
            --light-border: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4f8 100%);
            color: var(--text-primary);
            margin: 0;
        }

        .alert-success {
            background: linear-gradient(135deg, #dbeafe 0%, #dcfce7 100%);
            color: #065f46;
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
        }

        .alert-success::before {
            content: '✓';
            font-size: 20px;
            font-weight: bold;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #7f1d1d;
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
        }

        .alert-error::before {
            content: '⚠';
            font-size: 18px;
            font-weight: bold;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-account-wrapper {
            display: grid;
            grid-template-columns: 280px 1fr;
            max-width: 1400px;
            margin: 30px auto;
            gap: 30px;
            padding: 0 20px;
            min-height: calc(100vh - 200px);
        }

        /* SIDEBAR STYLES */
        .account-sidebar {
            position: sticky;
            top: 100px;
            height: fit-content;
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--light-border);
        }

        .sidebar-profile {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 2px solid var(--light-border);
        }

        .sidebar-avatar {
            width: 65px;
            height: 65px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-navy), var(--accent-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.2);
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }

        .sidebar-username {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 15px;
            margin-bottom: 6px;
        }

        .sidebar-edit-link {
            color: var(--primary-navy);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .sidebar-edit-link:hover {
            color: var(--accent-blue);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu-item {
            margin-bottom: 5px;
        }

        .sidebar-menu-title {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .sidebar-menu-title:hover {
            background: var(--soft-gray);
            color: var(--primary-navy);
        }

        .sidebar-menu-title.active-nav {
            background: linear-gradient(135deg, rgba(42, 59, 126, 0.1), rgba(59, 130, 246, 0.1));
            color: var(--primary-navy);
            border-left: 3px solid var(--primary-navy);
            padding-left: 12px;
        }

        .sidebar-submenu {
            list-style: none;
            padding: 0;
            margin: 8px 0 0 0;
            display: none;
        }

        .sidebar-menu-item:hover .sidebar-submenu {
            display: block;
        }

        .sidebar-submenu li {
            margin-bottom: 3px;
        }

        .sidebar-submenu a {
            display: block;
            padding: 10px 15px 10px 40px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar-submenu a::before {
            content: '';
            position: absolute;
            left: 15px;
            width: 4px;
            height: 4px;
            background: var(--primary-navy);
            border-radius: 50%;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .sidebar-submenu a:hover {
            background: var(--soft-gray);
            color: var(--primary-navy);
        }

        .sidebar-submenu a:hover::before {
            opacity: 1;
        }

        .sidebar-submenu a.active {
            background: linear-gradient(135deg, rgba(42, 59, 126, 0.1), rgba(59, 130, 246, 0.1));
            color: var(--primary-navy);
            font-weight: 600;
        }

        .seller-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            margin-top: 30px;
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .seller-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(42, 59, 126, 0.4);
        }

        /* MAIN CONTENT */
        .account-content {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--light-border);
            min-height: 400px;
        }

        /* SECTION STYLES */
        .content-section {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--light-border);
        }

        .section-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-header p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* PROFILE FORM */
        .profile-form, .password-form {
            max-width: 800px;
        }

        .profile-pic-section {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            padding: 30px;
            background: linear-gradient(135deg, rgba(42, 59, 126, 0.05), rgba(59, 130, 246, 0.05));
            border-radius: 12px;
            border: 2px dashed var(--light-border);
        }

        .profile-pic-display {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-navy), var(--accent-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.2);
        }

        .profile-pic-display img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-pic-controls {
            flex: 1;
        }

        .btn-upload {
            background: var(--primary-navy);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .btn-upload:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .profile-pic-controls small {
            display: block;
            color: var(--text-secondary);
            font-size: 12px;
        }

        /* FORM STYLES */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input, 
        .form-group textarea, 
        .form-group select {
            padding: 12px 14px;
            border: 1.5px solid var(--light-border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus, 
        .form-group textarea:focus, 
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-navy);
            box-shadow: 0 0 0 3px rgba(42, 59, 126, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(42, 59, 126, 0.4);
        }

        /* ADDRESS SECTION */
        .addresses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .address-card {
            border: 2px solid var(--light-border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .address-card.default {
            border-color: var(--primary-navy);
            background: linear-gradient(135deg, rgba(42, 59, 126, 0.02), rgba(59, 130, 246, 0.02));
        }

        .address-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .badge-default {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary-navy);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .address-info {
            margin-bottom: 15px;
        }

        .address-info h4 {
            margin: 0 0 8px 0;
            color: var(--text-primary);
            font-size: 16px;
        }

        .address-info p {
            margin: 6px 0;
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.5;
        }

        .address-info .phone {
            color: var(--primary-navy);
            font-weight: 500;
            margin-top: 10px;
        }

        .address-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--light-border);
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 8px 12px;
            border: 1px solid var(--light-border);
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            flex: 1;
            justify-content: center;
            text-decoration: none;
        }

        .btn-edit {
            background: var(--soft-gray);
            color: var(--primary-navy);
            border-color: var(--primary-navy);
        }

        .btn-edit:hover {
            background: var(--primary-navy);
            color: white;
        }

        .btn-default {
            background: var(--success-green);
            color: white;
            border-color: var(--success-green);
        }

        .btn-default:hover {
            opacity: 0.9;
        }

        .btn-danger {
            background: #fee2e2;
            color: var(--danger-red);
            border-color: var(--danger-red);
        }

        .btn-danger:hover {
            background: var(--danger-red);
            color: white;
        }

        .btn-secondary {
            background: var(--accent-blue);
            color: white;
            border-color: var(--accent-blue);
        }

        .btn-secondary:hover {
            opacity: 0.9;
        }

        /* PASSWORD SECTION */
        .security-tips {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(59, 130, 246, 0.05));
            border-left: 4px solid var(--success-green);
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .security-tips h4 {
            margin: 0 0 12px 0;
            color: var(--text-primary);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .security-tips ul {
            margin: 0;
            padding-left: 20px;
            list-style: none;
        }

        .security-tips li {
            color: var(--text-secondary);
            font-size: 13px;
            margin: 6px 0;
            position: relative;
            padding-left: 16px;
        }

        .security-tips li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--success-green);
            font-weight: bold;
        }

        /* ORDERS SECTION */
        .orders-tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid var(--light-border);
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .tab {
            padding: 14px 20px;
            text-decoration: none;
            color: var(--text-secondary);
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-weight: 500;
            font-size: 14px;
        }

        .tab:hover {
            color: var(--primary-navy);
        }

        .tab.active {
            color: var(--primary-navy);
            border-bottom-color: var(--primary-navy);
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-card {
            border: 1px solid var(--light-border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            background: white;
        }

        .order-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-border);
        }

        .order-number {
            display: block;
            font-weight: 700;
            color: var(--text-primary);
            font-size: 16px;
        }

        .order-date {
            display: block;
            color: var(--text-secondary);
            font-size: 12px;
            margin-top: 4px;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-paid {
            background: #dbeafe;
            color: #0c2d6b;
        }

        .status-shipped {
            background: #e0e7ff;
            color: #312e81;
        }

        .status-delivered,
        .status-completed {
            background: #dcfce7;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .order-body {
            margin-bottom: 20px;
        }

        .order-item {
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 15px;
            align-items: center;
        }

        .order-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background: var(--soft-gray);
        }

        .order-item-info h4 {
            margin: 0 0 6px 0;
            color: var(--text-primary);
            font-size: 14px;
        }

        .order-item-info p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 12px;
        }

        .order-item-total {
            text-align: right;
        }

        .order-item-total strong {
            font-size: 16px;
            color: var(--primary-navy);
        }

        .order-footer {
            display: flex;
            gap: 10px;
            padding-top: 15px;
            border-top: 1px solid var(--light-border);
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 64px;
            color: var(--light-border);
            margin-bottom: 20px;
            display: block;
        }

        .empty-state p {
            font-size: 16px;
            margin: 0;
        }
        .modal-overlay {
            backdrop-filter: blur(4px);
        }

        .modal-overlay > div {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--light-border);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        #modalTitle {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--accent-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .modal-overlay input,
        .modal-overlay textarea,
        .modal-overlay select {
            border: 1.5px solid var(--light-border);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .modal-overlay input:focus,
        .modal-overlay textarea:focus,
        .modal-overlay select:focus {
            outline: none;
            border-color: var(--primary-navy);
            box-shadow: 0 0 0 3px rgba(42, 59, 126, 0.1);
        }

        .modal-overlay .btn-primary {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 12px 32px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .modal-overlay .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(42, 59, 126, 0.4);
        }

        .modal-overlay .btn-outline {
            background: white;
            border: 1.5px solid var(--light-border);
            color: var(--text-primary);
            padding: 12px 32px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .modal-overlay .btn-outline:hover {
            border-color: var(--primary-navy);
            color: var(--primary-navy);
            background: var(--soft-gray);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .user-account-wrapper {
                grid-template-columns: 1fr;
            }

            .account-sidebar {
                position: relative;
                top: 0;
            }

            .account-content {
                padding: 30px 20px;
            }
        }

        @media (max-width: 600px) {
            .user-account-wrapper {
                margin: 15px auto;
                gap: 20px;
                padding: 0 15px;
            }

            .account-sidebar {
                padding: 20px;
            }

            .account-content {
                padding: 20px;
            }

            .modal-overlay > div {
                width: 90% !important;
                max-width: 100% !important;
            }
        }

        /* Loading spinner animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fa-spin {
            animation: spin 1.5s linear infinite;
        }

        .fa-circle-notch {
            color: #2A3B7E;
        }
    </style>

    <!-- Dashboard Integration: AJAX Navigation System -->
    <!-- <script src="../javascript/dashboard-integration.js" defer></script> -->

    <script>
        // Set profile picture with proper fallback
        function setupProfilePicture() {
            <?php 
            $profilePic = !empty($user['profile_pic']) ? '../uploads/profile/'.$user['profile_pic'] : null;
            ?>
            const avatarDiv = document.getElementById('sidebarAvatar');
            if (avatarDiv && '<?php echo $profilePic; ?>') {
                avatarDiv.style.backgroundImage = 'url("<?php echo $profilePic; ?>")';
                avatarDiv.style.backgroundSize = 'cover';
                avatarDiv.style.backgroundPosition = 'center';
            }
        }

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setupProfilePicture();
            });
        } else {
            setupProfilePicture();
        }
    </script>
</head>

<body>

    <nav>
        <?php
        $path_prefix = '../';
        include('../Components/header.php');
        ?>
    </nav>

    <div class="user-account-wrapper">

        <!-- SIDEBAR -->
        <aside class="account-sidebar">
            <div class="sidebar-profile">
                <div class="sidebar-avatar" id="sidebarAvatar">
                    <i class="far fa-user"></i>
                </div>
                <div>
                    <div class="sidebar-username" id="sidebarUsername">
                        <?php echo htmlspecialchars(!empty($user['username']) ? $user['username'] : (!empty($user['fullname']) ? $user['fullname'] : $user['email'])); ?>
                    </div>
                    <a href="?view=profile" class="sidebar-edit-link"><i class="fas fa-pen"></i> Edit Profile</a>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="?view=profile" class="sidebar-menu-title" data-menu="account">
                        <i class="fas fa-user"></i> My Account
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="?view=profile" class="sidebar-submenu-link" data-view="profile">Profile</a></li>
                        <li><a href="?view=banks" class="sidebar-submenu-link" data-view="banks">Banks & Cards</a></li>
                        <li><a href="?view=address" class="sidebar-submenu-link" data-view="address">Addresses</a></li>
                        <li><a href="?view=password" class="sidebar-submenu-link" data-view="password">Change Password</a></li>
                    </ul>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?view=orders" class="sidebar-menu-title" data-view="orders">
                        <i class="fas fa-clipboard-list"></i> My Purchase
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?view=notifications" class="sidebar-menu-title" data-view="notifications">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                </li>
            </ul>

            <!-- SELLER CENTRE BUTTON (Integration Point) -->
            <!-- Assuming Seller/Login.php or similar exists, or just a placeholder for Team 2 -->
            <a href="../Seller/index.php" class="seller-btn">
                <i class="fas fa-store"></i> Seller Centre
            </a>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="account-content" role="main">
            <?php echo $msg; ?>
            
            <?php if ($view == 'profile'): ?>
                <!-- PROFILE SECTION -->
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-user-circle"></i> My Profile</h2>
                        <p>Update your personal information</p>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="profile-form">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="profile-pic-section">
                            <div class="profile-pic-display" id="profilePicDisplay">
                                <?php if (!empty($user['profile_pic'])): ?>
                                    <img src="../uploads/profile/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div class="profile-pic-controls">
                                <input type="file" id="profilePicInput" name="profile_pic" accept="image/*" style="display:none;">
                                <button type="button" class="btn-upload" onclick="document.getElementById('profilePicInput').click();">
                                    <i class="fas fa-camera"></i> Change Photo
                                </button>
                                <small>JPG, PNG up to 5MB</small>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+63 XXX XXXX XXX" required>
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender">
                                    <option value="Not Specified" <?php echo ($user['gender'] == 'Not Specified') ? 'selected' : ''; ?>>Not Specified</option>
                                    <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Birth Date</label>
                                <input type="date" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>Address</label>
                            <textarea name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Postal Code</label>
                            <input type="text" name="zip" value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>

            <?php elseif ($view == 'address'): ?>
                <!-- ADDRESS SECTION -->
                <div class="content-section">
                    <div class="section-header">
                        <div>
                            <h2><i class="fas fa-map-marker-alt"></i> My Addresses</h2>
                            <p>Manage your delivery addresses</p>
                        </div>
                        <button class="btn-primary" onclick="openAddressModal()">
                            <i class="fas fa-plus"></i> Add New Address
                        </button>
                    </div>

                    <div class="addresses-grid">
                        <?php if (empty($user_addresses)): ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>No addresses yet. Add one to get started!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($user_addresses as $addr): ?>
                                <div class="address-card <?php echo $addr['is_default'] ? 'default' : ''; ?>">
                                    <?php if ($addr['is_default']): ?>
                                        <span class="badge-default">Default Address</span>
                                    <?php endif; ?>
                                    <div class="address-info">
                                        <h4><?php echo htmlspecialchars($addr['fullname']); ?></h4>
                                        <p><?php echo htmlspecialchars($addr['address']); ?></p>
                                        <p><?php echo htmlspecialchars($addr['city']); ?>, <?php echo htmlspecialchars($addr['zip']); ?></p>
                                        <p class="phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($addr['phone']); ?></p>
                                    </div>
                                    <div class="address-actions">
                                        <button class="btn-sm btn-edit" onclick="editAddress(<?php echo htmlspecialchars(json_encode($addr)); ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <?php if (!$addr['is_default']): ?>
                                            <button class="btn-sm btn-default" onclick="setDefault(<?php echo $addr['id']; ?>)">
                                                <i class="fas fa-check"></i> Set Default
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn-sm btn-danger" onclick="deleteAddress(<?php echo $addr['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif ($view == 'password'): ?>
                <!-- PASSWORD SECTION -->
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-lock"></i> Change Password</h2>
                        <p>Update your password to keep your account secure</p>
                    </div>

                    <form method="POST" class="password-form">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required minlength="8" placeholder="At least 8 characters">
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required minlength="8">
                        </div>

                        <div class="security-tips">
                            <h4><i class="fas fa-shield-alt"></i> Password Tips</h4>
                            <ul>
                                <li>Use at least 8 characters</li>
                                <li>Mix uppercase and lowercase letters</li>
                                <li>Include numbers and special characters</li>
                                <li>Don't use personal information</li>
                            </ul>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>

            <?php elseif ($view == 'orders'): ?>
                <!-- ORDERS SECTION -->
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-shopping-bag"></i> My Orders</h2>
                        <p>Track and manage your purchases</p>
                    </div>

                    <div class="orders-tabs">
                        <a href="?view=orders&tab=All" class="tab <?php echo $tab == 'All' ? 'active' : ''; ?>">All Orders</a>
                        <a href="?view=orders&tab=To Pay" class="tab <?php echo $tab == 'To Pay' ? 'active' : ''; ?>">To Pay</a>
                        <a href="?view=orders&tab=To Ship" class="tab <?php echo $tab == 'To Ship' ? 'active' : ''; ?>">To Ship</a>
                        <a href="?view=orders&tab=To Receive" class="tab <?php echo $tab == 'To Receive' ? 'active' : ''; ?>">To Receive</a>
                        <a href="?view=orders&tab=Completed" class="tab <?php echo $tab == 'Completed' ? 'active' : ''; ?>">Completed</a>
                        <a href="?view=orders&tab=Cancelled" class="tab <?php echo $tab == 'Cancelled' ? 'active' : ''; ?>">Cancelled</a>
                    </div>

                    <div class="orders-list">
                        <?php if (empty($my_orders)): ?>
                            <div class="empty-state">
                                <i class="fas fa-shopping-cart"></i>
                                <p>No orders yet. Start shopping!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($my_orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div>
                                            <span class="order-number">Order #<?php echo htmlspecialchars($order['id']); ?></span>
                                            <span class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                        </div>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </div>

                                    <div class="order-body">
                                        <div class="order-item">
                                            <img src="<?php echo htmlspecialchars($order['image_url'] ?? '../image/placeholder.png'); ?>" alt="Product" class="order-item-img">
                                            <div class="order-item-info">
                                                <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                                                <p>Qty: <?php echo intval($order['quantity']); ?> × ₱<?php echo number_format($order['price'], 2); ?></p>
                                            </div>
                                            <div class="order-item-total">
                                                <strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-footer">
                                        <a href="?view=tracking&order_id=<?php echo $order['id']; ?>" class="btn-sm btn-secondary">
                                            <i class="fas fa-map-marker-alt"></i> Track Order
                                        </a>
                                        <?php if ($order['status'] == 'Pending' || $order['status'] == 'Paid'): ?>
                                            <button class="btn-sm btn-danger" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- Default Empty State -->
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <p>This section is not available yet.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- ADDRESS MODAL -->
    <div id="addressModal" class="modal-overlay"
        style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div
            style="background: #fff; width: 500px; padding: 30px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); max-width: 95%;">
            <div style="font-size: 20px; font-weight: 500; margin-bottom: 25px; color: #333;" id="modalTitle">New
                Address</div>

            <form id="addressForm" action="" method="POST">
                <input type="hidden" name="action" value="save_address">
                <input type="hidden" name="address_id" id="addr_id" value="">

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <input type="text" name="fullname" id="addr_fullname" placeholder="Full Name"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px;" required>
                    </div>
                    <div style="flex: 1;">
                        <input type="text" name="phone" id="addr_phone" placeholder="Phone Number"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px;" required>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <input type="text" name="city" id="addr_city" placeholder="Region, Province, City"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px;" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <textarea name="address" id="addr_text" rows="3" placeholder="Street Name, Building, House No."
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px; font-family: inherit;"
                        required></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <input type="text" name="zip" id="addr_zip" placeholder="Postal Code"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px;" required>
                </div>

                <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                    <label
                        style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #666; cursor: pointer;">
                        <input type="checkbox" name="is_default" id="addr_default"> Set as Default Address
                    </label>
                    <button type="button" id="btn-detect-account"
                        style="background:none; border:1px solid #2A3B7E; color:#2A3B7E; padding:5px 10px; border-radius:4px; font-size:12px; cursor:pointer;">
                        <i class="fas fa-location-arrow"></i> Detect Location
                    </button>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-outline" onclick="closeAddressModal()"
                        style="padding: 10px 25px; border: 1px solid #ddd; background: #fff; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-primary"
                        style="padding: 10px 35px; background: #2A3B7E; color: #fff; border: none; cursor: pointer;">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden form for actions -->
    <form id="actionForm" method="POST" style="display:none;">
        <input type="hidden" name="action" id="formAction">
        <input type="hidden" name="address_id" id="formAddrId">
        <input type="hidden" name="order_id" id="formOrderId">
    </form>

    <!-- FOOTER -->
    <div class="footer-account-wrapper" style="margin-top: 50px; background: #fff; border-top: 1px solid #e2e8f0;">
        <?php
        $path_prefix = '../';
        include('../Components/footer.php');
        ?>
    </div>

    <script>
        // Handle profile picture preview
        const profilePicInput = document.getElementById('profilePicInput');
        if (profilePicInput) {
            profilePicInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('profilePicDisplay').innerHTML = '<img src="' + event.target.result + '" alt="Profile">';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function openAddressModal() {
            document.getElementById('modalTitle').innerText = 'New Address';
            document.getElementById('addressForm').reset();
            document.getElementById('addr_id').value = '';
            document.getElementById('addressModal').style.display = 'flex';
        }

        function closeAddressModal() {
            document.getElementById('addressModal').style.display = 'none';
        }

        function editAddress(addr) {
            document.getElementById('modalTitle').innerText = 'Edit Address';
            document.getElementById('addr_id').value = addr.id;
            document.getElementById('addr_fullname').value = addr.fullname;
            document.getElementById('addr_phone').value = addr.phone;
            document.getElementById('addr_city').value = addr.city;
            document.getElementById('addr_text').value = addr.address;
            document.getElementById('addr_zip').value = addr.zip;
            document.getElementById('addr_default').checked = addr.is_default == 1;
            document.getElementById('addressModal').style.display = 'flex';
        }

        function deleteAddress(id) {
            if (confirm('Are you sure you want to delete this address?')) {
                document.getElementById('formAction').value = 'delete_address';
                document.getElementById('formAddrId').value = id;
                document.getElementById('actionForm').submit();
            }
        }

        function setDefault(id) {
            document.getElementById('formAction').value = 'set_default';
            document.getElementById('formAddrId').value = id;
            document.getElementById('actionForm').submit();
        }

        function cancelOrder(id) {
            if (confirm('Are you sure you want to cancel this order?')) {
                document.getElementById('formAction').value = 'cancel_order';
                document.getElementById('formOrderId').value = id;
                document.getElementById('actionForm').submit();
            }
        }

        // Detect Location in Address Modal
        const detectBtnAcc = document.getElementById('btn-detect-account');
        if (detectBtnAcc) {
            detectBtnAcc.onclick = function () {
                if (!navigator.geolocation) {
                    alert("Geolocation not supported.");
                    return;
                }
                detectBtnAcc.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
                navigator.geolocation.getCurrentPosition(async (pos) => {
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`);
                        const data = await res.json();
                        if (data && data.address) {
                            document.getElementById('addr_text').value = data.display_name;
                            document.getElementById('addr_city').value = data.address.city || data.address.town || data.address.province || "";
                            document.getElementById('addr_zip').value = data.address.postcode || "";
                        }
                    } catch (e) { alert("Detection failed."); }
                    finally { detectBtnAcc.innerHTML = '<i class="fas fa-location-arrow"></i> Detect Location'; }
                }, () => {
                    alert("Location access denied.");
                    detectBtnAcc.innerHTML = '<i class="fas fa-location-arrow"></i> Detect Location';
                });
            };
        }

        // Close modal if clicked outside
        window.onclick = function (event) {
            const modal = document.getElementById('addressModal');
            if (event.target == modal) {
                closeAddressModal();
            }
        }
    </script>
</body>

</html>
