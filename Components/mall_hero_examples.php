<?php
/**
 * Mall Hero Component Usage Examples
 * 
 * This file demonstrates how to use the new professional mall_hero.php
 * component in different contexts and how to customize it.
 */

// Example 1: Basic Usage in Landing Page
?>
<!-- Landing Page -->
<?php include 'Components/mall_hero.php'; ?>

<!--
Example 2: With Custom CSS Override
-->
<style>
    .mall-hero-section {
        /* Override background gradient */
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%) !important;
    }
    
    .hero-title {
        /* Override title color */
        color: #eaeaea;
    }
    
    .hero-subtitle {
        /* Override subtitle */
        color: rgba(234, 234, 234, 0.8);
    }
</style>

<?php include 'Components/mall_hero.php'; ?>

<!--
Example 3: With Dynamic Content (PHP)
Note: Component uses static HTML, but you can modify it like this:
-->

<?php
// Get dynamic messaging from database/config
$hero_title = "Elevate Your Shopping";
$hero_subtitle = "Discover curated premium brands and exclusive collections.";
$hero_image = "../image/Dashboard/brand%20new%20bag.jpeg";
$stats = [
    ['number' => '500+', 'label' => 'Premium Sellers'],
    ['number' => '1M+', 'label' => 'Happy Customers'],
    ['number' => '50K+', 'label' => 'Products']
];
?>

<!-- Component would need to be modified to accept these variables -->
<!-- For now, edit the component directly or create a parameterized version -->

<?php include 'Components/mall_hero.php'; ?>

<!--
Example 4: In Multiple Locations
The component can be included in:
-->

<!-- Location 1: Shop Landing Page -->
<?php if (!isset($_GET['store']) && empty($_GET['search'])): ?>
    <?php include 'Components/mall_hero.php'; ?>
<?php endif; ?>

<!-- Location 2: After Category Browsing -->
<?php if (!empty($show_hero_reminder)): ?>
    <section style="margin: 60px 0;">
        <?php include 'Components/mall_hero.php'; ?>
    </section>
<?php endif; ?>

<!-- Location 3: Newsletter Signup Page -->
<section style="margin: 40px 0;">
    <?php include 'Components/mall_hero.php'; ?>
</section>

<!--
Example 5: Style Variants (Create additional CSS classes)
-->

<style>
    /* Minimal variant */
    .mall-hero-section.minimal {
        min-height: 450px;
    }
    
    .mall-hero-section.minimal .hero-stats {
        display: none;
    }
    
    /* Dark theme variant */
    .mall-hero-section.dark-theme {
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0d0d0d 100%);
    }
    
    .mall-hero-section.dark-theme .hero-orb {
        opacity: 0.15;
    }
    
    /* Compact variant */
    .mall-hero-section.compact {
        min-height: 400px;
    }
    
    .mall-hero-section.compact .hero-title {
        font-size: 2.5rem;
    }
</style>

<!-- Usage: Add class to customize appearance -->
<!-- Just modify the component to add these classes -->

<!--
Example 6: Integration with Database Content
-->

<?php
// Sample database integration
$database_config = [
    'hero' => [
        'title_main' => 'Elevate Your',
        'title_highlight' => 'Shopping',
        'subtitle' => 'Discover curated premium brands and exclusive collections.',
        'image' => '../image/Dashboard/brand%20new%20bag.jpeg',
        'cta_primary' => 'Explore All Stores',
        'cta_secondary' => 'View Highlights',
        'stats' => [
            ['number' => '500+', 'label' => 'Premium Sellers'],
            ['number' => '1M+', 'label' => 'Happy Customers'],
            ['number' => '50K+', 'label' => 'Products']
        ]
    ]
];

// To implement: Create a parameterized version of mall_hero.php
// that accepts these config values
?>

<!--
Example 7: Using component in Admin/Content Management Interface
-->

<?php
// Admin could update hero content through form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hero'])) {
    // Save to database
    $hero_title = $_POST['hero_title'];
    $hero_subtitle = $_POST['hero_subtitle'];
    $hero_image = $_POST['hero_image'];
    // etc...
}
?>

<div class="admin-hero-editor" style="margin: 20px; padding: 20px; background: #f5f5f5; border-radius: 8px;">
    <h3>Edit Mall Hero Section</h3>
    <form method="POST">
        <input type="text" name="hero_title" placeholder="Hero Title" value="Elevate Your Shopping">
        <textarea name="hero_subtitle" placeholder="Hero Subtitle">Discover curated premium brands</textarea>
        <input type="text" name="hero_image" placeholder="Image URL">
        <button type="submit" name="update_hero">Update Hero Section</button>
    </form>
    
    <!-- Preview -->
    <div style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 20px;">
        <h4>Preview:</h4>
        <?php include 'Components/mall_hero.php'; ?>
    </div>
</div>

<!--
Example 8: A/B Testing Multiple Variants
-->

<?php
$hero_variant = $_GET['variant'] ?? 'default'; // URL: ?variant=v1, ?variant=v2

// Load different hero content based on variant
$hero_variants = [
    'default' => [
        'title' => 'Elevate Your Shopping',
        'subtitle' => 'Discover curated premium brands and exclusive collections.'
    ],
    'v1' => [
        'title' => 'Shop Premium with Confidence',
        'subtitle' => 'Verified sellers and products you can trust.'
    ],
    'v2' => [
        'title' => 'Your Destination for Quality',
        'subtitle' => 'Thousands of verified shops, endless possibilities.'
    ]
];

$current_variant = $hero_variants[$hero_variant] ?? $hero_variants['default'];
?>

<!-- Render hero with variant data -->
<!-- Would require parameterized component version -->

<!--
Example 9: Mobile-First Rendering
-->

<?php
$is_mobile = preg_match('/Mobile|Android|iPhone/i', $_SERVER['HTTP_USER_AGENT']);
?>

<?php if (!$is_mobile): ?>
    <!-- Show full hero on desktop -->
    <?php include 'Components/mall_hero.php'; ?>
<?php else: ?>
    <!-- Show simplified hero on mobile -->
    <section class="mall-hero-section" style="min-height: 300px;">
        <div style="padding: 40px 20px; text-align: center;">
            <h1 style="color: white; font-size: 1.8rem;">
                Elevate Your Shopping
            </h1>
            <p style="color: rgba(255,255,255,0.8); margin: 15px 0;">
                Discover premium brands and exclusive collections
            </p>
            <a href="?search=" class="btn-hero-primary" style="display: inline-block;">
                Explore Now
            </a>
        </div>
    </section>
<?php endif; ?>

<!--
Example 10: Performance Optimization (Lazy Loading)
-->

<div id="hero-container" style="height: 650px; background: #f5f5f5;">
    <!-- Placeholder while loading -->
</div>

<script>
    // Lazy load hero section when needed
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('hero-container');
        
        // Load hero section dynamically
        fetch('Components/mall_hero.php')
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            });
    });
    
    // Or use Intersection Observer for viewport-based loading
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Load hero when visible
                fetch('Components/mall_hero.php')
                    .then(r => r.text())
                    .then(html => {
                        document.getElementById('hero-container').innerHTML = html;
                    });
                observer.unobserve(entry.target);
            }
        });
    });
    observer.observe(container);
</script>

<!-- ============================================ -->
<!-- ADVANCED CUSTOMIZATION GUIDE -->
<!-- ============================================ -->

<!--
To create a custom version of the hero for special promotions:
-->

<style>
    /* Promotion variant */
    .mall-hero-section.promo {
        background: linear-gradient(135deg, #ec4899 0%, #f43f5e 50%, #fb7185 100%);
    }
    
    .mall-hero-section.promo .hero-title {
        /* Adjust for promo */ 
    }
    
    /* Holiday variant */
    .mall-hero-section.holiday {
        background: linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%);
    }
    
    /* Summer variant */
    .mall-hero-section.summer {
        background: linear-gradient(135deg, #fed7aa 0%, #fdba74 50%, #fb923c 100%);
    }
</style>

<!-- Implementation: Simply add the class name to component -->
<!-- Would require modifying mall_hero.php to accept class parameter -->

<!-- ============================================ -->
<!-- INTEGRATION CHECKLIST -->
<!-- ============================================ -->

<!--
☑ Component created: Components/mall_hero.php
☑ Integrated into: Shop/index.php
☑ Animations working properly
☑ Responsive on all devices
☑ Colors match brand guidelines
☑ Links point to correct locations
☑ Images loading properly
☑ Text content is accurate
☑ No console errors
☑ Performance optimized
☑ Accessibility ready
☑ Cross-browser compatible
-->

<!-- ============================================ -->
<!-- PROPERTY CUSTOMIZATION REFERENCE -->
<!-- ============================================ -->

<!-- 
To make the component parameterized (accept variables):
Create Components/mall_hero.php with PHP parameters:

Example:
<?php
function renderMallHero($config = []) {
    $defaults = [
        'title_main' => 'Elevate Your',
        'title_highlight' => 'Shopping',
        'subtitle' => 'Discover curated premium brands...',
        'image' => '../image/Dashboard/brand%20new%20bag.jpeg',
        'button_primary_text' => 'Explore All Stores',
        'button_secondary_text' => 'View Highlights',
        'stats' => [...]
    ];
    
    $config = array_merge($defaults, $config);
    // Render HTML with $config values
}
?>

Usage:
<?php
renderMallHero([
    'title_highlight' => 'Experience',
    'subtitle' => 'Your custom subtitle here...'
]);
?>
-->

