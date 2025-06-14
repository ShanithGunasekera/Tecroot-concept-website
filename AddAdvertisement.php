<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Product | Gamer's Haven</title>
    <!-- Favicon -->
    <link rel="icon" href="2.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Ubuntu+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gamer-green: #00ff00;
            --gamer-dark: #121212;
            --gamer-light: #1e1e1e;
            --gamer-accent: #00cc00;
        }
        
        body {
            font-family: 'Ubuntu Mono', monospace;
            background-color: var(--gamer-dark);
            color: #fff;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 255, 0, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 255, 0, 0.05) 0%, transparent 20%);
        }
        
        .gamer-nav {
            background-color: rgba(0, 0, 0, 0.8) !important;
            border-bottom: 2px solid var(--gamer-green);
            font-family: 'Press Start 2P', cursive;
        }
        
        .gamer-nav .nav-link {
            color: #fff !important;
            transition: all 0.3s;
            position: relative;
            margin: 0 10px;
        }
        
        .gamer-nav .nav-link:hover {
            color: var(--gamer-green) !important;
            text-shadow: 0 0 5px var(--gamer-green);
        }
        
        .gamer-nav .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--gamer-green);
            transition: width 0.3s;
        }
        
        .gamer-nav .nav-link:hover:after {
            width: 100%;
        }
        
        .gamer-card {
            background-color: var(--gamer-light);
            border: 1px solid var(--gamer-green);
            border-radius: 0;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.1);
            margin-top: 30px;
        }
        
        .gamer-card-header {
            background-color: rgba(0, 0, 0, 0.5);
            border-bottom: 1px solid var(--gamer-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 1.2rem;
            padding: 15px;
            position: relative;
        }
        
        .gamer-card-header:before {
            content: '>>';
            color: var(--gamer-green);
            margin-right: 10px;
        }
        
        .gamer-form-control {
            background-color: rgba(0, 0, 0, 0.5);
            border: 1px solid #333;
            color: #fff;
            border-radius: 0;
            transition: all 0.3s;
        }
        
        .gamer-form-control:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--gamer-green);
            color: var(--gamer-green);
            box-shadow: 0 0 0 0.25rem rgba(0, 255, 0, 0.25);
        }
        
        .gamer-btn {
            background-color: var(--gamer-green);
            color: #000;
            border: none;
            border-radius: 0;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            padding: 10px 20px;
            transition: all 0.3s;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }
        
        .gamer-btn:hover {
            background-color: #00cc00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 0, 0.3);
        }
        
        .gamer-btn:active {
            transform: translateY(0);
        }
        
        .gamer-btn:after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            transition: all 0.3s;
        }
        
        .gamer-btn:hover:after {
            left: 100%;
        }
        
        .file-upload {
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .file-upload-input {
            position: absolute;
            font-size: 100px;
            opacity: 0;
            right: 0;
            top: 0;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: block;
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.5);
            border: 1px dashed #333;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-upload-label:hover {
            border-color: var(--gamer-green);
            background-color: rgba(0, 0, 0, 0.7);
        }
        
        .file-upload-icon {
            font-size: 2rem;
            color: var(--gamer-green);
            margin-bottom: 10px;
        }
        
        .publish-check {
            accent-color: var(--gamer-green);
            transform: scale(1.5);
            margin-right: 10px;
        }
        
        .success-message {
            animation: blink 1s infinite alternate;
        }
        
        @keyframes blink {
            from { opacity: 1; }
            to { opacity: 0.7; }
        }
        
        /* Terminal-like effect for form */
        .terminal-effect {
            position: relative;
        }
        
        .terminal-effect:before {
            content: '>';
            position: absolute;
            left: 15px;
            top: 12px;
            color: var(--gamer-green);
            font-family: 'Ubuntu Mono', monospace;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .gamer-nav .navbar-nav {
                background-color: rgba(0, 0, 0, 0.9);
                padding: 15px;
                margin-top: 10px;
                border: 1px solid var(--gamer-green);
            }
            
            .gamer-card {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Gaming-style Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark gamer-nav">
        <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="home.html">
            <img src="1.png" alt="Tecroot Logo" class="logo-img me-2" style="height: 30px;">
        </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="empIndex.html"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php"><i class="fas fa-list me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="account.php"><i class="fas fa-user me-1"></i> Add control</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-plus-circle me-1"></i> Add Product</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="gamer-card">
                    <div class="gamer-card-header text-center">
                        ADD NEW PRODUCT
                    </div>
                    <div class="card-body">
                        <form action="./AddAdvertisement.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <!-- Product Name -->
                            <div class="mb-4 terminal-effect">
                                <input type="text" class="form-control gamer-form-control" name="txtTitle" placeholder="Product Name*" required>
                                <div class="invalid-feedback" style="color: var(--gamer-green);">
                                    Please enter a product name.
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-4 terminal-effect">
                                <input type="text" class="form-control gamer-form-control" name="txtPrice" placeholder="Price* (e.g., 49.99)" required>
                                <div class="invalid-feedback" style="color: var(--gamer-green);">
                                    Please enter a valid price.
                                </div>
                            </div>
                            
                            <!-- Category -->
                            <div class="mb-4 terminal-effect">
                                <select class="form-select gamer-form-control" name="txtCategory" required>
                                    <option value="" disabled selected>Select Category*</option>
                                    <option value="accessories">Gaming Accessory</option>
                                    <option value="merchandise">Merchandise</option>
                                    <option value="collectibles">Collectibles</option>
                                    <option value="video-games">Video-Games</option>
                                </select>
                                <div class="invalid-feedback" style="color: var(--gamer-green);">
                                    Please select a category.
                                </div>
                            </div>
                            
                            <!-- Image Upload -->
                            <div class="mb-4">
                                <div class="file-upload">
                                    <input type="file" class="file-upload-input" id="imageFile" name="imageFile" accept="image/*" required>
                                    <label for="imageFile" class="file-upload-label">
                                        <div class="file-upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <div class="file-upload-text">
                                            <span id="file-name">Click to upload product image</span>
                                        </div>
                                    </label>
                                    <div class="invalid-feedback d-block" style="color: var(--gamer-green);">
                                        Please upload an image.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Publish Checkbox -->
                            <div class="mb-4 form-check">
                                <input class="form-check-input publish-check" type="checkbox" name="txtPublish" id="txtPublish">
                                <label class="form-check-label" for="txtPublish">
                                    Publish this product immediately
                                </label>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" name="btnSubmit" class="btn gamer-btn">
                                    <i class="fas fa-rocket me-2"></i> LAUNCH PRODUCT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- PHP Processing Section -->
                <?php
                if (isset($_POST["btnSubmit"])) {
                    $title = $_POST["txtTitle"];
                    $price = $_POST["txtPrice"];
                    $publish = isset($_POST["txtPublish"]) ? 1 : 0;
                    $category = $_POST["txtCategory"];

                    // Check if the uploads directory exists, if not create it
                    $uploadDir = "uploads/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $file_name = basename($_FILES["imageFile"]["name"]);
                    $file_path = $uploadDir . $file_name;

                    if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $file_path)) {
                        $conn = mysqli_connect("localhost", "root", "", "tecroot");

                        if (!$conn) {
                            die("DB connection failed: " . mysqli_connect_error());
                        }

                        // Ensure to escape the inputs to prevent SQL injection
                        $title = mysqli_real_escape_string($conn, $title);
                        $price = mysqli_real_escape_string($conn, $price);
                        $file_path = mysqli_real_escape_string($conn, $file_path);
                        $category = mysqli_real_escape_string($conn, $category);

                        $sql = "INSERT INTO advertisement (Product_Name, Price, Image_Path, Publish, Category) VALUES ('$title', '$price', '$file_path', '$publish', '$category')";

                        if (mysqli_query($conn, $sql)) {
                            echo '<div class="alert alert-success mt-4 text-center success-message" role="alert">
                                    <i class="fas fa-check-circle me-2"></i> Product launched successfully! <i class="fas fa-gamepad"></i>
                                  </div>';
                        } else {
                            echo '<div class="alert alert-danger mt-4" role="alert">
                                    Error: ' . mysqli_error($conn) . '
                                  </div>';
                        }

                        mysqli_close($conn);
                    } else {
                        echo '<div class="alert alert-danger mt-4" role="alert">
                                Error uploading the file.
                              </div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Form validation
        (function () {
            'use strict'
            
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')
            
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
        
        // Show selected file name
        document.getElementById('imageFile').addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : "Click to upload product image";
            document.getElementById('file-name').textContent = fileName;
            
            // Preview image
            if (e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var preview = document.getElementById('image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'image-preview';
                        preview.style.maxWidth = '100%';
                        preview.style.marginTop = '15px';
                        preview.style.border = '1px solid var(--gamer-green)';
                        document.querySelector('.file-upload-text').after(preview);
                    }
                    preview.src = event.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        // Add gaming-style typing effect to labels
        document.querySelectorAll('.terminal-effect input, .terminal-effect select').forEach(el => {
            el.addEventListener('focus', function() {
                this.parentElement.style.animation = 'none';
                void this.parentElement.offsetWidth; // trigger reflow
                this.parentElement.style.animation = 'blink 0.5s infinite alternate';
            });
            
            el.addEventListener('blur', function() {
                this.parentElement.style.animation = 'none';
            });
        });
    </script>
</body>
</html>