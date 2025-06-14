<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "tecroot");
if (!$conn) {
    die("DB connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM advertisement";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Advertisements | Tecroot</title>
    <!-- Favicon -->
    <link rel="icon" href="2.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        :root {
            --gamer-green: #32CD32;
            --gamer-dark: #0d0d0d;
            --gamer-light: #1a1a1a;
            --gamer-accent: #00ff00;
        }
        
        body {
            font-family: 'Anta', sans-serif;
            background-color: var(--gamer-dark);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 255, 0, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 255, 0, 0.05) 0%, transparent 20%);
        }
        
        .gamer-nav {
            background-color: rgba(0, 0, 0, 0.9) !important;
            border-bottom: 2px solid var(--gamer-green);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }
        
        .logo-img {
            height: 30px;
            margin-right: 10px;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem 0;
        }
        
        .page-header {
            color: var(--gamer-green);
            text-align: center;
            margin-bottom: 3rem;
            text-shadow: 0 0 10px rgba(50, 205, 50, 0.5);
            position: relative;
        }
        
        .page-header:after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background: var(--gamer-green);
            margin: 15px auto;
            box-shadow: 0 0 10px var(--gamer-green);
        }
        
        .product-card {
            background-color: var(--gamer-light);
            border: 1px solid var(--gamer-green);
            border-radius: 5px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 255, 0, 0.2);
        }
        
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 3px solid var(--gamer-green);
        }
        
        .product-body {
            padding: 1.5rem;
        }
        
        .product-title {
            color: var(--gamer-green);
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }
        
        .form-label {
            color: var(--gamer-green);
            font-weight: bold;
        }
        
        .form-control {
            background-color: #333;
            color: white;
            border: 1px solid var(--gamer-green);
            border-radius: 0;
            margin-bottom: 1rem;
        }
        
        .form-control:focus {
            background-color: #444;
            color: white;
            border-color: var(--gamer-accent);
            box-shadow: 0 0 0 0.25rem rgba(50, 205, 50, 0.25);
        }
        
        .btn-gamer {
            background-color: var(--gamer-green);
            color: #000;
            border: none;
            border-radius: 0;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s;
            text-transform: uppercase;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-gamer:hover {
            background-color: var(--gamer-accent);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 255, 0, 0.3);
        }
        
        .form-check-input {
            background-color: #333;
            border: 1px solid var(--gamer-green);
        }
        
        .form-check-input:checked {
            background-color: var(--gamer-green);
            border-color: var(--gamer-green);
        }
        
        .form-check-label {
            color: var(--gamer-green);
            margin-left: 0.5rem;
        }
        
        .gamer-footer {
            background-color: var(--gamer-dark);
            color: var(--gamer-green);
            text-align: center;
            padding: 1rem;
            border-top: 1px solid var(--gamer-green);
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.5);
        }
        
        /* Animation for cards */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-card {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark gamer-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="home.html">
                <img src="1.png" alt="Tecroot Logo" class="logo-img">
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
                        <a class="nav-link" href="products.php"><i class="fas fa-gamepad me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="editadvertisement.php"><i class="fas fa-edit me-1"></i> Edit Ads</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="account.php"><i class="fas fa-sign-out-alt me-1"></i> Add control</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1 class="page-header">Edit Advertisements</h1>
            
            <div class="row">
                <?php 
                $delay = 0;
                while ($row = mysqli_fetch_assoc($result)) { 
                    $delay += 0.1;
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card animate-card" style="animation-delay: <?php echo $delay; ?>s">
                        <img src="<?php echo htmlspecialchars($row['Image_Path']); ?>" class="product-img" alt="<?php echo htmlspecialchars($row['Product_Name']); ?>">
                        <div class="product-body">
                            <h3 class="product-title"><?php echo htmlspecialchars($row['Product_Name']); ?></h3>
                            
                            <form action="EditHandler.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                
                                <div class="mb-3">
                                    <label for="title_<?php echo $row['id']; ?>" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="title_<?php echo $row['id']; ?>" 
                                           name="txtTitle" value="<?php echo htmlspecialchars($row['Product_Name']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="price_<?php echo $row['id']; ?>" class="form-label">Price</label>
                                    <input type="text" class="form-control" id="price_<?php echo $row['id']; ?>" 
                                           name="txtPrice" value="<?php echo htmlspecialchars($row['Price']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image_<?php echo $row['id']; ?>" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="image_<?php echo $row['id']; ?>" 
                                           name="imageFile" accept="image/*">
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="publish_<?php echo $row['id']; ?>" 
                                           name="txtPublish" <?php echo $row['Publish'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="publish_<?php echo $row['id']; ?>">
                                        Publish this product
                                    </label>
                                </div>
                                
                                <button type="submit" name="btnSubmit" class="btn btn-gamer">
                                    <i class="fas fa-save me-2"></i> Update Product
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="gamer-footer">
        <div class="container">
            <p class="mb-0">© <?php echo date('Y'); ?> TECROOT - ALL RIGHTS RESERVED</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.animate-card');
            cards.forEach(card => {
                card.style.opacity = '1';
            });
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>