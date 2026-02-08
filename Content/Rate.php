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
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));

    // Handle File Upload (Basic Implementation)
    $media_path = "";
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
        $filename = $_FILES['media']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_name = uniqid() . "." . $ext;
            $upload_dir = "../uploads/reviews/";
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0777, true);

            if (move_uploaded_file($_FILES['media']['tmp_name'], $upload_dir . $new_name)) {
                $media_path = "uploads/reviews/" . $new_name;
            }
        }
    }

    // AI Sentiment Analysis
    include("../Categories/nlp_core.php");
    $ai_res = analyzeSentimentAI($comment);
    $sentiment = $ai_res['result']['sentiment'];
    $confidence = $ai_res['result']['confidence_score'];

    // Insert
    $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, comment, media_url, sentiment, confidence, created_at) VALUES ('$user_id', '$product_id_post', '$order_id_post', '$rating', '$comment', '$media_path', '$sentiment', '$confidence', NOW())";

    if (mysqli_query($conn, $sql)) {
        $success = true;
    } else {
        $msg = "Error: " . mysqli_error($conn);
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
            font-size: 24px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 30px;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #555;
            margin-right: 15px;
        }

        .user-name {
            font-weight: 500;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .label {
            display: block;
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 14px;
            color: #333;
        }

        /* Star Rating */
        .star-rating {
            display: flex;
            gap: 5px;
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
        }

        .star-rating i.active {
            color: #ffc107;
        }

        .star-rating i:hover {
            color: #ffca2c;
            /* Slight hover highlight */
        }

        /* Upload Box */
        .upload-box {
            border: 2px dashed #ddd;
            border-radius: 4px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s;
            background-color: #fafafa;
        }

        .upload-box:hover {
            border-color: #aaa;
        }

        .upload-box.uploaded {
            border-color: #28a745;
            /* Green border */
            background-color: transparent;
        }

        .upload-box i {
            font-size: 24px;
            color: #777;
            margin-bottom: 10px;
            display: block;
        }

        .upload-box span {
            color: #777;
            font-size: 14px;
        }

        .media-preview {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 4px;
            margin-top: 10px;
        }

        /* Textarea */
        textarea {
            width: 100%;
            height: 150px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            background-color: #fafafa;
        }

        textarea:focus {
            outline: none;
            border-color: #2A3B7E;
            background-color: white;
        }

        /* Buttons */
        .btn-submit {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #2A3B7E;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: #1e2b5e;
        }

        .btn-cancel {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 15px;
            color: #777;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-cancel:hover {
            color: #333;
        }

        /* Hidden Input for Stars */
        input[type="number"]#ratingInput {
            display: none;
        }

        input[type="file"]#mediaInput {
            display: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
            font-weight: 500;
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

        <h2 class="page-title">Rate Product</h2>

        <div class="user-info">
            <div class="user-avatar"><?php echo $user_initial; ?></div>
            <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

            <!-- Rating -->
            <div class="form-group">
                <span class="label">Product Quality</span>
                <div class="star-rating" id="starContainer">
                    <i class="far fa-star" data-val="1"></i>
                    <i class="far fa-star" data-val="2"></i>
                    <i class="far fa-star" data-val="3"></i>
                    <i class="far fa-star" data-val="4"></i>
                    <i class="far fa-star" data-val="5"></i>
                </div>
                <input type="number" name="rating" id="ratingInput" required value="0">
            </div>

            <!-- Photo/Video -->
            <div class="form-group">
                <span class="label">Add Photo/Video</span>
                <div class="upload-box" onclick="document.getElementById('mediaInput').click()">
                    <i class="fas fa-camera"></i>
                    <span id="uploadText">Click to upload</span>
                </div>
                <input type="file" name="media" id="mediaInput" accept="image/*,video/*"
                    onchange="handleFileSelect(this)">
            </div>

            <!-- Comment -->
            <div class="form-group">
                <span class="label">Share your experience</span>
                <textarea name="comment" placeholder="The product quality is excellent..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Submit Review</button>
            <a href="Order-history.php" class="btn-cancel">Cancel</a>
        </form>

    </div>

    <script>
        // Star Rating Logic
        const stars = document.querySelectorAll('.star-rating i');
        const ratingInput = document.getElementById('ratingInput');

        stars.forEach(star => {
            star.addEventListener('click', function () {
                const val = this.getAttribute('data-val');
                ratingInput.value = val;
                updateStars(val);
            });
            // Optional: Hover effect handled by CSS or JS?
            // CSS handles active state, let's keep it permanent on click
        });

        function updateStars(value) {
            stars.forEach(star => {
                const sVal = star.getAttribute('data-val');
                if (sVal <= value) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                    star.classList.add('active');
                } else {
                    star.classList.remove('fas');
                    star.classList.remove('active');
                    star.classList.add('far');
                }
            });
        }

        // File Select Logic
        function handleFileSelect(input) {
            const file = input.files[0];
            const uploadBox = document.querySelector('.upload-box');

            if (file) {
                // Clear previous content
                uploadBox.innerHTML = '';
                uploadBox.classList.add('uploaded'); // Add success class

                const reader = new FileReader();
                reader.onload = function (e) {
                    let previewElement;

                    if (file.type.startsWith('image/')) {
                        previewElement = document.createElement('img');
                        previewElement.src = e.target.result;
                        previewElement.className = 'media-preview';
                    } else if (file.type.startsWith('video/')) {
                        previewElement = document.createElement('video');
                        previewElement.src = e.target.result;
                        previewElement.className = 'media-preview';
                        previewElement.controls = true;
                    } else {
                        // Fallback for non-media
                        previewElement = document.createElement('div');
                        previewElement.textContent = file.name;
                    }

                    uploadBox.appendChild(previewElement);
                };
                reader.readAsDataURL(file);
            } else {
                // Reset if no file
                uploadBox.innerHTML = '<i class="fas fa-camera"></i><span id="uploadText">Click to upload</span>';
                uploadBox.classList.remove('uploaded');
            }
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
