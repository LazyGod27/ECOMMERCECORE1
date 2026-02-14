<?php
session_start();
include("../Database/config.php");


$zones = [];
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'shipping_zones'");
if (mysqli_num_rows($check_table) > 0) {
    $sql = "SELECT * FROM shipping_zones ORDER BY base_fee ASC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $zones = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
} else {
    $zones = [
        ['region_name' => 'Metro Manila', 'base_fee' => 60, 'estimated_days_min' => 2, 'estimated_days_max' => 3],
        ['region_name' => 'Luzon (Provincial)', 'base_fee' => 120, 'estimated_days_min' => 3, 'estimated_days_max' => 7],
        ['region_name' => 'Visayas', 'base_fee' => 160, 'estimated_days_min' => 5, 'estimated_days_max' => 10],
        ['region_name' => 'Mindanao', 'base_fee' => 180, 'estimated_days_min' => 7, 'estimated_days_max' => 14]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping & Delivery | IMARKET PH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="../css/services/shipping_delivery.css?v=<?php echo time(); ?>">
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
    <div style="background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #d97706 100%); padding: 70px 20px; color: white; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1;">
            <div style="position: absolute; width: 400px; height: 400px; background: white; border-radius: 50%; top: -150px; right: -150px;"></div>
            <div style="position: absolute; width: 300px; height: 300px; background: white; border-radius: 50%; bottom: -100px; left: -100px;"></div>
        </div>
        <div style="position: relative; z-index: 2; max-width: 900px; margin: 0 auto;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 20px;">
                <i class="fas fa-shipping-fast" style="font-size: 2.5rem;"></i>
                <h1 style="font-size: 3rem; font-weight: 800; margin: 0;">Fast & Reliable Shipping</h1>
            </div>
            <p style="font-size: 1.15rem; opacity: 0.95; margin: 0; line-height: 1.6;">We deliver nationwide with nationwide coverage. Track your orders in real-time and get your items delivered safely.</p>
        </div>
    </div>

    <!-- Service Navigation Tabs -->
    <div style="background: white; padding: 0; border-bottom: 2px solid #f1f5f9; position: sticky; top: 0; z-index: 100;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; overflow-x: auto;">
            <a href="Customer_Service.php?tab=faq" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-question-circle" style="font-size: 16px;"></i> FAQs
            </a>
            <a href="Customer_Service.php?tab=submit" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-ticket-alt" style="font-size: 16px;"></i> Tickets
            </a>
            <a href="Return & Refund.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-undo-alt" style="font-size: 16px;"></i> Returns
            </a>
            <a href="Shipping & Delivery.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #f97316; border-bottom: 3px solid #f97316; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap; background: #fff7ed;">
                <i class="fas fa-shipping-fast" style="font-size: 16px;"></i> Shipping
            </a>
            <a href="Contact Us.php" style="flex: 1; text-align: center; padding: 18px 12px; text-decoration: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-envelope" style="font-size: 16px;"></i> Contact
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 50px 20px;">
        <!-- Shipping Zones Section -->
        <div style="margin-bottom: 60px;">
            <h2 style="font-size: 2rem; color: #1e293b; margin-bottom: 12px; font-weight: 800;">Shipping Rates by Region</h2>
            <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 35px;">Transparent pricing with nationwide coverage</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
                <?php foreach ($zones as $zone): ?>
                    <div class="shipping-zone-card" style="background: white; border-radius: 16px; border: 2px solid #fed7aa; overflow: hidden; transition: all 0.3s; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.08);" onmouseover="this.style.boxShadow='0 15px 40px rgba(249, 115, 22, 0.2)'; this.style.transform='translateY(-8px)'; this.style.borderColor='#f97316'" onmouseout="this.style.boxShadow='0 4px 12px rgba(249, 115, 22, 0.08)'; this.style.transform='translateY(0)'; this.style.borderColor='#fed7aa'">
                        <!-- Header with Icon -->
                        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%); padding: 25px; text-align: center;">
                            <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);">
                                <?php
                                if (stripos($zone['region_name'], 'Manila') !== false) {
                                    echo '<i class="fas fa-city" style="color: white; font-size: 32px;"></i>';
                                } elseif (stripos($zone['region_name'], 'Visayas') !== false) {
                                    echo '<i class="fas fa-umbrella-beach" style="color: white; font-size: 32px;"></i>';
                                } elseif (stripos($zone['region_name'], 'Mindanao') !== false) {
                                    echo '<i class="fas fa-mountain" style="color: white; font-size: 32px;"></i>';
                                } else {
                                    echo '<i class="fas fa-truck" style="color: white; font-size: 32px;"></i>';
                                }
                                ?>
                            </div>
                            <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 700;"><?php echo htmlspecialchars($zone['region_name']); ?></h3>
                        </div>

                        <!-- Content -->
                        <div style="padding: 25px;">
                            <!-- Shipping Fee -->
                            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #fed7aa;">
                                <div style="font-size: 13px; color: #92400e; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Base Shipping Fee</div>
                                <div style="font-size: 2.2rem; color: #f97316; font-weight: 800;">₱<?php echo number_format($zone['base_fee'], 2); ?></div>
                            </div>

                            <!-- Delivery Time -->
                            <div style="margin-bottom: 20px;">
                                <div style="font-size: 13px; color: #92400e; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-clock" style="color: #f97316;"></i> Delivery Time
                                </div>
                                <div style="font-size: 1.1rem; color: #1e293b; font-weight: 700;"><?php echo $zone['estimated_days_min'] . ' - ' . $zone['estimated_days_max']; ?> <span style="font-size: 0.9rem; color: #64748b; font-weight: 500;">business days</span></div>
                            </div>

                            <!-- Status Badge -->
                            <div style="background: #dcfce7; border: 1px solid #86efac; padding: 10px 12px; border-radius: 8px; text-align: center; color: #059669; font-size: 12px; font-weight: 700;">
                                <i class="fas fa-check-circle" style="margin-right: 6px;"></i> Available
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- How It Works Section -->
        <div style="background: linear-gradient(135deg, #f0f9ff 0%, #f3e8ff 100%); padding: 45px; border-radius: 16px; border: 2px solid #e0e7ff; margin-bottom: 60px;">
            <h2 style="font-size: 1.8rem; color: #1e293b; margin: 0 0 35px 0; font-weight: 800; text-align: center;">How Our Shipping Works</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #f97316; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0;">1</div>
                        <h3 style="margin: 0; color: #1e293b; font-weight: 700;">Order Placed</h3>
                    </div>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Your order is received and payment is confirmed. We'll proceed with processing immediately.</p>
                </div>

                <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #f97316; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0;">2</div>
                        <h3 style="margin: 0; color: #1e293b; font-weight: 700;">Packed & Ready</h3>
                    </div>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Our team carefully packs your items and prepares them for shipment within 24 hours.</p>
                </div>

                <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #f97316; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0;">3</div>
                        <h3 style="margin: 0; color: #1e293b; font-weight: 700;">On Its Way</h3>
                    </div>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Your package is handed to our courier partner with a tracking number for real-time updates.</p>
                </div>

                <div style="background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #f97316; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0;">4</div>
                        <h3 style="margin: 0; color: #1e293b; font-weight: 700;">Delivered</h3>
                    </div>
                    <p style="margin: 0; color: #64748b; line-height: 1.6; font-size: 14px;">Package arrives at your doorstep safely. A confirmation message is sent to you immediately.</p>
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div style="margin-bottom: 60px;">
            <h2 style="font-size: 1.8rem; color: #1e293b; margin-bottom: 35px; font-weight: 800;">Everything You Need to Know</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
                <div class="info-card" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 15px 40px rgba(249, 115, 22, 0.15)'; this.style.transform='translateY(-6px)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #fff7ed, #fed7aa); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
                        <i class="fas fa-clock" style="font-size: 28px; color: #f97316;"></i>
                    </div>
                    <h3 style="margin: 0 0 12px 0; color: #1e293b; font-size: 1.1rem; font-weight: 700;">Order Processing</h3>
                    <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;">Orders are processed within 24 hours of payment confirmation. Orders placed on weekends or holidays will be processed on the next business day.</p>
                </div>

                <div class="info-card" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 15px 40px rgba(249, 115, 22, 0.15)'; this.style.transform='translateY(-6px)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #fff7ed, #fed7aa); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
                        <i class="fas fa-map" style="font-size: 28px; color: #f97316;"></i>
                    </div>
                    <h3 style="margin: 0 0 12px 0; color: #1e293b; font-size: 1.1rem; font-weight: 700;">Track Your Order</h3>
                    <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;">Once your order is shipped, you'll receive a tracking number via email. Monitor real-time updates on your package location and estimated arrival.</p>
                </div>

                <div class="info-card" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 15px 40px rgba(249, 115, 22, 0.15)'; this.style.transform='translateY(-6px)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #fff7ed, #fed7aa); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
                        <i class="fas fa-island" style="font-size: 28px; color: #f97316;"></i>
                    </div>
                    <h3 style="margin: 0 0 12px 0; color: #1e293b; font-size: 1.1rem; font-weight: 700;">Remote & Island Areas</h3>
                    <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;">Deliveries to remote areas or island territories may take an additional 3-5 business days depending on courier accessibility. Surcharges may apply.</p>
                </div>

                <div class="info-card" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 15px 40px rgba(249, 115, 22, 0.15)'; this.style.transform='translateY(-6px)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #fff7ed, #fed7aa); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
                        <i class="fas fa-handshake" style="font-size: 28px; color: #f97316;"></i>
                    </div>
                    <h3 style="margin: 0 0 12px 0; color: #1e293b; font-size: 1.1rem; font-weight: 700;">Trusted Couriers</h3>
                    <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;">We partner with trusted couriers like J&T Express, Ninja Van, and LBC to ensure your package arrives safely and on time.</p>
                </div>

                <div class="info-card" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 15px 40px rgba(249, 115, 22, 0.15)'; this.style.transform='translateY(-6px)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #fff7ed, #fed7aa); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
                        <i class="fas fa-shield-alt" style="font-size: 28px; color: #f97316;"></i>
                    </div>
                    <h3 style="margin: 0 0 12px 0; color: #1e293b; font-size: 1.1rem; font-weight: 700;">Package Safety</h3>
                    <p style="margin: 0; color: #64748b; line-height: 1.7; font-size: 14px;">All packages are insured and carefully handled. In case of damage or loss, we provide full support and replacement options.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 16px; padding: 45px; text-align: center; color: white; box-shadow: 0 15px 40px rgba(249, 115, 22, 0.2);">
            <h2 style="margin: 0 0 15px 0; font-size: 1.8rem; font-weight: 800;">Questions About Shipping?</h2>
            <p style="margin: 0 0 25px 0; font-size: 1.05rem; opacity: 0.95;">Our customer service team is ready to help. Reach out anytime!</p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="Customer_Service.php?tab=chat" style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 30px; background: white; color: #f97316; text-decoration: none; border-radius: 8px; font-weight: 700; transition: all 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'">
                    <i class="fas fa-headset"></i> Live Chat
                </a>
                <a href="Customer_Service.php?tab=submit" style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 30px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 8px; font-weight: 700; border: 2px solid white; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(0)'">
                    <i class="fas fa-ticket-alt"></i> Submit Ticket
                </a>
            </div>
        </div>
    </div>

    <footer style="margin-top: 80px;">
        <?php include '../Components/footer.php'; ?>
    </footer>
</body>

</html>



