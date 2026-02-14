<!-- Product Details System Guide -->

# Product Details Enhancement System

## Overview
This system allows for complete product information display including:
- Material composition
- Manufacturing origin/country
- Size charts
- Complete specifications
- Care and maintenance instructions
- Warranty information
- Physical dimensions and weight

## How to Use

### 1. Database Setup
Run the upgrade script or database migration:
```bash
php /Database/upgrade_product_details.php
```

This adds the following columns to the `products` table:
- `material` - Material composition
- `origin` - Country/place of origin
- `size_chart` - Size chart data (JSON)
- `specifications` - Product specifications (JSON)
- `care_instructions` - Care and maintenance text
- `warranty` - Warranty information
- `dimensions` - Physical dimensions
- `weight` - Product weight with unit

### 2. Adding Product Details via Database

Use the `updateProductDetails()` helper function:

```php
include '../Database/upgrade_product_details.php';

$details = [
    'material' => 'Cotton 65%, Polyester 35%',
    'origin' => 'Bangladesh',
    'warranty' => '1 Year Manufacturer',
    'weight' => '250g',
    'dimensions' => '30cm x 20cm x 10cm',
    'size_chart' => [
        ['Size' => 'XS', 'Chest' => '32"', 'Length' => '24"', 'Sleeve' => '30"'],
        ['Size' => 'S', 'Chest' => '34"', 'Length' => '25"', 'Sleeve' => '31"'],
        ['Size' => 'M', 'Chest' => '36"', 'Length' => '26"', 'Sleeve' => '32"'],
        ['Size' => 'L', 'Chest' => '38"', 'Length' => '27"', 'Sleeve' => '33"'],
        ['Size' => 'XL', 'Chest' => '40"', 'Length' => '28"', 'Sleeve' => '34"'],
    ],
    'specifications' => [
        ['key' => 'Material', 'value' => 'Cotton Blend'],
        ['key' => 'Fit Type', 'value' => 'Regular'],
        ['key' => 'Neckline', 'value' => 'Crew Neck'],
        ['key' => 'Sleeves', 'value' => 'Short'],
    ],
    'care_instructions' => "Machine wash in cold water with similar colors\nUse gentle cycle with mild detergent\nDo not bleach or use fabric softener\nTumble dry on low heat\nIron on low heat if needed"
];

updateProductDetails($conn, 1, $details);
```

### 3. Displaying Product Details in Modals

The system automatically displays all available details in the product modal. Just add the data attributes to product elements:

```html
<div class="product-card" 
    data-name="Premium Cotton T-Shirt"
    data-material="Cotton 65%, Polyester 35%"
    data-origin="Bangladesh"
    data-warranty="1 Year"
    data-weight="250g"
    data-dimensions="30cm x 20cm x 10cm"
    data-size-chart='[{"Size":"S","Chest":"34\"","Length":"25\""},...]'
    data-specifications='[{"key":"Material","value":"Cotton Blend"},...]'
    data-care-instructions="Machine wash cold\nUse mild detergent\nTumble dry low"
    onclick="openProductModal(this)">
    <!-- Product content -->
</div>
```

### 4. Dynamic Population from Database

To load product details from the database, fetch and add them as data attributes:

```php
$productDetails = getProductDetails($conn, $productId);

// Add to product output with data attributes
echo sprintf(
    '<div class="product-card" data-material="%s" data-origin="%s" ... onclick="openProductModal(this)">',
    htmlspecialchars($productDetails['material']),
    htmlspecialchars($productDetails['origin'])
);
```

## Display Sections

### Material Section
Shows material composition (e.g., "Cotton 65%, Polyester 35%")

### Origin Section  
Shows manufacturing country or place of origin

### Warranty Section
Displays warranty period and terms

### Size Chart
Interactive table showing:
- Size names (XS, S, M, L, XL)
- Measurements for different dimensions
- Easy-to-read format with header and row data

### Specifications
List of product specifications like:
- Fit type (Regular, Slim, Oversized)
- Neckline type
- Sleeve length
- Features and highlights

### Dimensions & Weight
Cards displaying:
- Overall dimensions (height × width × depth)
- Product weight with unit

### Care Instructions
Formatted list of care and maintenance steps:
- Washing instructions
- Drying guidelines
- Special handling notes

### Trust Badges
Automatic badges showing:
- Warranty information
- Made in [Country]
- Material composition

## Example Product Data

For a T-Shirt:
```json
{
  "material": "100% Cotton",
  "origin": "India",
  "warranty": "1 Year Manufacturer",
  "weight": "180g",
  "dimensions": "28cm × 20cm × 8cm",
  "size_chart": [
    { "Size": "XS", "Chest": "32\"", "Length": "24\"", "Sleeve": "30\"" },
    { "Size": "S", "Chest": "34\"", "Length": "25\"", "Sleeve": "31\"" },
    { "Size": "M", "Chest": "36\"", "Length": "26\"", "Sleeve": "32\"" },
    { "Size": "L", "Chest": "38\"", "Length": "27\"", "Sleeve": "33\"" },
    { "Size": "XL", "Chest": "40\"", "Length": "28\"", "Sleeve": "34\"" }
  ],
  "specifications": [
    { "key": "Material", "value": "100% Cotton" },
    { "key": "Fit Type", "value": "Regular" },
    { "key": "Neckline", "value": "Crew Neck" },
    { "key": "Sleeves", "value": "Short" },
    { "key": "Care", "value": "Machine Washable" }
  ],
  "care_instructions": "Machine wash in cold water\nUse gentle cycle\nDo not bleach\nTumble dry on low heat\nIron on medium heat if needed"
}
```

For Electronics (Headphones):
```json
{
  "material": "ABS Plastic, Silicone",
  "origin": "China",
  "warranty": "2 Years Global",
  "weight": "185g",
  "dimensions": "19cm × 15cm × 8cm",
  "specifications": [
    { "key": "Driver Size", "value": "40mm" },
    { "key": "Frequency Response", "value": "20Hz - 20kHz" },
    { "key": "Impedance", "value": "32Ω" },
    { "key": "Connectivity", "value": "Bluetooth 5.0 + 3.5mm Jack" },
    { "key": "Battery Life", "value": "40 hours" },
    { "key": "Noise Cancellation", "value": "Active Noise Cancellation (ANC)" }
  ],
  "care_instructions": "Clean with soft, dry microfiber cloth\nAvoid water exposure\nStore in cool, dry place\nDo not expose to extreme heat\nKeep away from magnetic fields"
}
```

## Integration Examples

### In Shop Landing Page
```php
// Get best-selling products with details
$bestSellers = getBestSellingProducts($conn, 4);
foreach ($bestSellers as $bs) {
    $details = getProductDetails($conn, $bs['id']);
    echo sprintf(
        '<div class="product-card" data-material="%s" data-origin="%s" ... >',
        htmlspecialchars($details['material']),
        htmlspecialchars($details['origin'])
    );
}
```

### In Best-Selling Section
Update [Categories/best_selling/card.php](Categories/best_selling/card.php) to include:
```php
$details = getProductDetails($conn, $product['id']);
// Add to data attributes
```

### In Search Results
Add product details when displaying search results

## Styling

All detail sections use the CSS file:
`/css/components/product-details.css`

Sections include:
- Material Quality (Purple accent)
- Origin (Pink accent)
- Warranty (Green accent)
- Weight (Orange accent)
- Size Chart (Indigo background)
- Specifications (Green background)
- Care Instructions (Amber background)
- Trust Badges

## Browser Compatibility
Works on all modern browsers with CSS Grid and Flexbox support.

## Future Enhancements
- Video tutorials for product usage
- Interactive size finder
- Eco-friendly/sustainable certifications
- User reviews by material/size
- PDF spec sheet download
