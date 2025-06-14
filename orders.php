<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = mysqli_connect("localhost", "root", "", "tecroot");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update order status
    if (isset($_POST['update_order'])) {
        $order_id = intval($_POST['order_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        $sql = "UPDATE orders SET status = '$status' WHERE order_id = $order_id";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Order #$order_id updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating order: " . mysqli_error($conn);
        }
        header("Location: orders.php");
        exit();
    }
    
    // Delete order
    if (isset($_POST['delete_order'])) {
        $order_id = intval($_POST['order_id']);
        
        // First delete order items
        $sql = "DELETE FROM order_items WHERE order_id = $order_id";
        mysqli_query($conn, $sql);
        
        // Then delete the order
        $sql = "DELETE FROM orders WHERE order_id = $order_id";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Order #$order_id deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting order: " . mysqli_error($conn);
        }
        header("Location: orders.php");
        exit();
    }
}

// Get all orders
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get order items for each order
foreach ($orders as &$order) {
    $order_id = $order['order_id'];
    $sql = "SELECT * FROM order_items WHERE order_id = $order_id";
    $result = mysqli_query($conn, $sql);
    $order['items'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
unset($order); // Break the reference
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Management | Tecroot</title>
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
        
        .gamer-card {
            background-color: var(--gamer-light);
            border: 1px solid var(--gamer-green);
            border-radius: 0;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .gamer-card-header {
            background-color: rgba(0, 0, 0, 0.5);
            border-bottom: 1px solid var(--gamer-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            padding: 15px;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .orders-table th {
            background-color: rgba(0, 0, 0, 0.7);
            color: var(--gamer-green);
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid var(--gamer-green);
        }
        
        .orders-table td {
            padding: 12px;
            border-bottom: 1px solid #333;
            vertical-align: middle;
        }
        
        .orders-table tr:hover {
            background-color: rgba(0, 255, 0, 0.05);
        }
        
        .customer-info {
            display: flex;
            flex-direction: column;
        }
        
        .customer-email {
            font-size: 0.8rem;
            color: #aaa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-processing {
            background-color: #17a2b8;
            color: #fff;
        }
        
        .status-completed {
            background-color: #28a745;
            color: #fff;
        }
        
        .status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }
        
        .action-btn {
            padding: 5px 10px;
            margin: 0 3px;
            font-size: 0.8rem;
            border-radius: 0;
        }
        
        .view-btn {
            background-color: var(--gamer-green);
            color: #000;
            border: none;
        }
        
        .edit-btn {
            background-color: #ffc107;
            color: #000;
            border: none;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* Modal styles */
        .modal-content {
            background-color: var(--gamer-light);
            border: 1px solid var(--gamer-green);
        }
        
        .modal-header {
            border-bottom: 1px solid var(--gamer-green);
        }
        
        .modal-title {
            color: var(--gamer-green);
            font-family: 'Press Start 2P', cursive;
        }
        
        .modal-footer {
            border-top: 1px solid var(--gamer-green);
        }
        
        .alert-success {
            background-color: rgba(0, 255, 0, 0.2);
            border-color: var(--gamer-green);
            color: var(--gamer-green);
        }
        
        .alert-danger {
            background-color: rgba(255, 0, 0, 0.2);
            border-color: #dc3545;
            color: #dc3545;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .orders-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark gamer-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="empIndex.html">
                <img src="2.png" alt="Tecroot Logo" class="logo-img me-2" style="height: 30px;">
                Tecroot
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
                        <a class="nav-link" href="inventory.php"><i class="fas fa-boxes me-1"></i> Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php"><i class="fas fa-shopping-cart me-1"></i> Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="account.php"><i class="fas fa-user me-1"></i> Account</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="gamer-card">
            <div class="gamer-card-header">
                <i class="fas fa-shopping-cart me-2"></i> ORDER MANAGEMENT
            </div>
            <div class="card-body">
                <?php if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td>
                                            <div class="customer-info">
                                                <strong><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong>
                                                <span class="customer-email"><?php echo htmlspecialchars($order['email']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                        <td><?php echo $order['total_items']; ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm action-btn view-btn" data-bs-toggle="modal" data-bs-target="#viewOrderModal" 
                                                    data-orderid="<?php echo $order['order_id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm action-btn edit-btn" data-bs-toggle="modal" data-bs-target="#editOrderModal" 
                                                    data-orderid="<?php echo $order['order_id']; ?>"
                                                    data-status="<?php echo htmlspecialchars($order['status']); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm action-btn delete-btn" data-bs-toggle="modal" data-bs-target="#deleteOrderModal" 
                                                    data-orderid="<?php echo $order['order_id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x mb-3" style="color: #333;"></i>
                        <p>No orders found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewOrderModalLabel">Order Details - #<span id="viewOrderId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewOrderDetails">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="orders.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrderModalLabel">Update Order #<span id="editOrderId"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="edit_order_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" id="edit_order_status" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_order" class="btn btn-success">Update Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Order Modal -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="orders.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteOrderModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="delete_order_id">
                        <p>Are you sure you want to delete this order? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_order" class="btn btn-danger">Delete Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Edit modal setup
        const editModal = document.getElementById('editOrderModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-orderid');
                const status = button.getAttribute('data-status');
                
                document.getElementById('edit_order_id').value = orderId;
                document.getElementById('editOrderId').textContent = orderId;
                document.getElementById('edit_order_status').value = status;
            });
        }
        
        // Delete modal setup
        const deleteModal = document.getElementById('deleteOrderModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-orderid');
                document.getElementById('delete_order_id').value = orderId;
            });
        }
        
        // View modal setup - load order details via AJAX
        const viewModal = document.getElementById('viewOrderModal');
        if (viewModal) {
            viewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-orderid');
                document.getElementById('viewOrderId').textContent = orderId;
                
                // Load order details via AJAX
                $.ajax({
                    url: 'get_order_details.php',
                    type: 'GET',
                    data: { order_id: orderId },
                    success: function(response) {
                        $('#viewOrderDetails').html(response);
                    },
                    error: function() {
                        $('#viewOrderDetails').html('<div class="alert alert-danger">Failed to load order details.</div>');
                    }
                });
            });
        }
    </script>

            <!-- Generate Report Button -->
    <div class="container mb-5">
        <div class="text-center">
            <form action="generate_report.php" method="post">
                <button type="submit" class="btn btn-lg gamer-btn" style="background-color: var(--gamer-green); color: black;">
                    <i class="fas fa-file-pdf me-2"></i> Generate Report
                </button>
            </form>
        </div>
    </div>


</body>
</html>
<?php mysqli_close($conn); ?>