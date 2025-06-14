<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "tecroot", "3306");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Search functionality
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gaming Products | Tecroot</title>
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
        
        .nav-links {
            display: flex;
            justify-content: space-around;
            width: 50%;
            text-transform: uppercase;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-links a {
            color: var(--gamer-green) !important;
            text-decoration: none;
            padding: 10px 15px;
            transition: all 0.3s;
            position: relative;
        }
        
        .nav-links a:hover {
            color: #1e90ff !important;
            text-shadow: 0 0 5px var(--gamer-green);
            transform: translateY(-3px);
        }
        
        .nav-links a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--gamer-green);
            transition: width 0.3s;
        }
        
        .nav-links a:hover:after {
            width: 100%;
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
            background-color: #1a1a1a;
            border: 1px solid var(--gamer-green);
            border-radius: 5px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 0, 0.2);
        }
        
        .product-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 3px solid var(--gamer-green);
        }
        
        .product-body {
            padding: 1.5rem;
        }
        
        .product-title {
            color: var(--gamer-green);
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .stock-info {
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .in-stock {
            color: #32CD32;
        }
        
        .out-of-stock {
            color: #FF3333;
        }
        
        .btn-gamer {
            background-color: var(--gamer-green);
            color: #000;
            border: none;
            border-radius: 0;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s;
            width: 100%;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }
        
        .btn-gamer:hover {
            background-color: #1e90ff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 255, 0, 0.3);
        }
        
        .btn-gamer:disabled {
            background-color: #666;
            cursor: not-allowed;
        }
        
        .btn-view-cart {
            background-color: #333;
            color: var(--gamer-green);
            border: 1px solid var(--gamer-green);
        }
        
        .btn-view-cart:hover {
            background-color: #444;
            color: #1e90ff;
            border-color: #1e90ff;
        }
        
        .gamer-footer {
            background-color: var(--gamer-dark);
            color: var(--gamer-green);
            text-align: center;
            padding: 1rem;
            border-top: 1px solid var(--gamer-green);
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.5);
        }
        
        .qty-control {
            display: flex;
            margin-bottom: 1rem;
        }
        
        .qty-btn {
            background: #333;
            color: white;
            border: 1px solid #444;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .qty-input {
            width: 50px;
            text-align: center;
            border: 1px solid #444;
            border-left: none;
            border-right: none;
            background: #2d2d2d;
            color: white;
        }
        
        .qty-input:disabled {
            background: #444;
            color: #888;
        }
        
        /* Search bar styles */
        .search-container {
            margin-right: 15px;
        }
        
        .search-form {
            display: flex;
        }
        
        .search-input {
            background-color: #1a1a1a !important;
            border: 1px solid var(--gamer-green) !important;
            color: white !important;
            border-radius: 0;
            width: 200px;
        }
        
        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(50, 205, 50, 0.25) !important;
            border-color: var(--gamer-green) !important;
        }
        
        .search-btn {
            background-color: var(--gamer-green) !important;
            color: black !important;
            border: 1px solid var(--gamer-green) !important;
            border-radius: 0;
        }
        
        .search-btn:hover {
            background-color: #1e90ff !important;
            border-color: #1e90ff !important;
        }
        
        .clear-search {
            color: var(--gamer-green);
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        
        .clear-search:hover {
            color: #1e90ff;
        }
    </style>
</head>
<body>
    <!-- Gaming Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark gamer-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="home.html">
                <img src="1.png" alt="Tecroot Logo" class="logo-img me-2" style="height: 30px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.html"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Sign In</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="customerProfile.php"><i class="fas fa-user me-1"></i> Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i> Cart 
                            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="badge bg-danger"><?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                
                <!-- Search Bar -->
                <div class="search-container">
                    <form class="search-form" method="GET" action="products.php">
                        <input class="form-control search-input" type="search" name="search" placeholder="Search products..." 
                               value="<?= htmlspecialchars($search_query) ?>" aria-label="Search">
                        <button class="btn btn-outline-success search-btn" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1 class="page-header">Gear Up with the Best!</h1>
            
            <?php if (!empty($search_query)): ?>
                <div class="text-center mb-4">
                    <p>Showing results for: <strong><?= htmlspecialchars($search_query) ?></strong></p>
                    <a href="products.php" class="clear-search"><i class="fas fa-times me-1"></i>Clear search</a>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <?php
                    // Modified query to include search functionality
                    $sql = "SELECT a.*, i.quantity AS stock_quantity 
                            FROM advertisement a
                            LEFT JOIN inventory i ON a.Product_Name = i.item_name
                            WHERE a.Category IN ('merchandise', 'video-games', 'accessories', 'collectibles')";
                    
                    if (!empty($search_query)) {
                        $sql .= " AND a.Product_Name LIKE '%$search_query%'";
                    }
                    
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Determine stock status
                            $stock_quantity = $row['stock_quantity'] ?? 0;
                            $stock_status = ($stock_quantity > 0) ? 
                                "<span class='in-stock'>In Stock: $stock_quantity</span>" : 
                                "<span class='out-of-stock'>Out of Stock</span>";
                ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($row['Image_Path']) ?>" class="product-img" alt="<?= htmlspecialchars($row['Product_Name']) ?>">
                        <div class="product-body">
                            <h3 class="product-title"><?= htmlspecialchars($row['Product_Name']) ?></h3>
                            <p class="product-price">LKR <?= number_format($row['Price'], 2) ?></p>
                            <div class="stock-info"><?= $stock_status ?></div>
                            
                            <form method="post" action="cartHandler.php">
                                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['Product_Name']) ?>">
                                <input type="hidden" name="price" value="<?= $row['Price'] ?>">
                                <input type="hidden" name="image_path" value="<?= htmlspecialchars($row['Image_Path']) ?>">
                                <input type="hidden" name="brand" value="<?= htmlspecialchars($row['Brand'] ?? 'Tecroot') ?>">
                                <input type="hidden" name="redirect" value="products.php">
                                
                                <div class="qty-control">
                                    <button type="button" class="qty-btn minus">-</button>
                                    <input type="number" name="quantity" value="1" min="1" max="<?= $stock_quantity ?>" class="qty-input" <?= ($stock_quantity < 1) ? 'disabled' : '' ?>>
                                    <button type="button" class="qty-btn plus">+</button>
                                </div>
                                
                                <button type="submit" class="btn btn-gamer" <?= ($stock_quantity < 1) ? 'disabled' : '' ?>>
                                    <i class="fas fa-cart-plus me-2"></i> ADD TO CART
                                </button>
                                <a href="cart.php" class="btn btn-gamer btn-view-cart">
                                    <i class="fas fa-eye me-2"></i> VIEW CART
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center">
                                <div class="alert alert-dark">
                                    <i class="fas fa-exclamation-circle me-2"></i> No products found' . 
                                    (!empty($search_query) ? ' matching your search.' : ' in this category') . '
                                </div>
                              </div>';
                    }
                    
                    mysqli_close($conn);
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="gamer-footer">
        <p class="mb-0">© <?= date('Y') ?> TECROOT - ALL RIGHTS RESERVED</p>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Quantity controls
        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.qty-input');
                if(this.classList.contains('minus') && input.value > 1) {
                    input.value--;
                } else if(this.classList.contains('plus')) {
                    if(input.disabled) return;
                    const max = parseInt(input.getAttribute('max')) || Infinity;
                    if(parseInt(input.value) < max) {
                        input.value++;
                    }
                }
            });
        });

        // Add animation to product cards when they come into view
        const productCards = document.querySelectorAll('.product-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        productCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>