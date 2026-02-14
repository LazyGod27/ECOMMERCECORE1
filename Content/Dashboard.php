<?php
session_start();

// 1. The Gatekeeper: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

// 2. The Time Limit (Session Timeout): Auto-logout after 30 minutes of inactivity
$timeout_duration = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../php/login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time

// 3. The Invisible Shield (Security Headers)
// Prevents other sites from putting your site in a frame (Clickjacking)
header("X-Frame-Options: DENY");
// Helps prevent Cross-Site Scripting (XSS)
header("X-XSS-Protection: 1; mode=block");
// Prevents the browser from guessing the media type (Mime Sniffing)
header("X-Content-Type-Options: nosniff");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD - IMARKET PH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard/Dashboard.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/components/footer.css">
</head>

<body>
    <nav>
        <?php include '../Components/header.php'; ?>
    </nav>

    <div class="hero-section">
        <div class="hero-slider">
            <div class="slide active" style="background-image: url('../image/Dashboard/f1.avif');"></div>
            <div class="slide" style="background-image: url('../image/Dashboard/msi.jpg');"></div>
            <div class="slide" style="background-image: url('../image/Dashboard/kobe.jpg');"></div>
            <div class="slide" style="background-image: url('../image/Dashboard/jbl.webp');"></div>
            <div class="slide" style="background-image: url('../image/Dashboard/watch.jpg');"></div>
        </div>

        <div class="hero-content">
            <span class="welcome-text">WELCOME TO</span>
            <br>
            <h1 class="brand-title">IMARKET PH</h1>
            <p class="hero-subtitle">Your Ultimate Shopping Destination in the Philippines</p>


            <div class="hero-buttons">
                <a href="../Shop/index.php?store=UrbanWear+PH" class="btn-shop">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
                <a href="../Categories/flash-deals/index.php" class="btn-flash">
                    <i class="fas fa-bolt"></i> Flash Deals
                </a>
                <a href="../Categories/best_selling/index.php" class="btn-best">
                    <i class="fas fa-fire"></i> Best Sellers
                </a>
            </div>
        </div>

        <div class="slider-indicators">
            <span class="indicator active" onclick="currentSlide(0)"></span>
            <span class="indicator" onclick="currentSlide(1)"></span>
            <span class="indicator" onclick="currentSlide(2)"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
        </div>

        <?php include '../Components/floating-buttons.php'; ?>
        <?php include '../Components/ai_confirmation_modal.php'; ?>
        <?php include '../Components/product-modal.php'; ?>
        <script>
            let currentSlideIndex = 0;
            const slides = document.querySelectorAll('.slide');
            const indicators = document.querySelectorAll('.indicator');
            const totalSlides = slides.length;
            let slideInterval;

            function showSlide(index) {
                // Remove active class from all slides and indicators
                slides.forEach(slide => slide.classList.remove('active'));
                indicators.forEach(indicator => indicator.classList.remove('active'));

                // Handle wrapping
                if (index >= totalSlides) currentSlideIndex = 0;
                else if (index < 0) currentSlideIndex = totalSlides - 1;
                else currentSlideIndex = index;

                // Add active class to current slide and indicator
                slides[currentSlideIndex].classList.add('active');

                // Safe check for indicator existence to avoid errors if mismatch
                if (indicators[currentSlideIndex]) {
                    indicators[currentSlideIndex].classList.add('active');
                }
            }

            function nextSlide() {
                showSlide(currentSlideIndex + 1);
            }

            function currentSlide(index) {
                showSlide(index);
                resetInterval();
            }

            function resetInterval() {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
            }

            // Initialize slider
            showSlide(currentSlideIndex);
            resetInterval();
        </script>


    <div style="background: white; padding-top: 50px;">
    </div>
</body>

</html>
