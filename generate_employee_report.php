<?php
require('fpdf.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tecroot";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee data
$sql = "SELECT name, position, salary FROM employees";
$result = $conn->query($sql);

class PDF extends FPDF {
    function Header() {
        // Tecroot Logo (make sure '1.png' exists in your directory)
        $this->Image('1.png', 10, 10, 20);
        // Title
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'TECROOT EMPLOYEE SALARY REPORT', 0, 1, 'C');
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

    function EmployeeSummary($data) {
        $this->SetFont('Arial','B',14);
        $this->Cell(0, 10, 'SALARY SUMMARY', 0, 1);
        $this->Ln(5);
        
        $this->SetFont('Arial','',12);
        $totalEmployees = count($data);
        $totalSalary = 0;
        
        foreach ($data as $row) {
            $totalSalary += $row['salary'];
        }
        
        $averageSalary = $totalSalary / max(1, $totalEmployees);
        
        $this->Cell(0, 10, 'Total Employees: ' . $totalEmployees, 0, 1);
        $this->Cell(0, 10, 'Total Salary: LKR ' . number_format($totalSalary, 2), 0, 1);
        $this->Cell(0, 10, 'Average Salary: LKR ' . number_format($averageSalary, 2), 0, 1);
        $this->Ln(10);
    }

    function EmployeeTable($data) {
        $this->SetFont('Arial','B',14);
        $this->Cell(0, 10, 'COMPLETE EMPLOYEE LIST', 0, 1);
        $this->Ln(5);
        
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(220, 220, 220); // Light gray background for headers
        $this->SetTextColor(0);
        
        // Table headers - matching order report width
        $this->Cell(30, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(60, 10, 'Name', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Position', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Salary (LKR)', 1, 1, 'C', true);
        
        $this->SetFont('Arial','',12);
        $id = 1;
        $totalSalary = 0;
        
        foreach ($data as $row) {
            $this->Cell(30, 10, $id, 1);
            $this->Cell(60, 10, $row['name'], 1);
            $this->Cell(50, 10, $row['position'], 1);
            $this->Cell(50, 10, number_format($row['salary'], 2), 1, 1);
            $totalSalary += $row['salary'];
            $id++;
        }
        
        $this->Ln(5);
    }

    function SalaryChart($chartImage) {
        $this->SetFont('Arial','B',14);
        $this->Cell(0, 10, 'SALARY DISTRIBUTION', 0, 1);
        $this->Ln(5);
        
        // Add the chart image
        $this->Image($chartImage, 10, $this->GetY(), 190, 100);
        $this->Ln(110); // Move below the chart
    }
}

// Generate chart (bar chart) using GD
$chartImage = 'chart.png';
$width = 800; // Larger size for better quality
$height = 400;
$chart = imagecreatetruecolor($width, $height);

// Colors matching order report style
$bgColor = imagecolorallocate($chart, 255, 255, 255); // White background
$barColor = imagecolorallocate($chart, 0, 100, 0); // Dark green bars
$textColor = imagecolorallocate($chart, 0, 0, 0); // Black text
$lineColor = imagecolorallocate($chart, 200, 200, 200); // Light gray for grid lines

imagefill($chart, 0, 0, $bgColor);

// Fetch employee data for the chart
$employeeNames = [];
$employeeSalaries = [];
$result->data_seek(0); // Reset result pointer
while ($row = $result->fetch_assoc()) {
    $employeeNames[] = $row['name'];
    $employeeSalaries[] = $row['salary'];
}

// Determine the maximum salary for scaling
$maxSalary = max($employeeSalaries);
$scale = ($height - 100) / $maxSalary; // Scale to fit in chart height

// Draw grid lines
for ($i = 0; $i <= 10; $i++) {
    $y = $height - 80 - ($i * ($height - 100) / 10);
    imageline($chart, 100, $y, $width - 50, $y, $lineColor);
    
    // Add Y-axis labels
    $salaryValue = $i * ($maxSalary / 10);
    imagestring($chart, 3, 30, $y - 8, "LKR " . number_format($salaryValue, 0), $textColor);
}

// Draw axis lines
imageline($chart, 100, $height - 80, $width - 50, $height - 80, $textColor); // X axis
imageline($chart, 100, 30, 100, $height - 80, $textColor); // Y axis

// Draw bars
$barWidth = 40;
$spaceBetweenBars = 30;
for ($i = 0; $i < count($employeeSalaries); $i++) {
    $x1 = 120 + ($i * ($barWidth + $spaceBetweenBars));
    $y1 = $height - 80;
    $x2 = $x1 + $barWidth;
    $y2 = $y1 - ($employeeSalaries[$i] * $scale);
    
    // Draw the bar
    imagefilledrectangle($chart, $x1, $y1, $x2, $y2, $barColor);
    
    // Add employee name below each bar (rotated text)
    $text = $employeeNames[$i];
    $textWidth = imagefontwidth(3) * strlen($text);
    $textX = $x1 + ($barWidth - $textWidth) / 2;
    imagestringup($chart, 3, $textX, $height - 30, $text, $textColor);
    
    // Add salary value above each bar
    $salaryText = "LKR " . number_format($employeeSalaries[$i], 0);
    $textWidth = imagefontwidth(3) * strlen($salaryText);
    $textX = $x1 + ($barWidth - $textWidth) / 2;
    imagestring($chart, 3, $textX, $y2 - 15, $salaryText, $textColor);
}

// Save the chart image
imagepng($chart, $chartImage);
imagedestroy($chart);

// Generate the PDF
$pdf = new PDF();
$pdf->AliasNbPages();

// Page 1: Summary
$pdf->AddPage();
$employees = [];
$result->data_seek(0); // Reset result pointer
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}
$pdf->EmployeeSummary($employees);

// Page 2: Employee List
$pdf->AddPage();
$pdf->EmployeeTable($employees);

// Page 3: Chart
$pdf->AddPage();
$pdf->SalaryChart($chartImage);

$pdf->Output('D', 'Tecroot_Employee_Salary_Report_' . date('Y-m-d') . '.pdf');

$conn->close();

// Clean up chart image
unlink($chartImage);
?>