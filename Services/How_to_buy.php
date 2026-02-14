<?php
session_start();
include("../Database/config.php");

// Fetch Buying Steps
$steps = [];
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'buying_steps'");
if (mysqli_num_rows($check_table) > 0) {
    $sql = "SELECT * FROM buying_steps ORDER BY step_order ASC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $steps = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
} else {
    // Fallback data
    $steps = [
        ['step_order' => 1, 'title' => 'Search & Select', 'description' => 'Browse our wide range of products using the search bar or categories.', 'icon_class' => 'fas fa-search'],
        ['step_order' => 2, 'title' => 'Add to Cart', 'description' => 'Select your preferred variation and quantity, then click "Add to Cart".', 'icon_class' => 'fas fa-cart-plus'],
        ['step_order' => 3, 'title' => 'Checkout', 'description' => 'Review your cart and proceed to checkout.', 'icon_class' => 'fas fa-shopping-bag'],
        ['step_order' => 4, 'title' => 'Place Order', 'description' => 'Enter shipping details and confirm your purchase.', 'icon_class' => 'fas fa-check-circle']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Buy | IMARKET PH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/services/how_to_buy.css?v=<?php echo time(); ?>">
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
    <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1e40af 100%); padding: 70px 20px; color: white; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1;">
            <div style="position: absolute; width: 400px; height: 400px; background: white; border-radius: 50%; top: -150px; right: -150px;"></div>
            <div style="position: absolute; width: 300px; height: 300px; background: white; border-radius: 50%; bottom: -100px; left: -100px;"></div>
        </div>
        <div style="position: relative; z-index: 2; max-width: 900px; margin: 0 auto;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 2.5rem;"></i>
                <h1 style="font-size: 3rem; font-weight: 800; margin: 0;">Shop With Us in 4 Steps</h1>
            </div>
            <p style="font-size: 1.15rem; opacity: 0.95; margin: 0; line-height: 1.6;">New to IMARKET? Follow our simple guide to make your first purchase and get exclusive deals!</p>
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
            <a href="How_to_buy.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #3b82f6; border-bottom: 3px solid #3b82f6; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap; background: #eff6ff;">
                <i class="fas fa-shopping-cart" style="font-size: 16px;"></i> How to Buy
            </a>
            <a href="Contact Us.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-envelope" style="font-size: 16px;"></i> Contact
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 50px 20px;">
        <!-- Steps Section -->
        <div style="margin-bottom: 60px;">
            <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 12px; font-weight: 800;">4 Simple Steps to Start Shopping</h2>
            <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 50px;">Follow this easy guide to make your first purchase and unlock amazing deals</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px;">
                <?php foreach ($steps as $step): ?>
                    <div style="background: white; border-radius: 16px; border: 2px solid #dbeafe; overflow: hidden; transition: all 0.3s; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);" onmouseover="this.style.boxShadow='0 15px 40px rgba(59, 130, 246, 0.2)'; this.style.transform='translateY(-8px)'; this.style.borderColor='#3b82f6'" onmouseout="this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.08)'; this.style.transform='translateY(0)'; this.style.borderColor='#dbeafe'">
                        <!-- Step Number Header -->
                        <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 30px 25px; text-align: center;">
                            <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);">
                                <i class="<?php echo htmlspecialchars($step['icon_class']); ?>" style="color: white; font-size: 32px;"></i>
                            </div>
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; position: absolute; top: 15px; right: 15px; color: white; font-weight: 800; font-size: 20px;">
                                <?php echo $step['step_order']; ?>
                            </div>
                        </div>

                        <!-- Content -->
                        <div style="padding: 30px;">
                            <h3 style="margin: 0 0 12px 0; color: #1e293b; font-size: 1.2rem; font-weight: 700;"><?php echo htmlspecialchars($step['title']); ?></h3>
                            <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;"><?php echo htmlspecialchars($step['description']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Benefits Section -->
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 45px; border-radius: 16px; border: 2px solid #bfdbfe; margin-bottom: 60px;">
            <h2 style="font-size: 1.8rem; color: #1e293b; margin: 0 0 35px 0; font-weight: 800; text-align: center;">Why Shop With IMARKET?</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px;">
                <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #3b82f6; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0;">
                            <i class="fas fa-check"></i>
                        </div>
                        <h3 style="margin: 0; color: #1e293b; font-weight: 700;">Quality Products</h3>
                    </div>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Shop from verified sellers offering authentic, high-quality products across all categories.</p>
                </div>

                <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #3b82f6; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 style="margin: 0; color: #1e293b; font-weight: 700;">Secure Checkout</h3>
                    </div>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Your payment information is encrypted and protected. Shop with confidence and peace of mind.</p>
                </div>

                <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #3b82f6; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3 style="margin: 0; color: #1e293b; font-weight: 700;">Best Prices</h3>
                    </div>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Enjoy competitive prices, exclusive discounts, and special promotions on thousands of products.</p>
                </div>

                <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #3b82f6; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 style="margin: 0; color: #1e293b; font-weight: 700;">24/7 Support</h3>
                    </div>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Our customer support team is available 24/7 to answer your questions and help with any issues.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 16px; padding: 50px 30px; text-align: center; color: white; box-shadow: 0 15px 40px rgba(59, 130, 246, 0.2);">
            <h2 style="margin: 0 0 15px 0; font-size: 2rem; font-weight: 800;">Ready to Start Shopping?</h2>
            <p style="margin: 0 0 30px 0; font-size: 1.1rem; opacity: 0.95;">Explore thousands of products at unbeatable prices and join millions of satisfied customers.</p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="../Content/Dashboard.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 16px 35px; background: white; color: #3b82f6; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 16px; transition: all 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'">
                    <i class="fas fa-shopping-cart"></i> Start Shopping Now
                </a>
                <a href="Customer_Service.php?tab=faq" style="display: inline-flex; align-items: center; gap: 8px; padding: 16px 35px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 16px; border: 2px solid white; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(0)'">
                    <i class="fas fa-question-circle"></i> Browse FAQs
                </a>
            </div>
        </div>
    </div>

    <footer style="margin-top: 80px;">
        <?php include '../Components/footer.php'; ?>
    </footer>
</body>

</html>
