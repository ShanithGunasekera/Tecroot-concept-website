<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Handle PDF generation if requested
if (isset($_GET['generate_pdf'])) {
    // Clear any existing output buffers
    while (ob_get_level()) ob_end_clean();

    // Load FPDF library
    $fpdfPath = __DIR__ . '/fpdf/fpdf.php';
    if (!file_exists($fpdfPath)) {
        die("Error: FPDF library not found at: $fpdfPath");
    }
    require_once($fpdfPath);

    // Database connection with error handling
    $conn = mysqli_connect("localhost", "root", "", "tecroot", "3306");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get advertisement statistics with proper null checks
    $statsQuery = mysqli_query($conn, 
        "SELECT COUNT(*) as total_ads, 
                AVG(Price) as avg_price, 
                MIN(IFNULL(created_at, NOW())) as oldest_ad, 
                MAX(IFNULL(created_at, NOW())) as newest_ad 
         FROM advertisement 
         WHERE Category IN ('merchandise', 'video-games', 'accessories', 'collectibles')");

    if (!$statsQuery) {
        die("Query failed: " . mysqli_error($conn));
    }

    $stats = mysqli_fetch_assoc($statsQuery);

    // Get category distribution
    $categoryResult = mysqli_query($conn, 
        "SELECT Category, COUNT(*) as count 
         FROM advertisement 
         GROUP BY Category");
    $category_counts = [];
    while ($row = mysqli_fetch_assoc($categoryResult)) {
        $category_counts[$row['Category']] = $row['count'];
    }

    // Get all advertisements with proper date handling
    $adsQuery = mysqli_query($conn, 
        "SELECT id, Product_Name, Category, Price, 
                IFNULL(created_at, NOW()) as created_at 
         FROM advertisement 
         WHERE Category IN ('merchandise', 'video-games', 'accessories', 'collectibles')
         ORDER BY created_at DESC");

    if (!$adsQuery) {
        die("Query failed: " . mysqli_error($conn));
    }

    $all_ads = mysqli_fetch_all($adsQuery, MYSQLI_ASSOC);

    class AdvertisementPDF extends FPDF {
        private $primaryColor = [50, 205, 50]; // Gamer green
        
        function Header() {
            if ($this->PageNo() == 3) return; // Skip header on chart page
            
            // Logo
            if (file_exists(__DIR__.'/1.png')) {
                $this->Image(__DIR__.'/1.png', 15, 10, 25);
            }
            
            // Title
            $this->SetFont('Arial','B',18);
            $this->SetTextColor(50, 205, 50);
            $this->Cell(0,15,'Tecroot Advertisements',0,1,'C');
            
            // Line separator
            $this->SetDrawColor(50, 205, 50);
            $this->Line(15, 30, 195, 30);
            $this->Ln(10);
        }
        
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->SetTextColor(100);
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        }
        
        function DrawStatsBox() {
            global $stats;
            
            $this->SetFont('Arial','B',14);
            $this->SetTextColor(50, 205, 50);
            $this->Cell(0,10,'ADVERTISEMENT SUMMARY',0,1,'C');
            $this->Ln(8);
            
            $this->SetFont('Arial','',12);
            $this->SetTextColor(0);
            
            $labelWidth = 70;
            $valueWidth = 60;
            
            $this->Cell($labelWidth,8,'Total Advertisements:',0,0,'R');
            $this->Cell($valueWidth,8,$stats['total_ads'] ?? 0,0,1,'L');
            
            $this->Cell($labelWidth,8,'Average Price:',0,0,'R');
            $this->Cell($valueWidth,8,'LKR '.number_format($stats['avg_price'] ?? 0, 2),0,1,'L');
            
            $this->Cell($labelWidth,8,'Date Range:',0,0,'R');
            $dateRange = (isset($stats['oldest_ad']) && isset($stats['newest_ad'])) 
                ? date('M j, Y', strtotime($stats['oldest_ad'])).' to '.date('M j, Y', strtotime($stats['newest_ad']))
                : 'N/A';
            $this->Cell($valueWidth,8,$dateRange,0,1,'L');
            
            $this->Ln(15);
        }
        
        function DrawAdsTable() {
            global $all_ads;
            
            $this->AddPage();
            
            $this->SetFont('Arial','B',14);
            $this->SetTextColor(50, 205, 50);
            $this->Cell(0,10,'ALL ADVERTISEMENTS',0,1,'C');
            $this->Ln(8);
            
            // Table header
            $this->SetFont('Arial','B',10);
            $this->SetFillColor(50, 205, 50);
            $this->SetTextColor(255);
            $this->Cell(20,8,'ID',1,0,'C',true);
            $this->Cell(60,8,'PRODUCT NAME',1,0,'C',true);
            $this->Cell(40,8,'CATEGORY',1,0,'C',true);
            $this->Cell(30,8,'PRICE (LKR)',1,0,'C',true);
            $this->Cell(40,8,'DATE ADDED',1,1,'C',true);
            
            // Table data
            $this->SetFont('Arial','',9);
            $this->SetTextColor(0);
            $fill = false;
            
            foreach ($all_ads as $ad) {
                $this->SetFillColor($fill ? 240 : 255);
                
                $productName = strlen($ad['Product_Name']) > 30 ? 
                    substr($ad['Product_Name'], 0, 27).'...' : 
                    $ad['Product_Name'];
                
                $this->Cell(20,7,$ad['id'] ?? '',1,0,'C',$fill);
                $this->Cell(60,7,$productName,1,0,'L',$fill);
                $this->Cell(40,7,ucfirst($ad['Category'] ?? ''),1,0,'C',$fill);
                $this->Cell(30,7,number_format($ad['Price'] ?? 0,2),1,0,'R',$fill);
                $this->Cell(40,7,date('m/d/Y', strtotime($ad['created_at'] ?? 'now')),1,1,'C',$fill);
                
                $fill = !$fill;
            }
        }

        function DrawCategoryChart() {
            global $category_counts;
            
            if (empty($category_counts)) return;
            
            $this->AddPage();
            $this->Ln(25);

            $chartWidth = 120;
            $chartHeight = 90;
            $chartX = (210 - $chartWidth) / 2;
            $chartY = 100;
            $maxValue = max($category_counts);
            $barCount = count($category_counts);
            $barWidth = $chartWidth / $barCount;
            $barSpacing = 5;

            $colors = [
                [52, 152, 219], [155, 89, 182], 
                [26, 188, 156], [241, 196, 15]
            ];
            
            // Axes
            $this->SetDrawColor(150);
            $this->Line($chartX, $chartY, $chartX + $chartWidth, $chartY);
            $this->Line($chartX, $chartY, $chartX, $chartY - $chartHeight);
            
            // Bars
            $i = 0;
            foreach ($category_counts as $category => $count) {
                $barHeight = ($count / $maxValue) * $chartHeight;
                $barX = $chartX + ($i * $barWidth) + $barSpacing/2;
                $barActualWidth = $barWidth - $barSpacing;
                
                $this->SetFillColor(...$colors[$i % count($colors)]);
                $this->Rect($barX, $chartY - $barHeight, $barActualWidth, $barHeight, 'F');
                
                $this->SetFont('Arial','B',10);
                $this->SetXY($barX, $chartY - $barHeight - 6);
                $this->Cell($barActualWidth, 5, $count, 0, 0, 'C');
                
                $this->SetFont('Arial','',10);
                $this->SetXY($barX, $chartY + 4);
                $this->Cell($barActualWidth, 5, strtoupper($category), 0, 0, 'C');
                
                $i++;
            }
            
            $this->SetY($chartY + 30);
            $this->SetFont('Arial','B',12);
            $this->SetTextColor(50, 205, 50);
            $this->Cell(0,8,'ADVERTISEMENTS BY CATEGORY',0,1,'C');

            $total = array_sum($category_counts);
            $this->SetFont('Arial','',10);
            $this->SetTextColor(0);
            foreach ($category_counts as $category => $count) {
                $percentage = ($count / $total) * 100;
                $this->Cell(0, 6, strtoupper($category) . ": " . $count . " ads (" . number_format($percentage, 1) . "%)", 0, 1, 'C');
            }
        }
    }

    // Generate the PDF
    try {
        $pdf = new AdvertisementPDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->DrawStatsBox();
        $pdf->DrawAdsTable();
        $pdf->DrawCategoryChart();
        $pdf->Output('D','Tecroot_Adds_Report'.date('Y-m-d').'.pdf');
        exit();
    } catch (Exception $e) {
        die("PDF Generation Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>View My Advertisements</title>
    <!-- Favicon -->
    <link rel="icon" href="2.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gamer-dark: #0d0d0d;
            --gamer-green: #32CD32;
            --gamer-light: #1a1a1a;
        }
        
        body {
            font-family: 'Anta', sans-serif;
            background-color: var(--gamer-dark);
            color: white;
            margin: 0;
            padding: 0;
        }
        
        .navbar-custom {
            background-color: var(--gamer-dark) !important;
            border-bottom: 2px solid var(--gamer-green);
        }
        
        .navbar-brand, .nav-link {
            color: var(--gamer-green) !important;
        }
        
        .nav-link:hover {
            color: #1e90ff !important;
        }
        
        .product-card {
            background-color: var(--gamer-light);
            border: 1px solid var(--gamer-green);
            border-radius: 5px;
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(50, 205, 50, 0.3);
        }
        
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid var(--gamer-green);
        }
        
        .product-title {
            color: var(--gamer-green);
            font-size: 1.2rem;
        }
        
        .btn-edit {
            background-color: var(--gamer-green);
            color: black;
        }
        
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-report {
            background-color: #6c757d;
            color: white;
            margin-bottom: 20px;
        }
        
        .btn-edit:hover {
            background-color: #228B22;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
        }
        
        .btn-report:hover {
            background-color: #5a6268;
        }
        
        .page-header {
            color: var(--gamer-green);
            text-align: center;
            margin: 30px 0;
            position: relative;
        }
        
        .page-header:after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background: var(--gamer-green);
            margin: 15px auto;
        }
        
        .report-section {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
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
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Sign In</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="account.php"><i class="fas fa-user me-1"></i> Add control</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i> Cart</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="page-header">ADVERTISEMENTS</h1>
        
        <!-- PDF Report Button -->
        <div class="report-section">
            <a href="?generate_pdf=1" class="btn btn-report btn-lg">
                <i class="fas fa-file-pdf me-2"></i> Download PDF Report
            </a>
        </div>
        
        <div class="row">
            <?php
                $conn = mysqli_connect("localhost", "root", "", "tecroot", "3306");
                if (!$conn) {
                    die("DB connection failed: " . mysqli_connect_error());
                }

                $sql = "SELECT * FROM advertisement WHERE Category IN ('merchandise', 'video-games', 'accessories', 'collectibles')";

                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="col-md-4 col-sm-6">
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($row['Image_Path']); ?>" class="product-img" alt="Product Image">
                    <div class="card-body">
                        <h5 class="product-title"><?php echo htmlspecialchars($row['Product_Name']); ?></h5>
                        <p class="card-text">Price: LKR <?php echo htmlspecialchars($row['Price']); ?></p>
                        <div class="d-grid gap-2">
                            <a href="EditAdvertisements.php?Add_ID=<?php echo $row['id']; ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="DeleteAdvertisement.php?Add_ID=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center"><p class="text-white">No advertisements found.</p></div>';
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    echo '<div class="col-12 text-center"><p class="text-white">Error preparing query: ' . mysqli_error($conn) . '</p></div>';
                }

                mysqli_close($conn);
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-center text-white py-3">
        <p class="mb-0">© <?php echo date('Y'); ?> Gamer's Store - All Rights Reserved</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>