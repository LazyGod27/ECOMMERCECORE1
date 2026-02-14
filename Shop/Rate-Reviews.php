<?php
session_start();
include("../Database/config.php");

$product_name = $_GET['product_name'] ?? 'Product';
$product_id = abs(crc32($product_name)) % 2147483647;

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../php/login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating'] ?? 5);
    $comment = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');
    $review_title = mysqli_real_escape_string($conn, $_POST['review_title'] ?? '');

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

    $media_json = !empty($media_paths) ? json_encode($media_paths) : null;

    // AI Sentiment Analysis
    include("../Categories/nlp_core.php");
    $ai_res = analyzeSentimentAI($comment);
    $sentiment = $ai_res['result']['sentiment'];
    $confidence = $ai_res['result']['confidence_score'];

    // Insert with new fields
    $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, review_title, comment, media_url, sentiment, confidence, created_at) 
            VALUES ('$user_id', '$product_id', 0, '$rating', '$review_title', '$comment', '$media_json', '$sentiment', '$confidence', NOW())";

    if (mysqli_query($conn, $sql)) {
        $success_msg = "Thank you! Your review for <strong>" . htmlspecialchars($product_name) . "</strong> has been submitted successfully.";

        // Email Notification to Admin
        require '../PHPMailer/src/Exception.php';
        require '../PHPMailer/src/PHPMailer.php';
        require '../PHPMailer/src/SMTP.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'linbilcelestre31@gmail.com';
            $mail->Password = 'erdrvfcuoeibstxo';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('no-reply@imarketph.com', 'IMarket PH');
            $mail->addAddress('linbilcelestre31@gmail.com', 'Admin');

            $mail->isHTML(true);
            $mail->Subject = 'New Product Review: ' . $product_name;
            $user_name = $_SESSION['fullname'] ?? 'A customer';
            $media_count = count($media_paths);
            $media_info = $media_count > 0 ? "<p><b>Media Files:</b> $media_count image(s)/video(s) attached</p>" : "";
            
            $mail->Body = "<h3>New Product Review Received</h3>
                           <p><b>Product:</b> $product_name</p>
                           <p><b>Rating:</b> $rating ⭐</p>
                           <p><b>Title:</b> $review_title</p>
                           <p><b>Customer:</b> $user_name</p>
                           <p><b>Sentiment:</b> $sentiment (Confidence: {$confidence}%)</p>
                           $media_info
                           <p><b>Review:</b></p>
                           <p>$comment</p>
                           <hr>
                           <p><a href='http://localhost/ecommerce%20core1/Shop/index.php'>View on Shop</a></p>";
            $mail->AltBody = "New Product Review\nProduct: $product_name\nRating: $rating Stars\nCustomer: $user_name\nComment: $comment";

            $mail->send();
        } catch (Exception $e) {
            // Silently fail email but review is already saved
        }
    } else {
        $error_msg = "Error submitting review: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Product - <?php echo htmlspecialchars($product_name); ?> | iMarket</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        nav {
            margin-bottom: 30px;
        }

        .rate-container {
            max-width: 750px;
            margin: 0 auto;
            background: white;
            padding: 45px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .rate-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f4f8;
        }

        .rate-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .product-name {
            font-size: 18px;
            color: #64748b;
            font-weight: 500;
            word-break: break-word;
        }

        .form-group {
            margin-bottom: 32px;
        }

        .form-label {
            display: block;
            font-weight: 700;
            margin-bottom: 14px;
            font-size: 14px;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-hint {
            font-size: 12px;
            color: #999;
            font-weight: 400;
            text-transform: none;
            display: block;
            margin-top: 4px;
        }

        /* Star Rating */
        .star-rating-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .star-rating {
            display: flex;
            justify-content: center;
            gap: 12px;
            font-size: 36px;
            margin: 15px 0;
        }

        .star-rating i {
            cursor: pointer;
            color: #ddd;
            transition: all 0.2s ease;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .star-rating i:hover {
            color: #ffc107;
            transform: scale(1.2);
        }

        .star-rating i.active {
            color: #ffc107;
            filter: drop-shadow(0 2px 4px rgba(255, 193, 7, 0.3));
        }

        .rating-label {
            font-size: 13px;
            color: #999;
            margin-top: 10px;
            font-style: italic;
        }

        /* Title Input */
        input[type="text"],
        textarea {
            width: 100%;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            background: #fafafa;
            color: #333;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #2A3B7E;
            background: white;
            box-shadow: 0 0 0 3px rgba(42, 59, 126, 0.1);
        }

        input[type="text"]::placeholder,
        textarea::placeholder {
            color: #999;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        /* Character Counter */
        .char-counter {
            font-size: 12px;
            color: #999;
            margin-top: 6px;
            text-align: right;
        }

        /* File Upload Container */
        .upload-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 14px;
            margin-top: 15px;
        }

        .upload-box {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 22px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fafafa;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .upload-box:hover {
            border-color: #2A3B7E;
            background: #f0f3ff;
        }

        .upload-box.uploaded {
            border-color: #10b981;
            background: #f0fdf4;
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
            font-size: 12px;
            font-weight: 500;
            word-break: break-word;
        }

        .media-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        .file-info {
            font-size: 12px;
            color: #999;
            margin-top: 8px;
        }

        /* Hidden Inputs */
        input[type="radio"],
        input[type="file"],
        input[type="number"] {
            display: none;
        }

        /* Buttons */
        .button-group {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 15px;
            margin-top: 35px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #2A3B7E 0%, #1e2b5e 100%);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(42, 59, 126, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(42, 59, 126, 0.3);
        }

        .btn-cancel {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 20px;
            transition: all 0.2s;
            cursor: pointer;
            background: white;
        }

        .btn-cancel:hover {
            color: #333;
            border-color: #999;
            background: #f5f5f5;
        }

        /* Alerts */
        .alert {
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c8e6c9 100%);
            color: #155724;
            border: 1px solid #b8e6b8;
            box-shadow: 0 2px 8px rgba(21, 87, 36, 0.1);
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
            box-shadow: 0 2px 8px rgba(114, 28, 36, 0.1);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #2A3B7E;
        }

        @media (max-width: 768px) {
            .rate-container {
                padding: 25px;
            }

            .rate-header h1 {
                font-size: 24px;
            }

            .button-group {
                grid-template-columns: 1fr;
            }

            .star-rating {
                gap: 8px;
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <nav>
        <?php $path_prefix = '../'; include '../Components/header.php'; ?>
    </nav>

    <div class="rate-container">
        <?php if ($success_msg): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
            <div style="text-align: center;">
                <a href="index.php" class="btn-submit" style="display: inline-block; width: auto; text-decoration: none; margin: 20px auto;">
                    <i class="fas fa-arrow-left"></i> Return to Shop
                </a>
            </div>
        <?php else: ?>
            <div class="rate-header">
                <h1><i class="fas fa-star"></i> Rate Product</h1>
                <div class="product-name">
                    <?php echo htmlspecialchars($product_name); ?>
                </div>
            </div>

            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="reviewForm" onsubmit="validateReview(event)">
                <input type="hidden" name="rating" id="ratingInput" required value="0">
                <input type="hidden" name="media_list" id="mediaListInput" value="">

                <!-- Star Rating -->
                <div class="form-group">
                    <label class="form-label">Product Rating <span style="color: #ef4444;">*</span></label>
                    <div class="star-rating-container">
                        <div class="form-hint">How would you rate this product?</div>
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

                <!-- Review Title -->
                <div class="form-group">
                    <label class="form-label">Review Title <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="review_title" id="reviewTitle" 
                           placeholder="e.g., Excellent quality and fast delivery" 
                           maxlength="100" required>
                    <div class="char-counter"><span id="titleCount">0</span>/100 characters</div>
                </div>

                <!-- Photo/Video Upload -->
                <div class="form-group">
                    <label class="form-label">Add Photos & Videos <span style="font-size: 12px; color: #999; text-transform: none;">(Optional)</span></label>
                    <span class="form-hint">Upload up to 5 images or videos to show the actual product</span>
                    
                    <div class="upload-container" id="uploadContainer">
                        <div class="upload-box" onclick="document.getElementById('mediaInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Add Photo/Video</span>
                        </div>
                    </div>
                    <input type="file" name="media[]" id="mediaInput" accept="image/*,video/*" multiple 
                           onchange="handleMultipleFileSelect(this)">
                    <div class="file-info" id="fileCountInfo">0 files selected (max 5)</div>
                </div>

                <!-- Review Comment -->
                <div class="form-group">
                    <label class="form-label">Write Your Review <span style="color: #ef4444;">*</span></label>
                    <span class="form-hint">Share what you liked, what could be improved, and any other relevant details</span>
                    <textarea name="comment" id="reviewComment" 
                              placeholder="Describe your experience with this product. What did you like? What could be improved?" 
                              maxlength="1000" required></textarea>
                    <div class="char-counter"><span id="charCount">0</span>/1000 characters</div>
                </div>

                <!-- Submit Button -->
                <div class="button-group">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Review
                    </button>
                    <button type="button" class="btn-cancel" onclick="history.back()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        const ratingLabels = {
            1: '⭐ Poor - Below expectations',
            2: '⭐⭐ Fair - Some issues',
            3: '⭐⭐⭐ Good - Meets expectations',
            4: '⭐⭐⭐⭐ Very Good - Exceeds expectations',
            5: '⭐⭐⭐⭐⭐ Excellent - Highly recommended'
        };

        let selectedFiles = [];
        const stars = document.querySelectorAll('#starContainer i');
        const ratingInput = document.getElementById('ratingInput');
        const ratingLabel = document.getElementById('ratingLabel');
        const uploadContainer = document.getElementById('uploadContainer');

        // Star Rating Logic
        stars.forEach(star => {
            star.addEventListener('click', function () {
                const val = this.getAttribute('data-val');
                ratingInput.value = val;
                updateStars(val);
                ratingLabel.textContent = ratingLabels[val];
                ratingLabel.style.color = getColor(val);
            });

            star.addEventListener('mouseenter', function () {
                const val = this.getAttribute('data-val');
                highlightStars(Array.from(stars), val);
            });
        });

        document.getElementById('starContainer').addEventListener('mouseleave', function () {
            if (ratingInput.value > 0) {
                updateStars(ratingInput.value);
            }
        });

        function updateStars(value) {
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
                star.style.color = sVal <= value ? '#ffc107' : '#ddd';
            });
        }

        function getColor(val) {
            const colors = {1: '#ef4444', 2: '#f97316', 3: '#eab308', 4: '#84cc16', 5: '#10b981'};
            return colors[val] || '#999';
        }

        // Title Character Counter
        document.getElementById('reviewTitle').addEventListener('input', function () {
            document.getElementById('titleCount').textContent = this.value.length;
        });

        // Comment Character Counter
        document.getElementById('reviewComment').addEventListener('input', function () {
            document.getElementById('charCount').textContent = this.value.length;
        });

        // File Upload Handler
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
            updateFileInfo();
        }

        function updateUploadContainer() {
            const container = document.getElementById('uploadContainer');
            container.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const fileBox = document.createElement('div');
                fileBox.className = 'upload-box uploaded';

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
            updateFileInfo();
            document.getElementById('mediaInput').value = '';
        }

        function updateFileInfo() {
            document.getElementById('fileCountInfo').textContent = selectedFiles.length + ' file(s) selected (max 5)';
        }

        // Form Validation
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
</body>

</html>
