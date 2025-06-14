<?php
session_start();

$fpdfPath = __DIR__ . '/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    die("Error: FPDF library not found.");
}
require($fpdfPath);

$conn = mysqli_connect("localhost", "root", "", "tecroot") or die("Connection failed");

$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total_orders, SUM(total_amount) as total_revenue, 
    AVG(total_amount) as avg_order_value, MIN(created_at) as oldest_order, MAX(created_at) as newest_order FROM orders"));

$statusResult = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM orders GROUP BY status");
while ($row = mysqli_fetch_assoc($statusResult)) $status_counts[$row['status']] = $row['count'];

$all_orders = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC"), MYSQLI_ASSOC);

class PDF extends FPDF {
    private $primaryColor = [41, 128, 185];
    
    function Header() {
        if ($this->PageNo() == 3) return; // Skip header on page 3
        
        $this->Image(__DIR__.'/1.png', 15, 10, 25);
        $this->SetFont('Arial','B',18);
        $this->SetTextColor(51, 51, 51);
        $this->Cell(0,15,'TECROOT ORDER REPORT',0,1,'C');
        $this->SetDrawColor(200, 200, 200);
        $this->Line(15, 30, 195, 30);
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    
    function DrawStatsBox() {
        global $stats;
        
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'ORDER SUMMARY',0,1,'C');
        $this->Ln(8);
        
        $labelWidth = 70;
        $valueWidth = 60;
        
        $this->SetFont('Arial','',12);
        $this->Cell($labelWidth,8,'Total Orders:',0,0,'R');
        $this->Cell($valueWidth,8,$stats['total_orders'],0,1,'L');
        
        $this->Cell($labelWidth,8,'Total Revenue:',0,0,'R');
        $this->Cell($valueWidth,8,'$'.number_format($stats['total_revenue'],2),0,1,'L');
        
        $this->Cell($labelWidth,8,'Average Order:',0,0,'R');
        $this->Cell($valueWidth,8,'$'.number_format($stats['avg_order_value'],2),0,1,'L');
        
        $this->Cell($labelWidth,8,'Date Range:',0,0,'R');
        $this->Cell($valueWidth,8,date('M j, Y',strtotime($stats['oldest_order'])).' to '.date('M j, Y',strtotime($stats['newest_order'])),0,1,'L');
        
        $this->Ln(15);
    }
    
    function DrawOrderTable() {
        global $all_orders;
        
        $this->AddPage();
        
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'COMPLETE ORDER LIST',0,1,'C');
        $this->Ln(8);
        
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(52, 152, 219);
        $this->SetTextColor(255);
        $this->Cell(15,8,'ID',1,0,'C',true);
        $this->Cell(45,8,'CUSTOMER',1,0,'C',true);
        $this->Cell(50,8,'EMAIL',1,0,'C',true);
        $this->Cell(30,8,'AMOUNT',1,0,'C',true);
        $this->Cell(25,8,'STATUS',1,0,'C',true);
        $this->Cell(25,8,'DATE',1,1,'C',true);
        
        $this->SetFont('Arial','',9);
        $this->SetTextColor(0);
        $fill = false;
        
        foreach ($all_orders as $order) {
            $this->SetFillColor($fill ? 240 : 255);
            
            $customer = strlen($order['first_name'].' '.$order['last_name']) > 20 ? 
                substr($order['first_name'].' '.$order['last_name'], 0, 17).'...' : 
                $order['first_name'].' '.$order['last_name'];
                
            $email = strlen($order['email']) > 25 ? 
                substr($order['email'], 0, 22).'...' : 
                $order['email'];
            
            $this->Cell(15,7,$order['order_id'],1,0,'C',$fill);
            $this->Cell(45,7,$customer,1,0,'L',$fill);
            $this->Cell(50,7,$email,1,0,'L',$fill);
            $this->Cell(30,7,'$'.number_format($order['total_amount'],2),1,0,'R',$fill);
            
            $statusColor = [
                'pending' => [241, 196, 15],
                'completed' => [46, 204, 113],
                'cancelled' => [231, 76, 60]
            ][strtolower($order['status'])] ?? [200, 200, 200];
            
            $this->SetFillColor($statusColor[0], $statusColor[1], $statusColor[2]);
            $this->Cell(25,7,ucfirst($order['status']),1,0,'C',true);
            
            $this->SetFillColor($fill ? 240 : 255);
            $this->Cell(25,7,date('m/d/Y',strtotime($order['created_at'])),1,1,'C',$fill);
            $fill = !$fill;
        }
    }

    function DrawBarChart() {
        global $status_counts;
        
        $this->AddPage();  // This is page 3
        
        $this->Ln(25); // Push the chart downward

        // Chart dimensions and position
        $chartWidth = 120;
        $chartHeight = 90;
        $chartX = (210 - $chartWidth) / 2;
        $chartY = 100;

        $maxValue = max($status_counts);
        $barCount = count($status_counts);
        $barWidth = $chartWidth / $barCount;
        $barSpacing = 5;

        $colors = [
            [241, 196, 15],
            [46, 204, 113],
            [231, 76, 60]
        ];
        
        // Axes
        $this->SetDrawColor(150);
        $this->Line($chartX, $chartY, $chartX + $chartWidth, $chartY);
        $this->Line($chartX, $chartY, $chartX, $chartY - $chartHeight);
        
        // Bars
        $i = 0;
        foreach ($status_counts as $status => $count) {
            $barHeight = ($count / $maxValue) * $chartHeight;
            $barX = $chartX + ($i * $barWidth) + $barSpacing/2;
            $barActualWidth = $barWidth - $barSpacing;
            
            $this->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
            $this->Rect($barX, $chartY - $barHeight, $barActualWidth, $barHeight, 'F');
            
            // Value above bar
            $this->SetFont('Arial','B',10);
            $this->SetXY($barX, $chartY - $barHeight - 6);
            $this->Cell($barActualWidth, 5, $count, 0, 0, 'C');
            
            // Label below bar
            $this->SetFont('Arial','',10);
            $this->SetXY($barX, $chartY + 4);
            $this->Cell($barActualWidth, 5, strtoupper($status), 0, 0, 'C');
            
            $i++;
        }
        
        // Y-axis labels
        $this->SetFont('Arial','',9);
        $this->SetXY($chartX - 15, $chartY - $chartHeight - 3);
        $this->Cell(10, 5, $maxValue, 0, 0, 'R');
        $this->SetXY($chartX - 15, $chartY - ($chartHeight/2) - 3);
        $this->Cell(10, 5, round($maxValue/2), 0, 0, 'R');

        // Status breakdown
        $this->SetY($chartY + 30);
        $this->SetFont('Arial','B',12);
        $this->Cell(0,8,'STATUS BREAKDOWN:',0,1,'C');

        $total = array_sum($status_counts);
        $this->SetFont('Arial','',10);
        foreach ($status_counts as $status => $count) {
            $percentage = ($count / $total) * 100;
            $this->Cell(0, 6, strtoupper($status) . ": " . $count . " orders (" . number_format($percentage, 1) . "%)", 0, 1, 'C');
        }
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->DrawStatsBox();
$pdf->DrawOrderTable();
$pdf->DrawBarChart();
$pdf->Output('D','Tecroot_Order_Report_'.date('Y-m-d').'.pdf');
mysqli_close($conn);
