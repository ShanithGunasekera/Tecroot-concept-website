<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Tecroot Gaming</title>
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
        
        .page-header {
            color: var(--gamer-green);
            text-align: center;
            margin: 3rem 0;
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
        
        .about-section {
            background-color: var(--gamer-light);
            border: 1px solid var(--gamer-green);
            border-radius: 5px;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .mission-card {
            background-color: rgba(0, 0, 0, 0.3);
            border-left: 4px solid var(--gamer-green);
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .team-member {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .member-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--gamer-green);
            margin-bottom: 1rem;
            box-shadow: 0 0 15px rgba(50, 205, 50, 0.5);
        }
        
        .gamer-btn {
            background-color: var(--gamer-green);
            color: #000;
            border: none;
            border-radius: 0;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s;
            text-transform: uppercase;
        }
        
        .gamer-btn:hover {
            background-color: var(--gamer-accent);
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
        }
        
        /* Stats counter */
        .stats-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 3rem 0;
        }
        
        .stat-item {
            text-align: center;
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            color: var(--gamer-green);
            font-family: 'Press Start 2P', cursive;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark gamer-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="home.html">
                <img src="1.png" alt="Tecroot Logo" class="logo-img">
                Tecroot
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.html"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php"><i class="fas fa-gamepad me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="Contactpage.html"><i class="fas fa-envelope me-1"></i> Contact us</a>
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
        <h1 class="page-header">LEVEL UP YOUR GAME WITH TECROOT</h1>
        
        <div class="about-section">
            <h2 class="text-center mb-4" style="color: var(--gamer-green);">OUR STORY</h2>
            <p class="lead">Founded in 2023, Tecroot emerged from a shared passion for gaming and cutting-edge technology. What began as a small team of competitive gamers has evolved into a premier destination for high-performance gaming gear.</p>
            
            <div class="mission-card">
                <h3><i class="fas fa-crosshairs me-2"></i> OUR MISSION</h3>
                <p>To empower gamers at all levels with equipment that enhances performance, durability, and style. We believe the right gear should disappear in your hands, leaving only pure gaming immersion.</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h3><i class="fas fa-trophy me-2"></i> ESPORTS PARTNERSHIPS</h3>
                    <p>We proudly sponsor professional esports teams competing in League of Legends, Counter-Strike 2, and Valorant tournaments worldwide. Our gear is battle-tested at the highest levels.</p>
                </div>
                <div class="col-md-6">
                    <h3><i class="fas fa-leaf me-2"></i> SUSTAINABLE GAMING</h3>
                    <p>Committed to reducing e-waste, we offer trade-in programs and use recycled materials in 65% of our products without compromising performance.</p>
                </div>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number">200K+</div>
                <div>Gamers Empowered</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">15+</div>
                <div>Pro Teams Sponsored</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div>Customer Support</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div>Performance Guarantee</div>
            </div>
        </div>
        
        <!-- Team Section -->
        <div class="about-section">
            <h2 class="text-center mb-4" style="color: var(--gamer-green);">MEET THE RAID PARTY</h2>
            <div class="row">
                <div class="col-md-4 team-member">
                    <img src="https://via.placeholder.com/150" alt="CEO" class="member-img">
                    <h3>Shafwan Mansoor</h3>
                    <p class="text-muted">Founder & CEO</p>
                    <p>"I started Tecroot because I was tired of gear failing during clutch moments."</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitch"></i></a>
                    </div>
                </div>
                <div class="col-md-4 team-member">
                    <img src="https://via.placeholder.com/150" alt="Lead Designer" class="member-img">
                    <h3>OMETH H.</h3>
                    <p class="text-muted">Lead Designer</p>
                    <p>"Every curve and LED placement is optimized for both form and function."</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-artstation"></i></a>
                    </div>
                </div>
                <div class="col-md-4 team-member">
                    <img src="https://via.placeholder.com/150" alt="Pro Gamer" class="member-img">
                    <h3>VINULI F.</h3>
                    <p class="text-muted">Pro Gamer Advisor</p>
                    <p>"I test every product in tournament conditions before approval."</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-discord"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="text-center my-5">
            <h3 class="mb-3">READY TO UPGRADE YOUR SETUP?</h3>
            <a href="products.php" class="btn btn-lg gamer-btn">
                <i class="fas fa-arrow-right me-2"></i> SHOP NOW
            </a>
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
</body>
</html>