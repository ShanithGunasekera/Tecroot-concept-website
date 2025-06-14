<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "tecroot");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get order statistics
$stats = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue,
        AVG(total_amount) as avg_order_value,
        MIN(created_at) as oldest_order,
        MAX(created_at) as newest_order
    FROM orders"
));

// Get orders by status
$status_result = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$status_counts = [];
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_counts[$row['status']] = $row['count'];
}

// Get recent orders
$recent_orders = [];
$recent_result = mysqli_query($conn, 
    "SELECT order_id, first_name, last_name, total_amount, status, created_at 
     FROM orders ORDER BY created_at DESC LIMIT 10"
);
while ($row = mysqli_fetch_assoc($recent_result)) {
    $recent_orders[] = $row;
}

// Close connection
mysqli_close($conn);

// Generate HTML report
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tecroot Order Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #32CD32; text-align: center; }
        h2 { color: #32CD32; border-bottom: 1px solid #32CD32; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #121212; color: #32CD32; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .stat { margin-bottom: 15px; }
        .logo { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="logo">
        <img src="1.png" alt="Tecroot Logo" style="height: 50px;">
    </div>
    <h1>Tecroot Order Management Report</h1>
    <p style="text-align: center;">Generated on: <?php echo date('F j, Y'); ?></p>
    
    <h2>Order Statistics</h2>
    <div class="stat">
        <p><strong>Total Orders:</strong> <?php echo $stats['total_orders']; ?></p>
        <p><strong>Total Revenue:</strong> $<?php echo number_format($stats['total_revenue'], 2); ?></p>
        <p><strong>Average Order Value:</strong> $<?php echo number_format($stats['avg_order_value'], 2); ?></p>
        <p><strong>Date Range:</strong> <?php echo date('M j, Y', strtotime($stats['oldest_order'])); ?> to <?php echo date('M j, Y', strtotime($stats['newest_order'])); ?></p>
    </div>
    
    <h2>Order Status Breakdown</h2>
    <table>
        <tr>
            <th>Status</th>
            <th>Count</th>
        </tr>
        <?php foreach ($status_counts as $status => $count): ?>
        <tr>
            <td><?php echo ucfirst($status); ?></td>
            <td><?php echo $count; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Recent Orders (Last 10)</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php foreach ($recent_orders as $order): ?>
        <tr>
            <td>#<?php echo $order['order_id']; ?></td>
            <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
            <td><?php echo ucfirst($order['status']); ?></td>
            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
<?php
$html = ob_get_clean();

// Output as PDF using native PHP (requires dompdf or similar)
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Tecroot_Order_Report_'.date('Y-m-d').'.pdf"');

// If you have dompdf installed (common in XAMPP)
if (class_exists('Dompdf\Dompdf')) {
    require_once 'dompdf/autoload.inc.php';
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    echo $dompdf->output();
} 
// Fallback to HTML download if no PDF library available
else {
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="Tecroot_Order_Report_'.date('Y-m-d').'.html"');
    echo $html;
}
?>