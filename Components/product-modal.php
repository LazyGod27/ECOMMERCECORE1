<!-- Product Details Modal (AI Search Result) -->
<link rel="stylesheet" href="../css/components/shared-product-view.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .product-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        backdrop-filter: blur(5px);
        animation: fadeIn 0.3s;
        overflow-y: auto;
        padding: 20px 0;
    }

    .product-modal-content {
        background: #fff;
        width: 1100px;
        max-width: 95%;
        display: flex;
        border-radius: 12px;
        position: relative;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s ease-out;
        margin: auto;
    }
    
    .pv-left {
        flex: 0 0 45%;
        min-height: 500px;
    }

    .pv-right {
        flex: 1;
        overflow-y: auto;
        max-height: 85vh;
        padding: 40px;
    }

    .product-modal-close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 32px;
        color: #666;
        cursor: pointer;
        z-index: 100;
        background: rgba(255, 255, 255, 0.9);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
        border: none;
        padding: 0;
        line-height: 1;
    }

    .product-modal-close:hover {
        background: #fff;
        color: #000;
        transform: rotate(90deg);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Enhanced Product Details */
    .pm-product-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1a1a1a;
        margin: 15px 0 20px 0;
        line-height: 1.3;
    }

    .pm-store-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .pm-store-badge {
        background: #2A3B7E;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .pm-description {
        color: #666;
        line-height: 1.6;
        margin: 20px 0;
        font-size: 0.95rem;
    }

    .pm-key-features {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }

    .pm-key-features h4 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pm-feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pm-feature-list li {
        padding: 6px 0;
        color: #666;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pm-feature-list li:before {
        content: "✓";
        color: #2A3B7E;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .pm-tooltip {
        font-size: 0.8rem;
        color: #999;
        margin-top: 8px;
        display: block;
    }

    .pm-rating-detail {
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 15px 0;
    }

    .pm-rating-stars {
        color: #ffc107;
        display: flex;
        gap: 3px;
    }

    .pm-rating-text {
        font-size: 0.9rem;
        color: #666;
    }

    .pm-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 20px 0;
    }

    @keyframes fadeIn { 
        from { opacity: 0; } 
        to { opacity: 1; } 
    }
    @keyframes slideUp { 
        from { transform: translateY(30px); opacity: 0; } 
        to { transform: translateY(0); opacity: 1; } 
    }

    @media (max-width: 900px) {
        .product-modal-content {
            flex-direction: column;
        }
        
        .pv-left {
            flex: 1;
            width: 100%;
            min-height: 400px;
        }
        
        .pv-right {
            max-height: none;
            padding: 30px;
        }
    }
</style>

<div id="product-modal-overlay" class="product-modal-overlay" onclick="if(event.target === this) closeProductModal()">
    <div class="product-modal-content">
        <button class="product-modal-close" onclick="closeProductModal()" aria-label="Close modal">&times;</button>

        <!-- LEFT SIDE: Product Image -->
        <div class="pv-left">
            <img id="pm-img" src="" alt="Product Image" class="pv-product-img" onerror="this.src='../image/no-image.png'">
        </div>

        <!-- RIGHT SIDE: Product Details -->
        <div class="pv-right">
            <!-- Header with Store Logo -->
            <div class="pv-header">
                <div class="pv-header-title">
                    <img src="../image/logo.png" alt="IMarket" class="pv-header-logo" onerror="this.style.display='none'"> 
                    <span id="pm-store" class="pm-store-badge">IMarket Official Store</span>
                </div>
            </div>

            <!-- Category -->
            <p class="pv-category" id="pm-category">Category</p>

            <!-- Product Title -->
            <h2 class="pm-product-title" id="pm-title">Loading Product...</h2>

            <!-- Store Info Badge -->
            <div class="pm-store-info">
                <i class="fas fa-shield-alt" style="color: #2A3B7E;"></i>
                <span style="font-size: 0.9rem; color: #666;">Official Store | Authentic Products</span>
            </div>

            <!-- Rating & Sold Info -->
            <div class="pv-meta">
                <div class="pv-rating" id="pm-rating">
                    <i class="fas fa-star"></i> 
                    <i class="fas fa-star"></i> 
                    <i class="fas fa-star"></i> 
                    <i class="fas fa-star"></i> 
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <span id="pm-sold" style="color: #666; font-size: 0.9rem;">1,250+ Sold</span>
            </div>

            <!-- Product Description -->
            <p class="pm-description" id="pm-description">
                High-quality product with excellent features. Perfect for everyday use with guaranteed authenticity and durability.
            </p>

            <!-- Key Features -->
            <div class="pm-key-features">
                <h4>Key Features</h4>
                <ul class="pm-feature-list" id="pm-features">
                    <li>Premium Quality Materials</li>
                    <li>Authentic & Guaranteed</li>
                    <li>Fast & Free Shipping</li>
                    <li>30-Day Money Back Guarantee</li>
                </ul>
            </div>

            <!-- Divider -->
            <div class="pm-divider"></div>

            <!-- Price Section -->
            <div class="pv-price-container">
                <span class="pv-price" id="pm-price">₱0.00</span>
                <span class="pv-original-price" id="pm-original-price" style="display:none;"></span>
                <span class="pv-discount-badge" id="pm-discount" style="display:none;"></span>
            </div>

            <!-- Options Section -->
            <div class="pv-options">
                <div class="pv-option-group">
                    <label for="pm-color" class="pv-option-label">Color / Variant</label>
                    <div class="pv-options" id="pm-color-options">
                        <button class="pv-option-btn selected" onclick="selectOption(this)">Standard</button>
                    </div>
                    <span class="pm-tooltip" id="pm-variant-note">Select a variant above</span>
                </div>

                <!-- Quantity Control -->
                <div class="pv-option-group">
                    <label for="pm-quantity" class="pv-option-label">Quantity</label>
                    <div class="pv-quantity-control">
                        <button class="pv-qty-btn" onclick="decreaseQty()">−</button>
                        <input type="number" id="pm-quantity" class="pv-qty-input" value="1" min="1" max="99" onchange="validateQty()">
                        <button class="pv-qty-btn" onclick="increaseQty()">+</button>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="pm-divider"></div>

            <!-- Action Buttons -->
            <div class="pv-actions">
                <a href="#" id="pm-cart-link" class="pv-btn pv-btn-cart">
                    <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i> Add to Cart
                </a>
                <a href="#" id="pm-buy-link" class="pv-btn pv-btn-buy">
                    <i class="fas fa-bolt" style="margin-right: 8px;"></i> Buy Now
                </a>
            </div>

            <!-- Additional Info -->
            <div style="margin-top: 30px; padding: 15px; background: #f0f4ff; border-radius: 8px; font-size: 0.9rem; color: #666;">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <i class="fas fa-truck" style="color: #2A3B7E; width: 20px;"></i>
                    <span><strong>Free Shipping</strong> on orders above ₱500</span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <i class="fas fa-undo" style="color: #2A3B7E; width: 20px;"></i>
                    <span><strong>Easy Returns</strong> within 30 days</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Quantity Controls
    function increaseQty() {
        const input = document.getElementById('pm-quantity');
        input.value = Math.min(parseInt(input.value) + 1, 99);
    }

    function decreaseQty() {
        const input = document.getElementById('pm-quantity');
        input.value = Math.max(parseInt(input.value) - 1, 1);
    }

    function validateQty() {
        const input = document.getElementById('pm-quantity');
        let val = parseInt(input.value) || 1;
        input.value = Math.max(1, Math.min(val, 99));
    }

    // Option Selection
    function selectOption(element) {
        const parent = element.parentElement;
        parent.querySelectorAll('.pv-option-btn').forEach(btn => btn.classList.remove('selected'));
        element.classList.add('selected');
    }

    // Open Modal with Product Data
    function openProductModal(productData) {
        const overlay = document.getElementById('product-modal-overlay');
        overlay.style.display = 'flex';
        
        // Reset quantity
        document.getElementById('pm-quantity').value = 1;
        
        // Hydrate data if passed
        if (productData) {
            // Image
            if (productData.image) {
                document.getElementById('pm-img').src = productData.image;
            }
            
            // Price
            if (productData.price) {
                document.getElementById('pm-price').innerText = productData.price;
            }
            
            // Title
            if (productData.name) {
                document.getElementById('pm-title').innerText = productData.name;
            }
            
            // Store
            if (productData.store) {
                document.getElementById('pm-store').innerText = productData.store || 'IMarket Official Store';
            }
            
            // Category
            if (productData.category) {
                document.getElementById('pm-category').innerText = productData.category;
            }
            
            // Description
            if (productData.description) {
                document.getElementById('pm-description').innerText = productData.description;
            }
            
            // Features
            if (productData.features && Array.isArray(productData.features)) {
                const featuresList = document.getElementById('pm-features');
                featuresList.innerHTML = productData.features
                    .map(feature => `<li>${feature}</li>`)
                    .join('');
            }
            
            // Discount Badge
            if (productData.discount) {
                const badge = document.getElementById('pm-discount');
                badge.innerText = `-${productData.discount}%`;
                badge.style.display = 'inline-block';
            } else {
                document.getElementById('pm-discount').style.display = 'none';
            }
            
            // Original Price (for strikethrough)
            if (productData.original_price) {
                const originalPrice = document.getElementById('pm-original-price');
                originalPrice.innerText = productData.original_price;
                originalPrice.style.display = 'inline';
            }
            
            // Build Cart Link
            const rawPrice = productData.raw_price || parseFloat(productData.price.replace(/[^0-9.]/g, '')) || 0;
            const quantity = document.getElementById('pm-quantity').value;
            
            document.getElementById('pm-cart-link').href = 
                `../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(productData.name)}&price=${rawPrice}&image=${encodeURIComponent(productData.image)}&quantity=${quantity}&store=${encodeURIComponent(productData.store || 'IMarket')}`;
            
            // Build Buy Now Link
            document.getElementById('pm-buy-link').href = 
                `../Content/Payment.php?product_name=${encodeURIComponent(productData.name)}&price=${rawPrice}&image=${encodeURIComponent(productData.image)}&quantity=${quantity}`;
        }
        
        // Scroll to top of modal
        document.querySelector('.pv-right').scrollTop = 0;
    }

    // Close Modal
    function closeProductModal() {
        document.getElementById('product-modal-overlay').style.display = 'none';
        
        // Clear URL params
        const url = new URL(window.location);
        url.searchParams.delete('ai_action');
        url.searchParams.delete('product');
        url.searchParams.delete('image');
        url.searchParams.delete('price');
        url.searchParams.delete('use_captured');
        window.history.pushState({}, '', url);
    }

    // Close modal on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeProductModal();
        }
    });

    // Check URL params on load
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.get('ai_action') === 'open_modal') {
            let imageUrl = urlParams.get('image') || '../image/no-image.png';

            // Use captured image from AI search if available
            if (urlParams.get('use_captured') === 'true') {
                const capturedImage = sessionStorage.getItem('ai_captured_image');
                if (capturedImage) {
                    imageUrl = capturedImage;
                }
            }

            // Product mapping for demo
            const productId = urlParams.get('product') || 'iphone_15_pro';
            const productMap = {
                'iphone_15_pro': {
                    name: 'iPhone 15 Pro Max 256GB',
                    price: '₱85,999',
                    raw_price: 85999,
                    description: 'Experience premium smartphone performance with advanced camera system, powerful processor, and stunning display.',
                    features: ['6.7" Super Retina XDR Display', 'A17 Pro Chip', 'Advanced Camera System', '5G Connectivity', 'All-day Battery'],
                    category: 'Electronics',
                    discount: 5,
                    original_price: '₱90,499'
                },
                'sneakers_casual': {
                    name: 'Premium Casual Sneakers - White',
                    price: '₱2,499',
                    raw_price: 2499,
                    description: 'Comfortable, stylish casual sneakers perfect for everyday wear.',
                    features: ['Breathable Mesh Upper', 'Cushioned Sole', 'Lightweight Design', 'Multiple Sizes Available'],
                    category: 'Fashion & Apparel',
                    discount: 10,
                    original_price: '₱2,777'
                },
                'hoodie_black': {
                    name: 'Loose Fit Hoodie - Black',
                    price: '₱1,999',
                    raw_price: 1999,
                    description: 'Comfortable and fashionable black hoodie for casual styling.',
                    features: ['100% Cotton Blend', 'Comfortable Fit', 'Kangaroo Pocket', 'Easy Care'],
                    category: 'Fashion & Apparel',
                    discount: 15,
                    original_price: '₱2,353'
                },
                'webcam_1080p': {
                    name: '1080p HD Web Camera with Microphone',
                    price: '₱3,999',
                    raw_price: 3999,
                    description: 'Professional-grade webcam for streaming, conferencing, and content creation.',
                    features: ['1080p Full HD', 'Built-in Microphone', 'Wide-angle Lens', 'USB Plug & Play'],
                    category: 'Electronics',
                    discount: 8,
                    original_price: '₱4,347'
                }
            };

            const productData = productMap[productId] || {
                name: 'Premium Product',
                price: urlParams.get('price') || '₱0.00',
                raw_price: parseFloat(urlParams.get('price')?.replace(/[^0-9.]/g, '') || 0),
                image: imageUrl,
                store: 'IMarket Official Store',
                category: 'Electronics',
                description: 'High-quality product with excellent features.',
                features: ['Premium Quality', 'Authentic Product', 'Fast Shipping', '30-Day Guarantee']
            };

            // Ensure image is set
            productData.image = imageUrl;
            productData.store = urlParams.get('store') || 'IMarket Official Store';

            openProductModal(productData);
        }
    });
</script>


