<?php
/**
 * IMARKET PH Mall Hero Component
 * Modern, professional hero section for the shopping experience
 * Standalone component integrated into Shop system
 */
?>

<style>
    /* Mall Hero Container */
    .mall-hero-section {
        position: relative;
        width: 100%;
        min-height: 650px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0c1d2f 100%);
        overflow: hidden;
        margin: 0;
        padding: 0;
    }

    /* Animated Background Elements */
    .hero-animated-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        z-index: 0;
    }

    .hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(100px);
        opacity: 0.3;
        animation: float-orb 15s ease-in-out infinite;
    }

    .hero-orb-1 {
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
        top: -100px;
        left: -100px;
        animation-delay: 0s;
    }

    .hero-orb-2 {
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, #a855f7 0%, transparent 70%);
        bottom: -50px;
        right: 10%;
        animation-delay: 3s;
    }

    .hero-orb-3 {
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, #06b6d4 0%, transparent 70%);
        bottom: 100px;
        left: 5%;
        animation-delay: 6s;
    }

    @keyframes float-orb {
        0%, 100% { transform: translate(0, 0); }
        33% { transform: translate(30px, -40px); }
        66% { transform: translate(-20px, 40px); }
    }

    .hero-grid {
        position: absolute;
        width: 100%;
        height: 100%;
        background-image: 
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(0deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 50px 50px;
        background-position: 0 0;
        opacity: 0.5;
        z-index: 1;
    }

    /* Hero Content Container */
    .hero-content-wrapper {
        position: relative;
        z-index: 10;
        height: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 40px;
    }

    /* Left Content */
    .hero-left-content {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
        padding: 10px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #60a5fa;
        backdrop-filter: blur(10px);
        animation: slide-in-left 0.8s ease-out;
    }

    .hero-badge i {
        font-size: 0.9rem;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -1px;
        color: white;
        margin: 0;
        animation: slide-in-left 0.8s ease-out 0.1s backwards;
    }

    .hero-title .gradient-text {
        background: linear-gradient(90deg, #60a5fa 0%, #a855f7 50%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
        display: inline-block;
    }

    .hero-title .gradient-text::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, #60a5fa 0%, #a855f7 50%, #06b6d4 100%);
        opacity: 0.2;
        filter: blur(20px);
        z-index: -1;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.75);
        margin: 0;
        max-width: 500px;
        animation: slide-in-left 0.8s ease-out 0.2s backwards;
    }

    /* Hero Features */
    .hero-features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        animation: slide-in-left 0.8s ease-out 0.3s backwards;
    }

    .feature-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(59, 130, 246, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #60a5fa;
        flex-shrink: 0;
        font-size: 0.95rem;
    }

    .feature-text {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .feature-text strong {
        color: white;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .feature-text small {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.8rem;
    }

    /* Hero Actions */
    .hero-actions {
        display: flex;
        gap: 16px;
        animation: slide-in-left 0.8s ease-out 0.4s backwards;
        flex-wrap: wrap;
    }

    .btn-hero-primary {
        padding: 16px 36px;
        font-size: 1rem;
        font-weight: 700;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-hero-primary::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        animation: shine 3s infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%); }
        50% { transform: translateX(100%); }
        100% { transform: translateX(100%); }
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(59, 130, 246, 0.4);
    }

    .btn-hero-primary:active {
        transform: translateY(0);
    }

    .btn-hero-secondary {
        padding: 16px 36px;
        font-size: 1rem;
        font-weight: 700;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.05);
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.4);
        transform: translateY(-2px);
    }

    /* Right Content - Visual */
    .hero-right-content {
        position: relative;
        height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: slide-in-right 0.8s ease-out 0.2s backwards;
    }

    .hero-showcase-container {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .hero-showcase-image {
        width: 100%;
        max-width: 400px;
        height: 450px;
        object-fit: cover;
        border-radius: 25px;
        box-shadow: 
            0 40px 80px rgba(0, 0, 0, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        z-index: 5;
        animation: float-image 4s ease-in-out infinite;
    }

    @keyframes float-image {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    /* Floating Tags */
    .showcase-tag {
        position: absolute;
        background: white;
        border-radius: 16px;
        padding: 12px 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 10;
        animation: float-in-left 0.8s ease-out 0.5s backwards;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .showcase-tag i {
        font-size: 1rem;
    }

    .tag-sale {
        top: 20px;
        left: -30px;
        transform: rotate(-8deg);
        color: #ef4444;
        background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
    }

    .tag-sale i {
        color: #fb7185;
    }

    .tag-premium {
        bottom: 60px;
        right: -25px;
        transform: rotate(8deg);
        color: #1e293b;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    }

    .tag-premium i {
        color: #f59e0b;
    }

    .tag-trusted {
        bottom: 20px;
        left: 0;
        color: #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .tag-trusted i {
        color: #34d399;
    }

    /* Animations */
    @keyframes slide-in-left {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slide-in-right {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes float-in-left {
        from {
            opacity: 0;
            transform: translateX(-20px) rotate(0deg);
        }
        to {
            opacity: 1;
            transform: translateX(0) rotate(var(--rotation, 0deg));
        }
    }

    /* Stats Row */
    .hero-stats {
        display: flex;
        gap: 40px;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        animation: slide-in-left 0.8s ease-out 0.5s backwards;
    }

    .stat {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 900;
        background: linear-gradient(90deg, #60a5fa 0%, #a855f7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content-wrapper {
            grid-template-columns: 1fr;
            gap: 40px;
            padding: 50px 30px;
        }

        .hero-right-content {
            height: 350px;
        }

        .hero-showcase-image {
            max-width: 300px;
            height: 350px;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1rem;
        }
    }

    @media (max-width: 768px) {
        .mall-hero-section {
            min-height: 550px;
        }

        .hero-content-wrapper {
            grid-template-columns: 1fr;
            gap: 30px;
            padding: 40px 20px;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 0.95rem;
        }

        .hero-features {
            grid-template-columns: 1fr;
        }

        .hero-actions {
            flex-direction: column;
        }

        .btn-hero-primary,
        .btn-hero-secondary {
            width: 100%;
            justify-content: center;
        }

        .hero-right-content {
            display: none;
        }

        .showcase-tag {
            display: none;
        }

        .hero-stats {
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }
    }

    /* Dark mode and theme integration */
    .mall-hero-section.dark-theme {
        background: linear-gradient(135deg, #0f0f1e 0%, #1a1a2e 50%, #0d0d1f 100%);
    }
</style>

<!-- Mall Hero Section -->
<section class="mall-hero-section">
    <!-- Animated Background -->
    <div class="hero-animated-bg">
        <div class="hero-grid"></div>
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>
    </div>

    <!-- Content -->
    <div class="hero-content-wrapper">
        <!-- Left: Text Content -->
        <div class="hero-left-content">
            <div class="hero-badge">
                <i class="fas fa-check-circle"></i>
                Verified Official Mall
            </div>

            <h1 class="hero-title">
                Elevate Your <span class="gradient-text">Shopping</span> Experience
            </h1>

            <p class="hero-subtitle">
                Discover curated premium brands and exclusive collections. Shop from verified sellers with confidence, quality, and style.
            </p>

            <!-- Features -->
            <div class="hero-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-text">
                        <strong>100% Verified</strong>
                        <small>Trusted sellers only</small>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Fast Shipping</strong>
                        <small>Nationwide delivery</small>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Easy Returns</strong>
                        <small>7-day guarantee</small>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Premium Quality</strong>
                        <small>Best selection</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="hero-actions">
                <a href="?search=" class="btn-hero-primary">
                    <i class="fas fa-store"></i>
                    Explore All Stores
                </a>
                <a href="#premium-collections" class="btn-hero-secondary">
                    <i class="fas fa-play"></i>
                    View Highlights
                </a>
            </div>

            <!-- Stats -->
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Premium Sellers</span>
                </div>
                <div class="stat">
                    <span class="stat-number">1M+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
                <div class="stat">
                    <span class="stat-number">50K+</span>
                    <span class="stat-label">Products</span>
                </div>
            </div>
        </div>

        <!-- Right: Visual Content -->
        <div class="hero-right-content">
            <div class="hero-showcase-container">
                <img src="../image/Dashboard/brand%20new%20bag.jpeg" 
                     alt="Premium Shopping Collection" 
                     class="hero-showcase-image">
                
                <!-- Floating Tags -->
                <div class="showcase-tag tag-sale">
                    <i class="fas fa-tag"></i>
                    <span>SALE -40%</span>
                </div>
                <div class="showcase-tag tag-premium">
                    <i class="fas fa-crown"></i>
                    <span>PREMIUM</span>
                </div>
                <div class="showcase-tag tag-trusted">
                    <i class="fas fa-check-circle"></i>
                    <span>100% Trusted</span>
                </div>
            </div>
        </div>
    </div>
</section>
