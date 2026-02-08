<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOYS & GAMES | IMARKETPH - IMarket</title>
    <link rel="icon" type="image/x-icon" href="../../image/logo.png">
    <link rel="stylesheet" href="../../css/components/category-base.css?v=<?php echo time(); ?>">
</head>

<body>
    <nav>
        <?php
 $path_prefix = '../../';
        include '../../Components/header.php'; ?>
    </nav>

    <div class="content">
        <div class="best_selling-container">
            <!-- Text Section (Left) -->
            

            <!-- Slider Section (Right)-->
            <div class="slider-section">
                <div class="fade-slider">
                    <!-- Slides -->
                    <div class="fade-slide active"
                        style="background-image: url('../../image/toys-games/Action%20Figure%20(Superhero).jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/toys-games/Board%20Game%20(Strategy).jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/toys-games/Building%20Blocks%20Set.jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/toys-games/Card%20Game%20(Family%20Fun).jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/toys-games/Doll%20House%20(Wooden).jpeg');">
                    </div>

                    <!-- Indicators (White Lines) -->
                    <div class="slider-indicators">
                        <span class="indicator active" onclick="goToSlide(0)"></span>
                        <span class="indicator" onclick="goToSlide(1)"></span>
                        <span class="indicator" onclick="goToSlide(2)"></span>
                        <span class="indicator" onclick="goToSlide(3)"></span>
                        <span class="indicator" onclick="goToSlide(4)"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="section-header">
            <h2>Toys & Games</h2>
            <p>Check out our Toys & Games!</p>
        </div>
        <?php
 include 'card.php'; ?>
    </div>



    <script>
        let currentSlideIndex = 0;
        const slides = document.querySelectorAll('.fade-slide');
        const indicators = document.querySelectorAll('.indicator');
        const totalSlides = slides.length;
        let slideInterval;

        function showSlide(index) {
            if (index >= totalSlides) index = 0;
            if (index < 0) index = totalSlides - 1;

            // Allow transition
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));

            currentSlideIndex = index;
            slides[currentSlideIndex].classList.add('active');
            indicators[currentSlideIndex].classList.add('active');
        }

        function nextSlide() {
            showSlide(currentSlideIndex + 1);
        }

        function goToSlide(index) {
            showSlide(index);
            resetInterval();
        }

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 4000);
        }

        resetInterval();
    </script>

    <footer>
        <?php
 include '../../Components/footer.php'; ?>
    </footer>
</body>

</html>








