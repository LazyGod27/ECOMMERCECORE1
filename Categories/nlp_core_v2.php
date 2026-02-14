<?php
/**
 * Enhanced Core AI NLP Engine for Sentiment Analysis
 * Version 2.0 - Improved Accuracy with Advanced NLP Features
 */

function analyzeSentimentAI($text)
{
    // 1. Preprocessing
    $text = trim($text);
    if (empty($text)) {
        return array(
            'status' => 'error',
            'message' => 'Empty text provided'
        );
    }

    $original_text = $text;
    $text = strtolower($text);
    
    // Remove extra whitespace
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Remove punctuation but keep for analysis
    $clean_text = preg_replace('/[^\w\s]/', '', $text);
    $words = array_filter(explode(' ', $clean_text));

    // 2. Enhanced Knowledge Base with Sentiment Strength
    $strong_positive = [
        'excellent', 'fantastic', 'amazing', 'outstanding', 'perfect', 'love', 'best',
        'wonderful', 'exceptional', 'brilliant', 'phenomenal', 'superb', 'terrific',
        'ganda', 'sulit na sulit', 'napakaganda', 'napakayaman', 'best ever'
    ];

    $positive_lexicon = [
        'good', 'great', 'nice', 'decent', 'satisfied', 'happy', 'fast', 'secure',
        'recommend', 'solid', 'legit', 'quality', 'worth', 'okay', 'ok', 'fine',
        'ayos', 'mabilis', 'sarap', 'effective', 'sulit', 'bait', 'bilis', 'okay naman',
        'worth it', 'satisfied', 'pleased', 'comfortable', 'reliable', 'impressive'
    ];

    $strong_negative = [
        'terrible', 'horrible', 'awful', 'disgusting', 'hate', 'worst', 'useless',
        'broke', 'broken', 'scam', 'fake', 'fraud', 'dangerous', 'nightmare',
        'pangit na pangit', 'nagsama', 'napakagastos', 'walang kwenta'
    ];

    $negative_lexicon = [
        'bad', 'poor', 'slow', 'disappointed', 'damage', 'waste', 'refund',
        'pangit', 'sira', 'bagal', 'sayang', 'wag', 'tagal', 'yupi', 'basag',
        'cheaply made', 'not good', 'doesnt work', 'false advertisement', 'misleading',
        'defective', 'disappointing', 'uncomfortable', 'unreliable'
    ];

    $neutral_lexicon = [
        'average', 'normal', 'standard', 'mediocre', 'average', 'ok lang',
        'sakto', 'pwede na', 'common', 'ordinary'
    ];

    // 3. Advanced Analysis with Negation Detection
    $score = 0;
    $detected_keywords = [];
    $negation_words = ['not', 'no', 'never', 'neither', 'cannot', "can't", "don't", 'hindi', 'walang', 'wala'];
    
    // Check for negations (e.g., "not good" should be negative)
    for ($i = 0; $i < count($words); $i++) {
        $word = $words[$i];
        $is_negated = false;
        
        // Check if previous word is a negation (look back 2 words max)
        if ($i > 0 && in_array($words[$i - 1], $negation_words)) {
            $is_negated = true;
        }
        if ($i > 1 && in_array($words[$i - 2], $negation_words)) {
            $is_negated = true;
        }

        // Score calculation
        if (in_array($word, $strong_positive)) {
            $score += $is_negated ? -1.5 : 2;
            $detected_keywords[] = ($is_negated ? 'not_' : '') . $word;
        } elseif (in_array($word, $positive_lexicon)) {
            $score += $is_negated ? -0.5 : 1;
            $detected_keywords[] = ($is_negated ? 'not_' : '') . $word;
        } elseif (in_array($word, $strong_negative)) {
            $score += $is_negated ? 1.5 : -2;
            $detected_keywords[] = ($is_negated ? 'not_' : '') . $word;
        } elseif (in_array($word, $negative_lexicon)) {
            $score += $is_negated ? 0.5 : -1;
            $detected_keywords[] = ($is_negated ? 'not_' : '') . $word;
        }
    }

    // 4. Strength Detection (exclamation marks, capital letters, repeated chars)
    $exclamation_count = substr_count($original_text, '!');
    $caps_ratio = strlen(preg_replace('/[^A-Z]/', '', $original_text)) / max(strlen($original_text), 1);
    $repeated_chars = preg_match_all('/(.)\1{2,}/', $original_text); // e.g., "sooooo"
    
    // Boost score for emphasis
    if ($exclamation_count > 2) {
        $score += ($score > 0) ? 0.5 : -0.5;
    }
    if ($caps_ratio > 0.3) {
        $score += ($score > 0) ? 0.3 : -0.3;
    }
    if ($repeated_chars > 0) {
        $score += ($score > 0) ? 0.2 : -0.2;
    }

    // 5. Length Analysis (Very short reviews might be less reliable)
    $word_count = count($words);
    $length_factor = 1.0;
    if ($word_count < 3) {
        $length_factor = 0.7; // Less confident for very short reviews
    }

    // 6. Classification with improved confidence
    $sentiment = 'Neutral';
    $confidence = 50;

    if ($score > 1) {
        $sentiment = 'Positive';
        // More accurate confidence calculation
        $confidence = min(98, 60 + (abs($score) * 12));
        $confidence = max(55, $confidence * $length_factor);
    } elseif ($score < -1) {
        $sentiment = 'Negative';
        $confidence = min(98, 60 + (abs($score) * 12));
        $confidence = max(55, $confidence * $length_factor);
    } elseif ($score > 0) {
        $sentiment = 'Positive';
        $confidence = min(98, 50 + (abs($score) * 15));
        $confidence = max(45, $confidence * $length_factor);
    } elseif ($score < 0) {
        $sentiment = 'Negative';
        $confidence = min(98, 50 + (abs($score) * 15));
        $confidence = max(45, $confidence * $length_factor);
    } else {
        // Very neutral if score == 0
        $confidence = ($word_count < 5) ? 45 : 70;
    }

    // Round confidence
    $confidence = round(max(20, min(99, $confidence)));

    // 7. Return enhanced structure
    return [
        'status' => 'success',
        'result' => [
            'sentiment' => $sentiment,
            'confidence_score' => $confidence . '%',
            'confidence_numeric' => $confidence,
            'score_value' => round($score, 2),
            'word_count' => $word_count,
            'analysis_method' => 'NLP_Enhanced_v2.0_with_Negation',
            'keywords_detected' => array_unique(array_slice($detected_keywords, 0, 10)),
            'has_strong_sentiment' => abs($score) > 1.5,
            'processed_at' => date('Y-m-d H:i:s')
        ]
    ];
}

/**
 * Detect Product Category from Image Recognition Results
 * Maps MobileNet predictions to product categories
 */
function mapDetectionToProduct($className, $confidence = 0.5)
{
    // Enhanced product mapping with broader category coverage
    $detection_map = [
        // Electronics
        'telephone' => ['category' => 'Electronics', 'type' => 'Phone', 'keywords' => ['phone', 'mobile', 'smartphone']],
        'phone' => ['category' => 'Electronics', 'type' => 'Phone', 'keywords' => ['phone', 'mobile']],
        'cellphone' => ['category' => 'Electronics', 'type' => 'Phone', 'keywords' => ['phone', 'mobile']],
        'laptop' => ['category' => 'Electronics', 'type' => 'Computer', 'keywords' => ['laptop', 'computer', 'pc']],
        'computer' => ['category' => 'Electronics', 'type' => 'Computer', 'keywords' => ['computer', 'desktop', 'pc']],
        'monitor' => ['category' => 'Electronics', 'type' => 'Display', 'keywords' => ['monitor', 'screen', 'display']],
        'camera' => ['category' => 'Electronics', 'type' => 'Camera', 'keywords' => ['camera', 'photo', 'photography']],
        'headphones' => ['category' => 'Electronics', 'type' => 'Audio', 'keywords' => ['headphones', 'earbuds', 'audio']],
        'speaker' => ['category' => 'Electronics', 'type' => 'Audio', 'keywords' => ['speaker', 'sound', 'audio']],
        'watch' => ['category' => 'Electronics', 'type' => 'Wearable', 'keywords' => ['watch', 'smartwatch', 'wearable']],
        'tablet' => ['category' => 'Electronics', 'type' => 'Tablet', 'keywords' => ['tablet', 'ipad', 'device']],
        'keyboard' => ['category' => 'Electronics', 'type' => 'Peripheral', 'keywords' => ['keyboard', 'input', 'peripheral']],
        'mouse' => ['category' => 'Electronics', 'type' => 'Peripheral', 'keywords' => ['mouse', 'input', 'pointer']],
        
        // Fashion & Apparel
        'shoe' => ['category' => 'Fashion', 'type' => 'Footwear', 'keywords' => ['shoe', 'footwear', 'sneaker']],
        'sneaker' => ['category' => 'Fashion', 'type' => 'Footwear', 'keywords' => ['sneaker', 'athletic', 'shoe']],
        'sandal' => ['category' => 'Fashion', 'type' => 'Footwear', 'keywords' => ['sandal', 'slipper', 'footwear']],
        'boot' => ['category' => 'Fashion', 'type' => 'Footwear', 'keywords' => ['boot', 'footwear']],
        'shirt' => ['category' => 'Fashion', 'type' => 'Clothing', 'keywords' => ['shirt', 'top', 'apparel']],
        'jersey' => ['category' => 'Fashion', 'type' => 'Clothing', 'keywords' => ['jersey', 'sports', 'shirt']],
        'hoodie' => ['category' => 'Fashion', 'type' => 'Clothing', 'keywords' => ['hoodie', 'sweatshirt', 'jacket']],
        'jacket' => ['category' => 'Fashion', 'type' => 'Clothing', 'keywords' => ['jacket', 'coat', 'outerwear']],
        'pants' => ['category' => 'Fashion', 'type' => 'Clothing', 'keywords' => ['pants', 'trousers', 'jeans']],
        'jeans' => ['category' => 'Fashion', 'type' => 'Clothing', 'keywords' => ['jeans', 'pants', 'denim']],
        'dress' => ['category' => 'Fashion', 'type' => 'Clothing', 'keywords' => ['dress', 'gown', 'apparel']],
        'skirt' => ['category' => 'Fashion', 'type' => 'Clothing', 'keywords' => ['skirt', 'apparel']],
        'hat' => ['category' => 'Fashion', 'type' => 'Accessories', 'keywords' => ['hat', 'cap', 'headwear']],
        'cap' => ['category' => 'Fashion', 'type' => 'Accessories', 'keywords' => ['cap', 'hat', 'headwear']],
        'bag' => ['category' => 'Fashion', 'type' => 'Accessories', 'keywords' => ['bag', 'backpack', 'purse']],
        'backpack' => ['category' => 'Fashion', 'type' => 'Accessories', 'keywords' => ['backpack', 'bag']],
        'sock' => ['category' => 'Fashion', 'type' => 'Accessories', 'keywords' => ['sock', 'hosiery']],
        'glove' => ['category' => 'Fashion', 'type' => 'Accessories', 'keywords' => ['glove', 'hand', 'accessory']],
        'necklace' => ['category' => 'Fashion', 'type' => 'Jewelry', 'keywords' => ['necklace', 'jewelry']],
        'bracelet' => ['category' => 'Fashion', 'type' => 'Jewelry', 'keywords' => ['bracelet', 'jewelry', 'wearable']],
        'ring' => ['category' => 'Fashion', 'type' => 'Jewelry', 'keywords' => ['ring', 'jewelry']],
        'earring' => ['category' => 'Fashion', 'type' => 'Jewelry', 'keywords' => ['earring', 'jewelry']],
        
        // Beauty & Health
        'lipstick' => ['category' => 'Beauty', 'type' => 'Cosmetics', 'keywords' => ['lipstick', 'makeup', 'cosmetics']],
        'cosmetics' => ['category' => 'Beauty', 'type' => 'Cosmetics', 'keywords' => ['cosmetics', 'makeup', 'beauty']],
        'perfume' => ['category' => 'Beauty', 'type' => 'Fragrance', 'keywords' => ['perfume', 'cologne', 'fragrance']],
        'bottle' => ['category' => 'Home', 'type' => 'Container', 'keywords' => ['bottle', 'container']],
        
        // Home & Living
        'lamp' => ['category' => 'Home', 'type' => 'Lighting', 'keywords' => ['lamp', 'light', 'lighting']],
        'light' => ['category' => 'Home', 'type' => 'Lighting', 'keywords' => ['light', 'lamp', 'lighting']],
        'furniture' => ['category' => 'Home', 'type' => 'Furniture', 'keywords' => ['furniture', 'chair', 'table']],
        'chair' => ['category' => 'Home', 'type' => 'Furniture', 'keywords' => ['chair', 'seat', 'furniture']],
        'table' => ['category' => 'Home', 'type' => 'Furniture', 'keywords' => ['table', 'furniture', 'desk']],
        'bed' => ['category' => 'Home', 'type' => 'Furniture', 'keywords' => ['bed', 'bedroom', 'furniture']],
        'coffee maker' => ['category' => 'Home', 'type' => 'Kitchen', 'keywords' => ['coffee', 'maker', 'kitchen']],
        'pot' => ['category' => 'Home', 'type' => 'Kitchen', 'keywords' => ['pot', 'cookware', 'kitchen']],
        'pan' => ['category' => 'Home', 'type' => 'Kitchen', 'keywords' => ['pan', 'cookware', 'kitchen']],
        'cup' => ['category' => 'Home', 'type' => 'Kitchen', 'keywords' => ['cup', 'drinkware', 'kitchen']],
        'plate' => ['category' => 'Home', 'type' => 'Kitchen', 'keywords' => ['plate', 'dinnerware', 'kitchen']],
        
        // Sports & Outdoors
        'ball' => ['category' => 'Sports', 'type' => 'Equipment', 'keywords' => ['ball', 'sports', 'equipment']],
        'bicycle' => ['category' => 'Sports', 'type' => 'Outdoor', 'keywords' => ['bicycle', 'bike', 'sports']],
        'skateboard' => ['category' => 'Sports', 'type' => 'Outdoor', 'keywords' => ['skateboard', 'sports']],
        'tennisracket' => ['category' => 'Sports', 'type' => 'Equipment', 'keywords' => ['racket', 'tennis', 'equipment']],
        'dog' => ['category' => 'Pets', 'type' => 'Pet', 'keywords' => ['pet', 'dog', 'animal']],
        'cat' => ['category' => 'Pets', 'type' => 'Pet', 'keywords' => ['pet', 'cat', 'animal']],
    ];

    // Normalize and check classification
    $normalized_class = strtolower(str_replace(' ', '', $className));
    
    // First try exact match
    if (isset($detection_map[$normalized_class])) {
        return [
            'found' => true,
            'category' => $detection_map[$normalized_class]['category'],
            'type' => $detection_map[$normalized_class]['type'],
            'keywords' => $detection_map[$normalized_class]['keywords'],
            'confidence' => $confidence,
            'original_detection' => $className
        ];
    }

    // Try partial match
    foreach ($detection_map as $key => $value) {
        if (stripos($normalized_class, $key) !== false || stripos($key, $normalized_class) !== false) {
            return [
                'found' => true,
                'category' => $value['category'],
                'type' => $value['type'],
                'keywords' => $value['keywords'],
                'confidence' => $confidence * 0.8, // Lower confidence for partial match
                'original_detection' => $className,
                'match_type' => 'partial'
            ];
        }
    }

    // No match found
    return [
        'found' => false,
        'category' => 'Unknown',
        'original_detection' => $className,
        'confidence' => 0
    ];
}

?>
