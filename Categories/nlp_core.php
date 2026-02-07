<?php
/**
 * Core AI NLP Engine for Sentiment Analysis
 * Used for both API and server-side rendering
 */

function analyzeSentimentAI($text)
{
    // 1. Preprocessing (Simulating Tokenization)
    $text = strtolower($text);
    // Remove punctuation
    $clean_text = preg_replace('/[^\w\s]/', '', $text);
    $words = explode(' ', $clean_text);

    // 2. Knowledge Base (Micro-Model of Keywords)
    $positive_lexicon = [
        'good', 'great', 'excellent', 'amazing', 'love', 'best', 'fantastic', 
        'satisfied', 'happy', 'fast', 'secure', 'perfect', 'recommend', 'nice', 
        'solid', 'legit', 'quality', 'super', 'ganda', 'ayos', 'mabilis', 
        'sarap', 'effective', 'worth', 'sulit', 'bait', 'bilis', 'okay', 'ok', 'wow'
    ];

    $negative_lexicon = [
        'bad', 'worst', 'terrible', 'slow', 'hate', 'disappointed', 'poor', 
        'broke', 'broken', 'damage', 'waste', 'refund', 'scam', 'fake', 
        'pangit', 'sira', 'bagal', 'sayang', 'wag', 'tagal', 'yupi', 'basag'
    ];

    $neutral_lexicon = [
        'average', 'fine', 'normal', 'okay naman', 'sakto', 'pwede na', 'standard'
    ];

    // 3. Analysis Logic (Scoring)
    $score = 0;
    $detected_keywords = [];

    foreach ($words as $word) {
        if (in_array($word, $positive_lexicon)) {
            $score += 1;
            $detected_keywords[] = $word;
        } elseif (in_array($word, $negative_lexicon)) {
            $score -= 1;
            $detected_keywords[] = $word;
        }
    }

    // 4. Classification
    $sentiment = 'Neutral';
    $confidence = 50; // Base confidence

    if ($score > 0) {
        $sentiment = 'Positive';
        $confidence = min(99, 60 + ($score * 10)); // Higher score = higher confidence
    } elseif ($score < 0) {
        $sentiment = 'Negative';
        $confidence = min(99, 60 + (abs($score) * 10));
    } else {
        // Check strict neutral phrases
        foreach ($neutral_lexicon as $phrase) {
            if (strpos($clean_text, $phrase) !== false) {
                $confidence = 85;
                break;
            }
        }
    }

    // 5. Construct AI Response Structure
    return [
        'status' => 'success',
        'result' => [
            'sentiment' => $sentiment,
            'confidence_score' => $confidence . '%',
            'analysis_method' => 'NLP_Keyword_Matching_v1.1',
            'keywords_detected' => array_unique($detected_keywords),
            'processed_at' => date('Y-m-d H:i:s')
        ]
    ];
}
?>
