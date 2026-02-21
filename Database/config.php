<?php
$host = "localhost:3306"; // Siguraduhing 3307 ang nasa XAMPP mo, kung hindi, gawing 3306
$user = "root";           // Default user ng XAMPP
$password = "";           // Default ay walang password sa XAMPP
$db = "core1_marketph";   // Pangalan ng database na ginawa mo sa phpMyAdmin

$conn = new mysqli("localhost", "root", "", "core1_marketph");


// Auto-create users table if it doesn't exist
$sql_create_users = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(10) DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `profile_pic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql_create_users);

// Ensure users has profile_pic column (for existing tables)
$check_profile_pic = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_pic'");
if ($check_profile_pic && $check_profile_pic->num_rows == 0) {
  $conn->query("ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL");
}

// Auto-create support_tickets table if it doesn't exist
$sql_create_tickets = "CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(50) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') DEFAULT 'Open',
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium',
  `assigned_to` int(11) DEFAULT NULL,
  `admin_reply` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `customer_id` (`customer_id`),
  KEY `assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$conn->query($sql_create_tickets);

// Ensure support_tickets has all necessary columns
$ticket_cols = [
  'ticket_number' => "VARCHAR(50) DEFAULT NULL",
  'customer_id' => "INT(11) DEFAULT NULL",
  'category' => "VARCHAR(100) DEFAULT NULL",
  'subject' => "VARCHAR(200) NOT NULL",
  'message' => "TEXT NOT NULL",
  'status' => "ENUM('Open','In Progress','Resolved','Closed') DEFAULT 'Open'",
  'priority' => "ENUM('Low','Medium','High','Urgent') DEFAULT 'Medium'",
  'assigned_to' => "INT(11) DEFAULT NULL",
  'admin_reply' => "TEXT DEFAULT NULL",
  'is_read' => "TINYINT(1) DEFAULT 0",
  'user_read' => "TINYINT(1) DEFAULT 0"
];

foreach ($ticket_cols as $col => $def) {
  $res = $conn->query("SHOW COLUMNS FROM support_tickets LIKE '$col'");
  if ($res && $res->num_rows == 0) {
    // Column missing, add it
    $conn->query("ALTER TABLE support_tickets ADD COLUMN $col $def");
  }
}

// Special fix for 'user_id' if it exists and is causing 'no default value' error
$check_uid = $conn->query("SHOW COLUMNS FROM support_tickets LIKE 'user_id'");
if ($check_uid && $check_uid->num_rows > 0) {
  $conn->query("ALTER TABLE support_tickets MODIFY COLUMN user_id INT(11) DEFAULT NULL");
}

// Ensure unique index on ticket_number exists
$res = $conn->query("SHOW INDEX FROM support_tickets WHERE Key_name = 'ticket_number'");
if ($res && $res->num_rows == 0) {
  // Check if column exists first (it should now)
  $col_check = $conn->query("SHOW COLUMNS FROM support_tickets LIKE 'ticket_number'");
  if ($col_check && $col_check->num_rows > 0) {
    $conn->query("ALTER TABLE support_tickets ADD UNIQUE (ticket_number)");
  }
}
// Auto-create orders table if it doesn't exist (needed for checkout/Confirmation)
$sql_create_orders = "CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tracking_number` varchar(50) DEFAULT NULL,
  `product_id` int(11) DEFAULT 0,
  `product_name` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `full_name` varchar(255) NOT NULL DEFAULT '',
  `phone_number` varchar(50) NOT NULL DEFAULT '',
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL DEFAULT '',
  `postal_code` varchar(20) NOT NULL DEFAULT '',
  `payment_method` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(50) DEFAULT 'Pending',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql_create_orders);

// Auto-update orders table structure (add any missing columns)
$check_orders = $conn->query("SHOW TABLES LIKE 'orders'");
if ($check_orders->num_rows > 0) {
  // Check and add user_id
  $check_user_id = $conn->query("SHOW COLUMNS FROM orders LIKE 'user_id'");
  if ($check_user_id->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN user_id INT NOT NULL AFTER id");
  }

  // Check and add tracking_number
  $check_tracking = $conn->query("SHOW COLUMNS FROM orders LIKE 'tracking_number'");
  if ($check_tracking->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(50) AFTER user_id");
  }

  // Check and add product_id
  $check_product_id = $conn->query("SHOW COLUMNS FROM orders LIKE 'product_id'");
  if ($check_product_id->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN product_id INT DEFAULT 0 AFTER tracking_number");
  }

  // Check and add product_name
  $check_pname = $conn->query("SHOW COLUMNS FROM orders LIKE 'product_name'");
  if ($check_pname->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN product_name VARCHAR(255) NOT NULL AFTER product_id");
  }

  // Check and add quantity
  $check_qty = $conn->query("SHOW COLUMNS FROM orders LIKE 'quantity'");
  if ($check_qty->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN quantity INT NOT NULL AFTER product_name");
  }

  // Check and add price
  $check_price = $conn->query("SHOW COLUMNS FROM orders LIKE 'price'");
  if ($check_price->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN price DECIMAL(10,2) NOT NULL AFTER quantity");
  }

  // Check and add total_amount
  $check_total = $conn->query("SHOW COLUMNS FROM orders LIKE 'total_amount'");
  if ($check_total->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN total_amount DECIMAL(10,2) NOT NULL AFTER price");
  }

  // Check and add image_url (some versions might have used image_url instead of image)
  $check_image = $conn->query("SHOW COLUMNS FROM orders LIKE 'image_url'");
  if ($check_image->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN image_url VARCHAR(255) AFTER status");
  }

  // Check and add full_name, phone_number, address, city, postal_code, payment_method, status
  $cols_to_check = [
    'full_name' => "VARCHAR(255) NOT NULL",
    'phone_number' => "VARCHAR(50) NOT NULL",
    'address' => "TEXT NOT NULL",
    'city' => "VARCHAR(100) NOT NULL",
    'postal_code' => "VARCHAR(20) NOT NULL",
    'payment_method' => "VARCHAR(50) NOT NULL",
    'status' => "VARCHAR(50) DEFAULT 'Pending'"
  ];

  foreach ($cols_to_check as $col => $def) {
    $c_check = $conn->query("SHOW COLUMNS FROM orders LIKE '$col'");
    if ($c_check->num_rows == 0) {
      $conn->query("ALTER TABLE orders ADD COLUMN $col $def");
    }
  }

  // Final fix for 'order_number' error: make it nullable if it exists
  $check_order_num = $conn->query("SHOW COLUMNS FROM orders LIKE 'order_number'");
  if ($check_order_num->num_rows > 0) {

    $conn->query("ALTER TABLE orders MODIFY COLUMN order_number VARCHAR(50) NULL");
  }

  // Handle 'customer_id' and 'address_id' errors for existing shopify-style tables
  $check_cust_id = $conn->query("SHOW COLUMNS FROM orders LIKE 'customer_id'");
  if ($check_cust_id->num_rows > 0) {
    $conn->query("ALTER TABLE orders MODIFY COLUMN customer_id INT NULL");
  }

  $check_addr_id = $conn->query("SHOW COLUMNS FROM orders LIKE 'address_id'");
  if ($check_addr_id->num_rows > 0) {
    $conn->query("ALTER TABLE orders MODIFY COLUMN address_id INT NULL");
  }
}

// Auto-create cart table if it doesn't exist
$sql_create_cart = "CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT 0,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `shop_name` varchar(255) DEFAULT 'Imarket',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql_create_cart);

// Auto-create order_items table if it doesn't exist (needed for best-sellers query)
$sql_create_order_items = "CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql_create_order_items);

// Auto-create reviews table if it doesn't exist (for product reviews)
$sql_create_reviews = "CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT 0,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `sentiment` varchar(20) DEFAULT 'Neutral',
  `confidence` float DEFAULT 0.0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql_create_reviews);

// Ensure reviews has sentiment/confidence columns (for fetch_reviews.php)
$check_sentiment = $conn->query("SHOW COLUMNS FROM reviews LIKE 'sentiment'");
if ($check_sentiment && $check_sentiment->num_rows == 0) {
  $conn->query("ALTER TABLE reviews ADD COLUMN sentiment VARCHAR(20) DEFAULT 'Neutral'");
  $conn->query("ALTER TABLE reviews ADD COLUMN confidence FLOAT DEFAULT 0.0");
}

// Auto-create user_addresses table if it doesn't exist
$sql_create_user_addresses = "CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql_create_user_addresses);

// Auto-create ticket_replies table for threaded conversations
$sql_create_replies = "CREATE TABLE IF NOT EXISTS `ticket_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('customer','admin') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql_create_replies);

// Auto-update cart table structure
$check_cart = $conn->query("SHOW TABLES LIKE 'cart'");
if ($check_cart->num_rows > 0) {
  $check_cart_pid = $conn->query("SHOW COLUMNS FROM cart LIKE 'product_id'");
  if ($check_cart_pid->num_rows == 0) {
    $conn->query("ALTER TABLE cart ADD COLUMN product_id INT DEFAULT 0 AFTER user_id");
  }
}
?>
