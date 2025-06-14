<?php
require('fpdf.php');

session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tecroot";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if employee is logged in
if (!isset($_SESSION['email']) || !str_starts_with($_SESSION['email'], 'emp')) {
    header("Location: login.php");
    exit();
}

// Fetch all user data
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

class PDF extends FPDF {
    function Header() {
        // Tecroot Logo
        $this->Image('1.png', 10, 10, 20);
        // Title
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'TECROOT USER PROFILE REPORT', 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Generated on: ' . date("Y-m-d"), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function UserSummary($data) {
        $this->SetFont('Arial','B',14);
        $this->Cell(0, 10, 'USER SUMMARY', 0, 1);
        $this->Ln(5);
        
        $this->SetFont('Arial','',12);
        $totalUsers = count($data);
        $totalEmployees = 0;
        $totalCustomers = 0;
        $newUsersThisMonth = 0;
        $currentMonth = date('m');
        
        foreach ($data as $row) {
            if (str_starts_with($row['email'], 'emp')) {
                $totalEmployees++;
            } else {
                $totalCustomers++;
            }
            
            if (date('m', strtotime($row['created_at'])) == $currentMonth) {
                $newUsersThisMonth++;
            }
        }
        
        $this->Cell(0, 10, 'Total Users: ' . $totalUsers, 0, 1);
        $this->Cell(0, 10, 'Total Employees: ' . $totalEmployees, 0, 1);
        $this->Cell(0, 10, 'Total Customers: ' . $totalCustomers, 0, 1);
        $this->Cell(0, 10, 'New Users This Month: ' . $newUsersThisMonth, 0, 1);
        $this->Ln(10);
    }

    function UserTable($data) {
        $this->SetFont('Arial','B',14);
        $this->Cell(0, 10, 'COMPLETE USER LIST', 0, 1);
        $this->Ln(5);
        
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(0, 100, 0); // Dark green background for headers
        $this->SetTextColor(255);
        
        // Table headers
        $this->Cell(15, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Name', 1, 0, 'C', true);
        $this->Cell(60, 10, 'Email', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Type', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Join Date', 1, 1, 'C', true);
        
        $this->SetFont('Arial','',12);
        $this->SetTextColor(0);
        
        foreach ($data as $row) {
            $userType = str_starts_with($row['email'], 'emp') ? 'Employee' : 'Customer';
            $joinDate = date('Y-m-d', strtotime($row['created_at']));
            
            $this->Cell(15, 10, $row['id'], 1);
            $this->Cell(50, 10, $row['full_name'], 1);
            $this->Cell(60, 10, $row['email'], 1);
            $this->Cell(25, 10, $userType, 1);
            $this->Cell(30, 10, $joinDate, 1, 1);
        }
        
        $this->Ln(5);
    }

    function UserChart($chartImage) {
        $this->SetFont('Arial','B',14);
        $this->Cell(0, 10, 'USER REGISTRATION TRENDS', 0, 1);
        $this->Ln(5);
        
        // Add the chart image
        $this->Image($chartImage, 10, $this->GetY(), 190, 100);
        $this->Ln(110); // Move below the chart
    }
}

// Generate chart (line chart) using GD
$chartImage = 'user_chart.png';
$width = 800;
$height = 400;
$chart = imagecreatetruecolor($width, $height);

// Colors matching Tecroot style
$bgColor = imagecolorallocate($chart, 18, 18, 18); // Dark background
$lineColor = imagecolorallocate($chart, 0, 255, 0); // Green line
$textColor = imagecolorallocate($chart, 255, 255, 255); // White text
$gridColor = imagecolorallocate($chart, 50, 50, 50); // Dark grid lines

imagefill($chart, 0, 0, $bgColor);

// Get registration data by month
$registrationData = [];
$result->data_seek(0); // Reset result pointer
while ($row = $result->fetch_assoc()) {
    $month = date('Y-m', strtotime($row['created_at']));
    if (!isset($registrationData[$month])) {
        $registrationData[$month] = 0;
    }
    $registrationData[$month]++;
}

// Sort by date
ksort($registrationData);

// Prepare chart data
$months = array_keys($registrationData);
$counts = array_values($registrationData);
$maxCount = max($counts);
$scale = ($height - 100) / max(1, $maxCount);

// Draw grid lines
for ($i = 0; $i <= 10; $i++) {
    $y = $height - 80 - ($i * ($height - 100) / 10);
    imageline($chart, 100, $y, $width - 50, $y, $gridColor);
    
    // Add Y-axis labels
    $value = $i * ($maxCount / 10);
    imagestring($chart, 3, 30, $y - 8, number_format($value, 0), $textColor);
}

// Draw axis lines
imageline($chart, 100, $height - 80, $width - 50, $height - 80, $textColor); // X axis
imageline($chart, 100, 30, 100, $height - 80, $textColor); // Y axis

// Draw line chart
$pointRadius = 5;
$prevX = $prevY = null;
$monthCount = count($months);
$spacePerMonth = ($width - 150) / max(1, $monthCount - 1);

for ($i = 0; $i < $monthCount; $i++) {
    $x = 120 + ($i * $spacePerMonth);
    $y = $height - 80 - ($counts[$i] * $scale);
    
    // Draw point
    imagefilledellipse($chart, $x, $y, $pointRadius * 2, $pointRadius * 2, $lineColor);
    
    // Draw line to previous point
    if ($prevX !== null) {
        imageline($chart, $prevX, $prevY, $x, $y, $lineColor);
    }
    
    // Add month label
    imagestring($chart, 3, $x - 15, $height - 60, substr($months[$i], 5), $textColor);
    
    $prevX = $x;
    $prevY = $y;
}

// Save the chart image
imagepng($chart, $chartImage);
imagedestroy($chart);

// Generate the PDF
$pdf = new PDF();
$pdf->AliasNbPages();

// Page 1: Summary
$pdf->AddPage();
$users = [];
$result->data_seek(0); // Reset result pointer
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$pdf->UserSummary($users);

// Page 2: User List
$pdf->AddPage();
$pdf->UserTable($users);

// Page 3: Chart
$pdf->AddPage();
$pdf->UserChart($chartImage);

$pdf->Output('D', 'Tecroot_User_Report_' . date('Y-m-d') . '.pdf');

$conn->close();

// Clean up chart image
unlink($chartImage);
?>