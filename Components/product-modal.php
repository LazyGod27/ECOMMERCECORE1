<!-- Product Details Modal (AI Search Result) -->
<link rel="stylesheet" href="../css/components/shared-product-view.css?v=<?php echo time(); ?>">
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
    }

    .product-modal-content {
        background: #fff;
        width: 1000px;
        max-width: 95%;
        max-height: 90vh; /* Prevent growing off-screen */
        display: flex;
        border-radius: 12px;
        position: relative;
        /* overflow: hidden; REMOVED to prevent clipping buttons */
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s ease-out;
    }
    
    .pv-right {
        overflow-y: auto; /* Allow scrolling for details */
    }

    .product-modal-close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 32px;
        color: #666;
        cursor: pointer;
        z-index: 100;
        background: rgba(255, 255, 255, 0.8);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .product-modal-close:hover {
        background: #fff;
        color: #000;
        transform: rotate(90deg);
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<div id="product-modal-overlay" class="product-modal-overlay" onclick="if(event.target === this) closeProductModal()">
    <div class="product-modal-content">
        <span class="product-modal-close" onclick="closeProductModal()">&times;</span>

        <div class="pv-left">
            <img id="pm-img" src="" alt="Product Image" class="pv-product-img">
        </div>

        <div class="pv-right">
            <div class="pv-header">
                <div class="pv-header-title">
                    <img src="../image/logo.png" class="pv-header-logo"> |
                    <span id="pm-store">IMarket Official Store</span>
                </div>
                <p class="pv-category" id="pm-category">Category</p>
            </div>

            <h2 class="pm-product-title pv-title" id="pm-title">Product Title</h2>
            
            <div class="pv-meta">
                <div class="pv-rating" id="pm-rating">
                    <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i>
                </div>
                <span id="pm-sold">Sold Out</span>
            </div>

            <div class="pv-price-container">
                <span class="pv-price" id="pm-price">₱0.00</span>
            </div>

            <div class="pv-options">
                <div class="pv-option-group">
                    <span class="pv-option-label">Color</span>
                    <div class="pv-options">
                        <button class="pv-option-btn selected">Standard</button>
                    </div>
                </div>
            </div>

            <div class="pv-actions">
                <a href="#" id="pm-cart-link" class="pv-btn pv-btn-cart">Add to Cart</a>
                <a href="#" id="pm-buy-link" class="pv-btn pv-btn-buy">Buy Now</a>
            </div>
        </div>
    </div>
</div>

<script>
    function openProductModal(productData) {
        document.getElementById('product-modal-overlay').style.display = 'flex';
        // Hydrate data if passed
        if (productData) {
            if (productData.image) document.getElementById('pm-img').src = productData.image;
            if (productData.price) document.getElementById('pm-price').innerText = productData.price;
            if (productData.name) document.getElementById('pm-title').innerText = productData.name;
            if (productData.store) document.getElementById('pm-store').innerText = productData.store;
            if (productData.category) document.getElementById('pm-category').innerText = productData.category;
            
            const rawPrice = productData.raw_price || parseFloat(productData.price.replace(/[^0-9.]/g, '')) || 0;
            
            document.getElementById('pm-cart-link').href = `../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(productData.name)}&price=${rawPrice}&image=${encodeURIComponent(productData.image)}&quantity=1&store=${encodeURIComponent(productData.store || 'IMarket')}`;
            document.getElementById('pm-buy-link').href = `../Content/Payment.php?product_name=${encodeURIComponent(productData.name)}&price=${rawPrice}&image=${encodeURIComponent(productData.image)}&quantity=1`;
        }
    }

    function closeProductModal() {
        document.getElementById('product-modal-overlay').style.display = 'none';
        // Clear params to avoid reopening on refresh
        const url = new URL(window.location);
        url.searchParams.delete('ai_action');
        url.searchParams.delete('product');
        window.history.pushState({}, '', url);
    }

    // Check URL params on load
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('ai_action') === 'open_modal') {

            let imageUrl = urlParams.get('image');

            // Check if we should use the captured image from AI search
            if (urlParams.get('use_captured') === 'true') {
                const capturedImage = sessionStorage.getItem('ai_captured_image');
                if (capturedImage) {
                    imageUrl = capturedImage;
                }
            }

            // Simple map for names based on ID (since PHP redirect doesn't pass full name yet)
            const productId = urlParams.get('product');
            let productName = "iPhone 15 Pro Max";
            if (productId === 'sneakers_casual') productName = "Casual Sneakers";
            if (productId === 'hoodie_black') productName = "H&M Loose Fit Hoodie";
            if (productId === 'webcam_1080p') productName = "1080p HD Web Camera";

            const productData = {
                id: productId,
                price: urlParams.get('price'),
                image: imageUrl,
                name: productName,
                store: "TechZone PH",
                category: "Gadgets"
            };
            openProductModal(productData);
        }
    });
</script>


