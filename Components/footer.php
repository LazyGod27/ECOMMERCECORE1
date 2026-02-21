<?php
if (!isset($path_prefix)) {
    $path_prefix = '../';
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo $path_prefix; ?>css/components/footer.css">

<footer class="footer">
    <div class="footer-container">
        <!-- Customer Care -->
        <div class="footer-column">
            <h4>Customer Care</h4>
            <ul>
                <li><a href="<?php echo $path_prefix; ?>Services/Customer_Service.php">Customer Service</a></li>
                <li><a href="<?php echo $path_prefix; ?>Services/How_to_buy.php">How to Buy</a></li>
                <li><a href="<?php echo $path_prefix; ?>Services/Shipping%20%26%20Delivery.php">Shipping & Delivery</a>
                </li>
                <li><a href="<?php echo $path_prefix; ?>Services/Return%20%26%20Refund.php">Return & Refund</a></li>
                <li><a href="<?php echo $path_prefix; ?>Services/Contact%20Us.php">Contact Us</a></li>
            </ul>
        </div>

        <!-- About ImarketPH -->
        <div class="footer-column">
            <h4>About ImarketPH</h4>
            <ul>
                <li><a href="<?php echo $path_prefix; ?>About/About_Us.php">About Us</a></li>
                <li><a href="<?php echo $path_prefix; ?>Sellers/index.php">Our Sellers</a></li>
                <li><a href="<?php echo $path_prefix; ?>About/Careers.php">Careers</a></li>
                <li><a href="<?php echo $path_prefix; ?>About/Terms%20%26%20Conditions.php">Terms & Conditions</a></li>
                <li><a href="<?php echo $path_prefix; ?>About/Privacy%20Policy.php">Privacy Policy</a></li>
            </ul>
        </div>

        <!-- Payment Methods -->
        <div class="footer-column">
            <h4>Payment Methods</h4>
            <div class="payment-icons">
                <img src="<?php echo $path_prefix; ?>image/Banks/visa.png" alt="Visa" class="pay-icon">
                <img src="<?php echo $path_prefix; ?>image/Banks/Master-card.png" alt="Mastercard" class="pay-icon">
                <img src="<?php echo $path_prefix; ?>image/Banks/Gcash.jpeg" alt="GCash" class="pay-icon">
                <img src="<?php echo $path_prefix; ?>image/Banks/Maya.png" alt="Maya" class="pay-icon">
            </div>
        </div>

        <!-- Delivery Services -->
        <div class="footer-column">
            <h4>Delivery Services</h4>
            <div class="delivery-icons">
                <img src="<?php echo $path_prefix; ?>image/Delivery/jnt.png" alt="J&T" class="del-icon">
                <img src="<?php echo $path_prefix; ?>image/Delivery/ninjavan.png" alt="NinjaVan" class="del-icon">
                <img src="<?php echo $path_prefix; ?>image/Delivery/lbc.png" alt="LBC" class="del-icon">
                <img src="<?php echo $path_prefix; ?>image/Delivery/flash.jpeg" alt="Flash" class="del-icon">
            </div>
        </div>

        <!-- Follow Us -->
        <div class="footer-column">
            <h4>Follow Us</h4>
            <div class="social-icons">
                <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>



    </div>

    <div class="footer-bottom">
        <p>&copy; 2025 All Rights Reserved by ImarketPH</p>
    </div>
</footer>
