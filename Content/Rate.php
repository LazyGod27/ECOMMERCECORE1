<?php
session_start();
include("../Database/config.php");

// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch User Name/Initial for UI
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "User";
$user_initial = strtoupper(substr($user_name, 0, 1));

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
// We might need product_id. If not in GET, try to find from order? 
// For now assume passed or we can be lenient.
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

$msg = "";

// 2. Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id_post = intval($_POST['order_id']); // Use hidden field
    $product_id_post = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review_title = mysqli_real_escape_string($conn, trim($_POST['review_title'] ?? ''));
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));
    $recommend = isset($_POST['recommend']) ? intval($_POST['recommend']) : null;

    // Handle Multiple File Uploads
    $media_paths = [];
    if (isset($_FILES['media']) && is_array($_FILES['media']['error'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
        $upload_dir = "../uploads/reviews/";
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        foreach ($_FILES['media']['error'] as $key => $error) {
            if ($error == 0 && $_FILES['media']['size'][$key] > 0) {
                $filename = $_FILES['media']['name'][$key];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_name = uniqid() . "." . $ext;
                    if (move_uploaded_file($_FILES['media']['tmp_name'][$key], $upload_dir . $new_name)) {
                        $media_paths[] = "uploads/reviews/" . $new_name;
                    }
                }
            }
        }
    }

    // Convert media paths to JSON
    $media_json = !empty($media_paths) ? json_encode($media_paths) : null;

    // AI Sentiment Analysis
    include("../Categories/nlp_core.php");
    $ai_res = analyzeSentimentAI($comment);
    $sentiment = $ai_res['result']['sentiment'];
    $confidence = $ai_res['result']['confidence_score'];

    // Insert with new fields
    $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, review_title, comment, media_url, sentiment, confidence, recommended, created_at) 
            VALUES ('$user_id', '$product_id_post', '$order_id_post', '$rating', '$review_title', '$comment', '$media_json', '$sentiment', '$confidence', " . ($recommend !== null ? "'$recommend'" : "NULL") . ", NOW())";

    if (mysqli_query($conn, $sql)) {
        $success = true;
    } else {
        // If columns don't exist, use fallback INSERT
        $sql_fallback = "INSERT INTO reviews (user_id, product_id, order_id, rating, comment, media_url, sentiment, confidence, created_at) 
                        VALUES ('$user_id', '$product_id_post', '$order_id_post', '$rating', '$comment', '$media_json', '$sentiment', '$confidence', NOW())";
        if (mysqli_query($conn, $sql_fallback)) {
            $success = true;
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Product</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            color: #333;
        }

        nav {
            background-color: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .rate-container {
            max-width: 700px;
            margin: 0 auto 50px auto;
            background: white;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h2.page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1a1a1a;
        }

        .page-subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
            font-weight: 400;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 35px;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
            border-radius: 12px;
            border-left: 4px solid #2A3B7E;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #2A3B7E 0%, #1e2b5e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            margin-right: 15px;
            font-size: 18px;
        }

        .user-details h3 {
            margin: 0;
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 600;
        }

        .user-details p {
            margin: 5px 0 0 0;
            font-size: 13px;
            color: #888;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .label {
            display: block;
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 14px;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .label-hint {
            font-size: 12px;
            color: #999;
            font-weight: 400;
            text-transform: none;
            display: block;
            margin-top: 4px;
        }

        /* Enhanced Star Rating System */
        .star-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .star-section-title {
            font-weight: 600;
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .star-rating {
            display: flex;
            gap: 8px;
            font-size: 28px;
            color: #ddd;
            cursor: pointer;
            margin: 10px 0;
        }

        .star-rating i {
            transition: all 0.2s ease;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .star-rating i:hover {
            color: #ffca2c;
            transform: scale(1.15);
        }

        .star-rating i.active {
            color: #ffc107;
            filter: drop-shadow(0 2px 4px rgba(255, 193, 7, 0.3));
        }

        .rating-label {
            font-size: 12px;
            color: #999;
            margin-top: 8px;
            font-style: italic;
        }

        /* Multiple Image Upload */
        .upload-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .upload-box {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: #fafafa;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .upload-box:hover {
            border-color: #2A3B7E;
            background-color: #f0f3ff;
        }

        .upload-box.uploaded {
            border-color: #10b981;
            background-color: #f0fdf4;
            padding: 0;
            overflow: hidden;
        }

        .upload-box i {
            font-size: 28px;
            color: #2A3B7E;
            margin-bottom: 8px;
            display: block;
        }

        .upload-box span {
            color: #666;
            font-size: 13px;
            font-weight: 500;
        }

        .media-preview {
            max-width: 100%;
            max-height: 120px;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            display: block;
        }

        .media-remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .upload-box.uploaded:hover .media-remove-btn {
            opacity: 1;
        }

        /* Textarea */
        textarea {
            width: 100%;
            min-height: 140px;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            resize: vertical;
            background-color: #fafafa;
            color: #333;
            transition: all 0.2s;
        }

        textarea:focus {
            outline: none;
            border-color: #2A3B7E;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(42, 59, 126, 0.1);
        }

        textarea::placeholder {
            color: #999;
        }

        /* Character Counter */
        .char-counter {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }

        /* Rating Criteria Grid */
        .rating-criteria {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .criteria-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .criteria-label {
            font-weight: 600;
            font-size: 13px;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        /* Buttons */
        .button-group {
            display: grid;
            grid-template-columns: 1fr 0.3fr;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-submit {
            padding: 14px 30px;
            background: linear-gradient(135deg, #2A3B7E 0%, #1e2b5e 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-cancel {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #777;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 20px;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            color: #333;
            border-color: #999;
            background: #f5f5f5;
        }

        /* Hidden Input for Stars */
        input[type="number"]#ratingInput {
            display: none;
        }

        input[type="file"]#mediaInput {
            display: none;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c8e6c9 100%);
            color: #155724;
            border: 1px solid #b8e6b8;
            padding: 18px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 2px 8px rgba(21, 87, 36, 0.1);
        }

        /* Verified Purchase Badge */
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #dbeafe;
            color: #0369a1;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .verified-badge i {
            font-size: 13px;
        }

        /* Input Fields */
        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            background-color: #fafafa;
            color: #333;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #2A3B7E;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(42, 59, 126, 0.1);
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder {
            color: #999;
        }

        /* Radio Buttons */
        input[type="radio"] {
            cursor: pointer;
            accent-color: #2A3B7E;
        }

        input[type="radio"]:hover {
            transform: scale(1.1);
        }

        label[style*="display: flex"] {
            padding: 10px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        label[style*="display: flex"]:hover {
            background-color: #f5f7fa;
        }

    </style>
</head>

<body>

    <nav>
        <?php
        $path_prefix = '../';
        include '../Components/header.php';
        ?>
    </nav>

    <div class="rate-container">
        <?php if (isset($success) && $success): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> Review submitted successfully! Redirecting...
            </div>
            <script>
                setTimeout(function () {
                    // Redirect to the product view page (picture 2)
                    // Assuming path is relative to Content/Rate.php -> Categories/best_selling/view-product.php
                    // Adjust path if product_id implies a different directory, but user pointed to picture 2 which is best_selling
                    window.location.href = "../Categories/best_selling/view-product.php?id=<?php echo $product_id_post; ?>";
                }, 2000);
            </script>
        <?php endif; ?>

        <h2 class="page-title">Write a Review <span class="verified-badge" title="Verified Purchase"><i class="fas fa-check-circle"></i> Verified Purchase</span></h2>
        <p class="page-subtitle">Share your honest feedback to help other customers make informed decisions</p>

        <div class="user-info">
            <div class="user-avatar"><?php echo $user_initial; ?></div>
            <div class="user-details">
                <h3><?php echo htmlspecialchars($user_name); ?></h3>
                <p>Verified Customer</p>
            </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="reviewForm" onsubmit="validateReview(event)">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="rating" id="ratingInput" required value="0">
            <input type="hidden" name="media_list" id="mediaListInput" value="">

            <!-- OVERALL RATING SECTION -->
            <div class="form-group">
                <label class="label">Overall Rating <span style="color: #ef4444;">*</span></label>
                <div class="star-section">
                    <div class="star-section-title">How would you rate this product?</div>
                    <div class="star-rating" id="starContainer">
                        <i class="far fa-star" data-val="1" title="Poor"></i>
                        <i class="far fa-star" data-val="2" title="Fair"></i>
                        <i class="far fa-star" data-val="3" title="Good"></i>
                        <i class="far fa-star" data-val="4" title="Very Good"></i>
                        <i class="far fa-star" data-val="5" title="Excellent"></i>
                    </div>
                    <div class="rating-label" id="ratingLabel">Select a rating</div>
                </div>
            </div>

            <!-- DETAILED RATINGS SECTION -->
            <div class="form-group">
                <label class="label">Rate Specific Aspects (Optional)</label>
                <div class="rating-criteria">
                    <div class="criteria-item">
                        <span class="criteria-label">Quality</span>
                        <div class="star-rating criteria-stars" data-criteria="quality">
                            <i class="far fa-star" data-val="1"></i>
                            <i class="far fa-star" data-val="2"></i>
                            <i class="far fa-star" data-val="3"></i>
                            <i class="far fa-star" data-val="4"></i>
                            <i class="far fa-star" data-val="5"></i>
                        </div>
                    </div>
                    <div class="criteria-item">
                        <span class="criteria-label">Value for Money</span>
                        <div class="star-rating criteria-stars" data-criteria="value">
                            <i class="far fa-star" data-val="1"></i>
                            <i class="far fa-star" data-val="2"></i>
                            <i class="far fa-star" data-val="3"></i>
                            <i class="far fa-star" data-val="4"></i>
                            <i class="far fa-star" data-val="5"></i>
                        </div>
                    </div>
                    <div class="criteria-item">
                        <span class="criteria-label">Shipping & Packaging</span>
                        <div class="star-rating criteria-stars" data-criteria="shipping">
                            <i class="far fa-star" data-val="1"></i>
                            <i class="far fa-star" data-val="2"></i>
                            <i class="far fa-star" data-val="3"></i>
                            <i class="far fa-star" data-val="4"></i>
                            <i class="far fa-star" data-val="5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PHOTOS/VIDEOS SECTION -->
            <div class="form-group">
                <label class="label">Add Photos & Videos <span style="font-size: 12px; color: #999;">(Optional)</span></label>
                <span class="label-hint">Upload up to 5 images or videos to help other customers visualize the product</span>
                
                <div class="upload-container" id="uploadContainer">
                    <div class="upload-box" onclick="document.getElementById('mediaInput').click()" title="Click to upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Add Photo/Video</span>
                    </div>
                </div>
                <input type="file" name="media[]" id="mediaInput" accept="image/*,video/*" multiple
                    onchange="handleMultipleFileSelect(this)" style="display: none;">
                <span class="label-hint" style="margin-top: 10px;" id="fileCountInfo">0 files selected (max 5)</span>
            </div>

            <!-- REVIEW TITLE -->
            <div class="form-group">
                <label class="label">Review Title <span style="color: #ef4444;">*</span></label>
                <input type="text" name="review_title" id="reviewTitle" placeholder="e.g., Excellent quality and fast delivery" 
                       maxlength="100" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 14px; background: #fafafa;" required>
                <span class="label-hint" id="titleCount">0/100 characters</span>
            </div>

            <!-- DETAILED COMMENT SECTION -->
            <div class="form-group">
                <label class="label">Detailed Review <span style="color: #ef4444;">*</span></label>
                <span class="label-hint">Share what you liked, what could be improved, and any other relevant details</span>
                <textarea name="comment" id="reviewComment" 
                    placeholder="Describe your experience with this product in detail. What did you like? Any suggestions for improvement?" 
                    maxlength="1000" required></textarea>
                <div class="char-counter"><span id="charCount">0</span>/1000 characters</div>
            </div>

            <!-- WOULD RECOMMEND -->
            <div class="form-group">
                <label class="label">Would you recommend this product?</label>
                <div style="display: flex; gap: 20px; margin: 15px 0;">
                    <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500;">
                        <input type="radio" name="recommend" value="1" style="margin-right: 8px;">
                        <span style="color: #10b981;"><i class="fas fa-thumbs-up"></i> Yes, I'd recommend</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500;">
                        <input type="radio" name="recommend" value="0" style="margin-right: 8px;">
                        <span style="color: #ef4444;"><i class="fas fa-thumbs-down"></i> No, I wouldn't</span>
                    </label>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="button-group">
                <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Submit Review</button>
                <a href="Order-history.php" class="btn-cancel"><i class="fas fa-times"></i></a>
            </div>
        </form>

    </div>

    <script>
        const ratingLabels = {
            1: '⭐ Poor - Below expectations',
            2: '⭐⭐ Fair - Some issues',
            3: '⭐⭐⭐ Good - Meets expectations',
            4: '⭐⭐⭐⭐ Very Good - Exceeds expectations',
            5: '⭐⭐⭐⭐⭐ Excellent - Highly recommended'
        };

        const stars = document.querySelectorAll('#starContainer i');
        const ratingInput = document.getElementById('ratingInput');
        const ratingLabel = document.getElementById('ratingLabel');
        const uploadContainer = document.getElementById('uploadContainer');
        let selectedFiles = [];

        // MAIN STAR RATING LOGIC
        stars.forEach(star => {
            star.addEventListener('click', function () {
                const val = this.getAttribute('data-val');
                ratingInput.value = val;
                updateMainStars(val);
                ratingLabel.textContent = ratingLabels[val];
                ratingLabel.style.color = getColorForRating(val);
            });

            star.addEventListener('mouseenter', function () {
                const val = this.getAttribute('data-val');
                highlightStars(Array.from(stars), val);
            });
        });

        document.getElementById('starContainer').addEventListener('mouseleave', function () {
            if (ratingInput.value > 0) {
                updateMainStars(ratingInput.value);
            }
        });

        function updateMainStars(value) {
            stars.forEach(star => {
                const sVal = star.getAttribute('data-val');
                if (sVal <= value) {
                    star.classList.remove('far');
                    star.classList.add('fas', 'active');
                } else {
                    star.classList.remove('fas', 'active');
                    star.classList.add('far');
                }
            });
        }

        function highlightStars(starArray, value) {
            starArray.forEach(star => {
                const sVal = star.getAttribute('data-val');
                if (sVal <= value) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        }

        function getColorForRating(val) {
            const colors = { 1: '#ef4444', 2: '#f97316', 3: '#eab308', 4: '#84cc16', 5: '#10b981' };
            return colors[val] || '#999';
        }

        // CRITERIA STARS LOGIC
        document.querySelectorAll('.criteria-stars').forEach(criteriaGroup => {
            criteriaGroup.querySelectorAll('i').forEach(star => {
                star.addEventListener('click', function () {
                    const val = this.getAttribute('data-val');
                    updateCriteriaStars(criteriaGroup, val);
                });

                star.addEventListener('mouseenter', function () {
                    const val = this.getAttribute('data-val');
                    highlightStars(Array.from(criteriaGroup.querySelectorAll('i')), val);
                });
            });

            criteriaGroup.addEventListener('mouseleave', function () {
                const activeVal = this.querySelector('i.active')?.getAttribute('data-val') || 0;
                if (activeVal > 0) {
                    updateCriteriaStars(this, activeVal);
                }
            });
        });

        function updateCriteriaStars(container, value) {
            container.querySelectorAll('i').forEach(star => {
                const sVal = star.getAttribute('data-val');
                if (sVal <= value) {
                    star.classList.remove('far');
                    star.classList.add('fas', 'active');
                } else {
                    star.classList.remove('fas', 'active');
                    star.classList.add('far');
                }
            });
        }

        // TITLE CHARACTER COUNTER
        document.getElementById('reviewTitle').addEventListener('input', function () {
            const count = this.value.length;
            document.getElementById('titleCount').textContent = count + '/100 characters';
        });

        // COMMENT CHARACTER COUNTER
        document.getElementById('reviewComment').addEventListener('input', function () {
            const count = this.value.length;
            document.getElementById('charCount').textContent = count + '/1000 characters';
        });

        // MULTIPLE FILE UPLOAD LOGIC
        function handleMultipleFileSelect(input) {
            const files = Array.from(input.files);
            const maxFiles = 5;

            if (files.length > maxFiles) {
                alert('You can upload a maximum of ' + maxFiles + ' files');
                input.value = '';
                return;
            }

            selectedFiles = files;
            updateUploadContainer();
            updateMediaList();
            updateFileInfo();
        }

        function updateUploadContainer() {
            const container = document.getElementById('uploadContainer');
            
            // Clear container but keep Add button
            container.innerHTML = '';

            // Add uploaded files first
            selectedFiles.forEach((file, index) => {
                const fileBox = document.createElement('div');
                fileBox.className = 'upload-box uploaded';
                fileBox.style.position = 'relative';

                const reader = new FileReader();
                reader.onload = function (e) {
                    let mediumElement;

                    if (file.type.startsWith('image/')) {
                        mediumElement = document.createElement('img');
                        mediumElement.src = e.target.result;
                        mediumElement.className = 'media-preview';
                    } else if (file.type.startsWith('video/')) {
                        mediumElement = document.createElement('video');
                        mediumElement.src = e.target.result;
                        mediumElement.className = 'media-preview';
                        mediumElement.controls = false;
                    } else {
                        mediumElement = document.createElement('div');
                        mediumElement.innerHTML = '<i class="fas fa-file" style="font-size: 40px; color: #999;"></i>';
                    }

                    fileBox.appendChild(mediumElement);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'media-remove-btn';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.onclick = function (e) {
                        e.preventDefault();
                        removeFile(index);
                    };
                    fileBox.appendChild(removeBtn);
                };
                reader.readAsDataURL(file);

                container.appendChild(fileBox);
            });

            // Add the upload button if not at max
            if (selectedFiles.length < 5) {
                const addBtn = document.createElement('div');
                addBtn.className = 'upload-box';
                addBtn.onclick = () => document.getElementById('mediaInput').click();
                addBtn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i><span>Add Photo/Video</span>';
                container.appendChild(addBtn);
            }
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateUploadContainer();
            updateMediaList();
            updateFileInfo();
            // Reset input
            document.getElementById('mediaInput').value = '';
        }

        function updateMediaList() {
            // Store file names for backend processing
            const fileNames = selectedFiles.map(f => f.name).join('|');
            document.getElementById('mediaListInput').value = fileNames;
        }

        function updateFileInfo() {
            document.getElementById('fileCountInfo').textContent = selectedFiles.length + ' file(s) selected (max 5)';
        }

        // FORM VALIDATION
        function validateReview(e) {
            const rating = document.getElementById('ratingInput').value;
            const title = document.getElementById('reviewTitle').value;
            const comment = document.getElementById('reviewComment').value;

            if (rating == 0) {
                e.preventDefault();
                alert('Please select a rating');
                return false;
            }

            if (title.trim().length < 5) {
                e.preventDefault();
                alert('Please provide a review title (minimum 5 characters)');
                return false;
            }

            if (comment.trim().length < 20) {
                e.preventDefault();
                alert('Please provide a detailed review (minimum 20 characters)');
                return false;
            }

            return true;
        }
    </script>
    <div style="margin-top: 50px;">
        <?php
        $path_prefix = '../';
        include '../Components/footer.php';
        ?>
    </div>
</body>

</html>
