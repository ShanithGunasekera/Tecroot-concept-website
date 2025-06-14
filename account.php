<?php
session_start();
if(!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Dashboard | Gamer's Haven</title>
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
        
        .dashboard-header {
            color: var(--gamer-green);
            text-align: center;
            margin: 3rem 0;
            text-shadow: 0 0 10px rgba(50, 205, 50, 0.5);
        }
        
        .dashboard-header:after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background: var(--gamer-green);
            margin: 15px auto;
            box-shadow: 0 0 10px var(--gamer-green);
        }
        
        .dashboard-card {
            background-color: var(--gamer-light);
            border: 1px solid var(--gamer-green);
            border-radius: 5px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
            height: 100%;
            text-align: center;
            padding: 2rem 1rem;
        }
        
        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 0, 0.2);
        }
        
        .card-icon {
            font-size: 3rem;
            color: var(--gamer-green);
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            color: var(--gamer-green);
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }
        
        .btn-gamer {
            background-color: var(--gamer-green);
            color: #000;
            border: none;
            border-radius: 0;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s;
            width: 80%;
            margin: 0 auto;
            text-transform: uppercase;
            display: block;
        }
        
        .btn-gamer:hover {
            background-color: #1e90ff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 255, 0, 0.3);
        }
        
        .gamer-footer {
            background-color: var(--gamer-dark);
            color: var(--gamer-green);
            text-align: center;
            padding: 1rem;
            border-top: 1px solid var(--gamer-green);
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.5);
            margin-top: auto;
        }
        
        @media (max-width: 768px) {
            .dashboard-card {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Gaming Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark gamer-nav">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="home.html">
            <img src="2.png" alt="Tecroot Logo" class="logo-img me-2" style="height: 30px;">
        </a>
        <h1 class="logo">Tecroot</h1>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="empIndex.html"><i class="fas fa-home me-1"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Sign In</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="CustomerProfile.php"><i class="fas fa-user me-1"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i> Cart</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Main Content -->
    <div class="container my-5">
        <h1 class="dashboard-header">ACCOUNT DASHBOARD</h1>
        
        <div class="row">
            <!-- Profile Card -->
            <div class="col-md-4 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h3 class="card-title">MY PROFILE</h3>
                    <a href="#" class="btn btn-gamer">
                        <i class="fas fa-edit me-2"></i> VIEW PROFILE
                    </a>
                </div>
            </div>
            
            <!-- Add Product Card -->
            <div class="col-md-4 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h3 class="card-title">ADD PRODUCT</h3>
                    <a href="AddAdvertisement.php" class="btn btn-gamer">
                        <i class="fas fa-plus me-2"></i> ADD NEW
                    </a>
                </div>
            </div>
            
            <!-- View Products Card -->
            <div class="col-md-4 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <h3 class="card-title">MY PRODUCTS</h3>
                    <a href="ViewMyAdvertisements.php" class="btn btn-gamer">
                        <i class="fas fa-eye me-2"></i> VIEW ALL
                    </a>
                </div>
            </div>
            
            <!-- Edit Products Card -->
            <div class="col-md-4 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3 class="card-title">EDIT PRODUCTS</h3>
                    <a href="EditAdvertisements.php" class="btn btn-gamer">
                        <i class="fas fa-pencil-alt me-2"></i> EDIT
                    </a>
                </div>
            </div>
            
            <!-- All Products Card -->
            <div class="col-md-4 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3 class="card-title">SHOP PRODUCTS</h3>
                    <a href="products.php" class="btn btn-gamer">
                        <i class="fas fa-shopping-bag me-2"></i> BROWSE
                    </a>
                </div>
            </div>
            
            <!-- Cart Card -->
            <div class="col-md-4 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="card-title">MY CART</h3>
                    <a href="cart.php" class="btn btn-gamer">
                        <i class="fas fa-cart-arrow-down me-2"></i> VIEW CART
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="gamer-footer">
        <p class="mb-0">© <?php echo date('Y'); ?> GAMER'S HAVEN - ALL RIGHTS RESERVED</p>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>