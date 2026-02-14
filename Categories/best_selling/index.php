<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEST SELLING | IMARKETPH - Top Trending Products</title>
    <link rel="icon" type="image/x-icon" href="../../image/logo.png">
    <link rel="stylesheet" href="../../css/components/category-base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-navy: #2A3B7E;
            --accent-blue: #3b82f6;
            --success-green: #10b981;
            --warning-orange: #f59e0b;
            --danger-red: #ef4444;
        }

        .variant-swatches{display:flex;gap:6px;margin-top:8px;flex-wrap:wrap}
        .variant-swatch{width:40px;height:40px;border-radius:6px;border:2px solid transparent;padding:0;cursor:pointer;background:#f0f0f0;overflow:hidden;background-size:cover;background-position:center;position:relative;transition:all 0.2s}
        .variant-swatch:hover{transform:scale(1.08);border-color:#999}
        .variant-swatch.selected{border-color:#3b82f6;box-shadow:0 0 0 2px #3b82f6;transform:scale(1.15)}
        .variant-swatch img{width:100%;height:100%;object-fit:cover;display:block}

        .category-label {
            position: absolute;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, var(--accent-blue) 0%, var(--danger-red) 100%);
            color: white;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            z-index: 20;
        }

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
            <!-- Slider Section (Right) -->
            <div class="slider-section">
                <div class="category-label">
                    <i class="fas fa-fire"></i> BEST SELLERS
                </div>
                <div class="fade-slider">
                    <!-- Slides -->
                    <div class="fade-slide active"
                        style="background-image: url('../../image/Best-seller/bag-men.jpeg');">
                    </div>
                    <div class="fade-slide" style="background-image: url('../../image/Best-seller/bag-women.jpeg');">
                    </div>
                    <div class="fade-slide"
                        style="background-image: url('../../image/Best-seller/Earphone-bluetooth.jpeg');">
                    </div>
                    <div class="fade-slide" style="background-image: url('../../image/Best-seller/Relo.jpeg');"></div>
                    <div class="fade-slide" style="background-image: url('../../image/Best-seller/School-bag.jpg');">
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
            <h2>🔥 Top Selling Products</h2>
            <p>Discover the products our customers love most - Premium quality at great prices!</p>
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
    </script>

    <footer>
        <?php
 include '../../Components/footer.php'; ?>
    </footer>

    <script>
        // Generate placeholder variants (same logic as Shop)
        function generateVariantsForProduct(prodName, baseImage = '') {
            const n = prodName.toLowerCase();
            const palettes = [];

            if (n.includes('bag')) {
                return {
                    'Black': ['image' => '../../image/Best-seller/bag-men.jpeg', 'color' => '#111111'],
                    'Brown': ['image' => '../../image/Best-seller/bag-women.jpeg', 'color' => '#8b4513']
                };
            } else if (n.includes('laptop') || n.includes('computer')) {
                return {
                    'Silver': ['image' => '../../image/Best-seller/laptop.jpeg', 'color' => '#b8c2cc'],
                    'Black': ['image' => '../../image/Best-seller/pc%20computer.avif', 'color' => '#111111']
                };
            } else if (n.includes('phone') || n.includes('iphone')) {
                return {
                    'White': ['image' => '../../image/Best-seller/iphone.jpeg', 'color' => '#ffffff'],
                    'Black': ['image' => '../../image/Best-seller/iphone.jpeg', 'color' => '#111111']
                };
            } else if (n.includes('keyboard')) {
                return {
                    'Black': ['image' => '../../image/Best-seller/Keyboard-maagas.jpeg', 'color' => '#111111'],
                    'White': ['image' => '../../image/Best-seller/Keyboard-maagas.jpeg', 'color' => '#ffffff']
                };
            } else if (n.includes('watch')) {
                return {
                    'Silver': ['image' => '../../image/Best-seller/Snart%20watch.jpeg', 'color' => '#b8c2cc'],
                    'Black': ['image' => '../../image/Best-seller/Snart%20watch.jpeg', 'color' => '#111111']
                };
            } else if (n.includes('shoe') || n.includes('sneaker')) {
                return {
                    'White': ['image' => '../../image/Best-seller/snikers%20shoes.avif', 'color' => '#ffffff'],
                    'Black': ['image' => '../../image/Best-seller/snikers%20shoes.avif', 'color' => '#111111']
                };
            } else {
                // Fallback variants
                return {
                    'Variant 1': ['image' => baseImage, 'color' => '#111111'],
                    'Variant 2': ['image' => baseImage, 'color' => '3b82f6']
                };
            }
        }

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
                            if (typeof v === 'string') normalized.push({ key: k, image: v, color: '' });
                            else normalized.push({ key: k, image: v.image || '', color: v.color || '' });
                        }
                    }
                }

                const name = el.getAttribute('data-name') || 'Product';
                const baseImg = el.getAttribute('data-image') || '';
                if (normalized.length === 0) {
                    const gen = generateVariantsForProduct(name, baseImg);
                    for (const k in gen) normalized.push({ key: k, image: gen[k].image, color: gen[k].color });
                }

                const imgEl = el.querySelector('img.product-img');
                const swatchContainer = el.querySelector('.variant-swatches');
                if (!swatchContainer) return;
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

        // Initialize swatches on page load
        window.addEventListener('DOMContentLoaded', function() {
            const nodes = document.querySelectorAll('[data-variants]');
            nodes.forEach(n => buildSwatchesForElement(n));
        });
    </script>
</body>

</html>








