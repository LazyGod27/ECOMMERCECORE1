<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTS & OUTDOOR | IMARKETPH - Athletic Equipment & Outdoor Essentials</title>
    <link rel="icon" type="image/x-icon" href="../../image/logo.png">
    <link rel="stylesheet" href="../../css/components/category-base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .category-label {
            position: absolute;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            z-index: 20;
        }

        /* Optimize slideshow images for better resolution */
        .fade-slider {
            background-color: #f5f5f5;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
        }
        
        .fade-slide {
            width: 100%;
            height: 100%;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed;
        }

        .variant-swatches{display:flex;gap:6px;margin-top:8px;flex-wrap:wrap;position:absolute;bottom:8px;left:8px;z-index:5;}
        .variant-swatch{width:40px;height:40px;border-radius:6px;border:2px solid transparent;padding:0;cursor:pointer;background:#f0f0f0;overflow:hidden;background-size:cover;background-position:center;position:relative;transition:all 0.2s;}
        .variant-swatch:hover{transform:scale(1.08);border-color:#999}
        .variant-swatch.selected{border-color:#3b82f6;box-shadow:0 0 0 2px #3b82f6;transform:scale(1.15)}
        .variant-swatch img{width:100%;height:100%;object-fit:cover;display:block}
    </style>
</head>

<body>
    <nav>
        <?php
 $path_prefix = '../../';
        include '../../Components/header.php'; ?>
    </nav>

    <div class="content">
        <div class="best_selling-container">
            <!-- Slider Section (Right)-->
            <div class="slider-section">
                <div class="category-label">
                    ⚽ SPORTS & OUTDOOR
                </div>
                <div class="fade-slider">
                    <!-- Slides -->
                    <div class="fade-slide active"
                        style="background-image: url('../../image/sports-outdoor/Yoga%20Mat%20(Non-Slip).jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/sports-outdoor/Dumbbell%20Set%20(Adjustable).jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/sports-outdoor/Resistance%20Bands%20Set.jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/sports-outdoor/Jump%20Rope%20(Speed%20Type).jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/sports-outdoor/Sports%20Water%20Bottle%20(1L).jpeg');">
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
            <h2>⚽ Sports & Outdoor Gear</h2>
            <p>Athletic equipment & outdoor essentials</p>
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
            slideInterval = setInterval(nextSlide, 5000);
        }

        resetInterval();

        // Build variant swatches for product cards
        function buildSwatchesForElement(el) {
            try {
                const variantsAttr = el.getAttribute('data-variants');
                let variants = [];
                if (variantsAttr && variantsAttr.trim() !== '') {
                    try { variants = JSON.parse(variantsAttr); } catch(e) { variants = []; }
                }

                let normalized = [];
                if (variants && typeof variants === 'object' && !Array.isArray(variants)) {
                    for (const k in variants) {
                        if (variants.hasOwnProperty(k)) {
                            const v = variants[k];
                            if (typeof v === 'string') normalized.push({ key: k, image: v });
                            else normalized.push({ key: k, image: v.image || '' });
                        }
                    }
                }

                const imgEl = el.querySelector('img.product-img');
                const swatchContainer = el.querySelector('.variant-swatches');
                if (!swatchContainer || normalized.length === 0) return;
                swatchContainer.innerHTML = '';

                normalized.forEach((v, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'variant-swatch';
                    btn.title = v.key;
                    btn.dataset.image = v.image || '';
                    btn.dataset.key = v.key;

                    const img = document.createElement('img');
                    img.src = v.image || '';
                    img.alt = v.key;
                    img.onerror = function() { this.style.display = 'none'; btn.style.background = '#e0e0e0'; };
                    btn.appendChild(img);

                    if (i === 0) btn.classList.add('selected');
                    btn.onclick = function (ev) {
                        ev.stopPropagation();
                        if (imgEl && btn.dataset.image) imgEl.src = btn.dataset.image;
                        const sibs = swatchContainer.querySelectorAll('.variant-swatch');
                        sibs.forEach(s => s.classList.remove('selected'));
                        btn.classList.add('selected');
                    };
                    swatchContainer.appendChild(btn);
                });
            } catch (err) {
                console.error('buildSwatchesForElement error', err);
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            const nodes = document.querySelectorAll('[data-variants]');
            nodes.forEach(n => buildSwatchesForElement(n));
        });
    </script>

    <footer>
        <?php
 include '../../Components/footer.php'; ?>
    </footer>
</body>

</html>








