<?php
// DAPAT ITO ANG LINE 1, NO SPACES BEFORE THIS

include_once(__DIR__ . '/../../Components/security.php');
include_once(__DIR__ . '/../../Database/config.php');

// Dito lang magsisimula ang HTML mo
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLASH DEALS | IMARKETPH - Limited Time Offers</title>
    <link rel="icon" type="image/x-icon" href="../../image/logo.png">
    <link rel="stylesheet" href="../../css/components/category-base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-navy: #2A3B7E;
            --primary-dark: #1a2657;
            --accent-blue: #3b82f6;
            --success-green: #10b981;
            --danger-red: #ef4444;
            --warning-orange: #f59e0b;
        }

        .variant-swatches{display:flex;gap:6px;margin-top:8px;flex-wrap:wrap}
        .variant-swatch{width:40px;height:40px;border-radius:6px;border:2px solid transparent;padding:0;cursor:pointer;background:#f0f0f0;overflow:hidden;background-size:cover;background-position:center;position:relative;transition:all 0.2s}
        .variant-swatch:hover{transform:scale(1.08);border-color:#999}
        .variant-swatch.selected{border-color:#3b82f6;box-shadow:0 0 0 2px #3b82f6;transform:scale(1.15)}
        .variant-swatch img{width:100%;height:100%;object-fit:cover;display:block}

        body {
            background: #f8fafc;
        }

        /* Flash Deals Hero Banner - Professional Mall Style */
        .flash-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2d3748 100%);
            padding: 100px 20px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .flash-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .flash-hero::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(30px); }
        }

        .flash-hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
            animation: slideDown 0.8s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .flash-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
            padding: 10px 25px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1px;
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
            animation: pulse-badge 2s infinite;
        }

        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .badge-icon {
            font-size: 18px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .flash-hero-title {
            font-size: 56px;
            font-weight: 900;
            margin: 0 0 15px 0;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
        }

        .flash-hero-subtitle {
            font-size: 18px;
            color: #cbd5e1;
            margin: 0 0 35px 0;
            line-height: 1.6;
        }

        .flash-info {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            padding: 16px 28px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-3px);
        }

        .info-item i {
            font-size: 22px;
            color: #fbbf24;
        }

        .info-item span {
            font-weight: 700;
            font-size: 15px;
        }

        /* Flash Deals Container */
        .content {
            padding: 60px 20px;
            background: #f8fafc;
        }

        .flash-deals-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .section-title h2 {
            font-size: 40px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 12px 0;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ef4444 0%, #f97316 100%);
            border-radius: 2px;
        }

        .section-title p {
            color: #64748b;
            font-size: 16px;
            margin: 20px 0 0 0;
        }

        /* Flash Products Grid - Professional Mall Layout */
        .flash-products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
            margin-top: 40px;
        }

        .flash-product-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
            animation: slideInUp 0.6s ease-out forwards;
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
        }

        .flash-product-card:nth-child(1) { animation-delay: 0.05s; }
        .flash-product-card:nth-child(2) { animation-delay: 0.1s; }
        .flash-product-card:nth-child(3) { animation-delay: 0.15s; }
        .flash-product-card:nth-child(4) { animation-delay: 0.2s; }
        .flash-product-card:nth-child(5) { animation-delay: 0.25s; }
        .flash-product-card:nth-child(6) { animation-delay: 0.3s; }
        .flash-product-card:nth-child(7) { animation-delay: 0.35s; }
        .flash-product-card:nth-child(8) { animation-delay: 0.4s; }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .flash-product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: #cbd5e1;
        }

        /* Badge Section - Professional */
        .flash-badges {
            position: absolute;
            top: 16px;
            right: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 10;
        }

        .discount-badge {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 18px;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            text-align: center;
            min-width: 70px;
        }

        .flash-badge {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 7px 14px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
            animation: flash-glow 1.5s infinite;
        }

        @keyframes flash-glow {
            0%, 100% { box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4); }
            50% { box-shadow: 0 10px 30px rgba(249, 115, 22, 0.6); }
        }

        /* Image Container - Professional */
        .product-image-container {
            position: relative;
            height: 280px;
            overflow: hidden;
            background: linear-gradient(135deg, #f0f0f0 0%, #e5e5e5 100%);
            flex-shrink: 0;
        }

        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .flash-product-card:hover .product-image-container img {
            transform: scale(1.12);
        }

        /* Product Info - Better Layout */
        .product-info {
            padding: 22px 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .shop-name {
            font-size: 11px;
            color: #3b82f6;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .shop-name::before {
            content: '🏪';
            font-size: 12px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 12px 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .price-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .original-price {
            font-size: 14px;
            color: #94a3b8;
            text-decoration: line-through;
            font-weight: 500;
        }

        .sale-price {
            font-size: 24px;
            font-weight: 900;
            color: #ef4444;
        }

        /* Countdown - Professional */
        .product-countdown {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            color: #991b1b;
            font-weight: 700;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }

        .product-countdown i {
            color: #ef4444;
            font-size: 14px;
            animation: shake 1s infinite;
        }

        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-4deg); }
            75% { transform: rotate(4deg); }
        }

        .countdown-timer {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Variant Swatches - Better Visual */
        .variant-swatches {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .variant-swatch {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            padding: 0;
            cursor: pointer;
            background: #f1f5f9;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .variant-swatch:hover {
            border-color: #94a3b8;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .variant-swatch.selected {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
            transform: scale(1.15);
        }

        .variant-swatch img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Buttons - Professional Mall Style */
        .add-to-cart-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2A3B7E 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            margin-top: auto;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 59, 126, 0.4);
        }

        .add-to-cart-btn:active {
            transform: translateY(0);
        }

        .quick-checkout-btn {
            transition: all 0.3s ease !important;
            margin-top: 8px;
            background-image: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border: none !important;
            color: white !important;
            cursor: pointer !important;
            padding: 14px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            width: 100% !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .quick-checkout-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4) !important;
        }

        .quick-checkout-btn:active {
            transform: translateY(0) !important;
        }

        @media (max-width: 768px) {
            .flash-hero {
                padding: 60px 20px;
            }

            .flash-hero-title {
                font-size: 36px;
            }

            .flash-hero-subtitle {
                font-size: 15px;
            }

            .flash-info {
                gap: 20px;
                flex-direction: column;
            }

            .info-item {
                width: 100%;
                justify-content: center;
            }

            .flash-products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }

            .product-image-container {
                height: 220px;
            }

            .section-title h2 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <nav>
        <?php
        $path_prefix = '../../';
        include '../../Components/header.php';
        ?>
    </nav>

    <!-- Flash Deals Hero Banner -->
    <section class="flash-hero">
        <div class="flash-hero-content">
            <div class="flash-hero-badge">
                <span class="badge-icon">⚡</span>
                <span class="badge-text">LIMITED TIME OFFERS</span>
            </div>
            <h1 class="flash-hero-title">
                FLASH DEALS
            </h1>
            <p class="flash-hero-subtitle">Discover amazing discounts on premium products - Grab them before they're gone!</p>
            <div class="flash-info">
                <div class="info-item">
                    <i class="fas fa-fire"></i>
                    <span>Up to 70% OFF</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span>Limited Stock</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Fast Delivery</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Flash Deals Products -->
    <div class="content">
        <div class="flash-deals-container">
            <div class="section-title">
                <h2>Today's Best Deals</h2>
                <p>Flash sales updated throughout the day - Shop premium items at unbeatable prices</p>
            </div>

            <?php include 'card.php'; ?>
        </div>
    </div>

    <footer>
        <?php include '../../Components/footer.php'; ?>
    </footer>

    <script>
        // Generate variants for Flash Deals products
        function generateVariantsForProduct(prodName, baseImage = '') {
            const n = prodName.toLowerCase();

            if (n.includes('bag')) {
                return {
                    'Black': {image: '../../image/Best-seller/bag-men.jpeg', color: '#111111'},
                    'Brown': {image: '../../image/Best-seller/bag-women.jpeg', color: '#8b4513'}
                };
            } else if (n.includes('laptop') || n.includes('computer')) {
                return {
                    'Silver': {image: '../../image/Best-seller/laptop.jpeg', color: '#b8c2cc'},
                    'Black': {image: '../../image/Best-seller/pc%20computer.avif', color: '#111111'}
                };
            } else if (n.includes('phone') || n.includes('iphone')) {
                return {
                    'White': {image: '../../image/Best-seller/iphone.jpeg', color: '#ffffff'},
                    'Black': {image: '../../image/Best-seller/iphone.jpeg', color: '#111111'}
                };
            } else if (n.includes('keyboard')) {
                return {
                    'Black': {image: '../../image/Best-seller/Keyboard-maagas.jpeg', color: '#111111'},
                    'White': {image: '../../image/Best-seller/Keyboard-maagas.jpeg', color: '#ffffff'}
                };
            } else if (n.includes('watch')) {
                return {
                    'Silver': {image: '../../image/Best-seller/Snart%20watch.jpeg', color: '#b8c2cc'},
                    'Black': {image: '../../image/Best-seller/Snart%20watch.jpeg', color: '#111111'}
                };
            } else if (n.includes('shoe') || n.includes('sneaker')) {
                return {
                    'White': {image: '../../image/Best-seller/snikers%20shoes.avif', color: '#ffffff'},
                    'Black': {image: '../../image/Best-seller/snikers%20shoes.avif', color: '#111111'}
                };
            } else {
                return {
                    'Variant 1': {image: baseImage, color: '#111111'},
                    'Variant 2': {image: baseImage, color: '#3b82f6'}
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

        // Add to Cart Handler
        function addToCart(button) {
            const card = button.closest('.flash-product-card');
            const productName = card.querySelector('.product-name').textContent.trim();
            const salePrice = card.querySelector('.sale-price').textContent.replace('₱', '').replace(/,/g, '');
            const image = card.querySelector('.product-image-container img').src;
            const variant = card.querySelector('.variant-swatch.selected')?.getAttribute('title') || 'Default';

            // Create form and submit to add-to-cart
            const formData = new FormData();
            formData.append('product_name', productName);
            formData.append('product_price', salePrice);
            formData.append('product_image', image);
            formData.append('variant', variant);
            formData.append('quantity', 1);

            fetch('../../Content/add-to-cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Show success message
                showNotification('✓ Added to cart!', 'success');
                // Optional: Update cart count
                setTimeout(() => {
                    location.reload(); // Refresh to show updated cart
                }, 1500);
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding to cart', 'error');
            });
        }

        // Quick Checkout Handler
        function quickCheckout(button) {
            const card = button.closest('.flash-product-card');
            const productName = card.querySelector('.product-name').textContent.trim();
            const salePrice = card.querySelector('.sale-price').textContent.replace('₱', '').replace(/,/g, '');
            const image = card.querySelector('.product-image-container img').src;
            const variant = card.querySelector('.variant-swatch.selected')?.getAttribute('title') || 'Default';

            // Create form and submit to Payment page
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../Content/Payment.php';

            const fields = {
                'product_name': productName,
                'product_price': salePrice,
                'product_image': image,
                'variant': variant,
                'quantity': 1,
                'quick_checkout': 'true'
            };

            for (const key in fields) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        }

        // Show notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                border-radius: 8px;
                font-weight: 600;
                z-index: 9999;
                animation: slideIn 0.4s ease-out;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.4s ease-out';
                setTimeout(() => notification.remove(), 400);
            }, 2000);
        }

        // Update cart count
        function updateCartCount() {
            const cartIcon = document.querySelector('[data-cart-count]');
            if (cartIcon) {
                fetch('../../php/get_cart_count.php')
                    .then(r => r.json())
                    .then(data => {
                        cartIcon.setAttribute('data-cart-count', data.count || 0);
                    })
                    .catch(e => console.log('Could not update cart count'));
            }
        }

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(100px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            @keyframes slideOut {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(100px);
                }
            }
        `;
        document.head.appendChild(style);

        // Initialize swatches on page load
        window.addEventListener('DOMContentLoaded', function() {
            const nodes = document.querySelectorAll('[data-variants]');
            nodes.forEach(n => buildSwatchesForElement(n));

            // Get cart count and update header
            updateCartCount();
        });

        // Countdown Timer
        function updateCountdowns() {
            const timers = document.querySelectorAll('.countdown-timer');
            timers.forEach(timer => {
                let seconds = parseInt(timer.getAttribute('data-time')) || 3600;
                
                const interval = setInterval(() => {
                    if (seconds > 0) {
                        seconds--;
                        
                        const hours = Math.floor(seconds / 3600);
                        const minutes = Math.floor((seconds % 3600) / 60);
                        const secs = seconds % 60;
                        
                        timer.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                    }
                }, 1000);
            });
        }

        window.addEventListener('DOMContentLoaded', updateCountdowns);
    </script>

</body>

</html>
