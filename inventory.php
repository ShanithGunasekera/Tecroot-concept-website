<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "tecroot");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create operation
    if (isset($_POST['add_item'])) {
        $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
        $quantity = intval($_POST['quantity']);
        $unit_cost = floatval($_POST['unit_cost']);
        
        // Handle file upload
        $image_path = '';
        if ($_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = basename($_FILES['item_image']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
            }
        }
        
        $sql = "INSERT INTO inventory (item_name, quantity, unit_cost, image_path) 
                VALUES ('$item_name', $quantity, $unit_cost, '$image_path')";
        mysqli_query($conn, $sql);
    }
    
    // Update operation
    if (isset($_POST['update_item'])) {
        $item_id = intval($_POST['item_id']);
        $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
        $quantity = intval($_POST['quantity']);
        $unit_cost = floatval($_POST['unit_cost']);
        
        // Handle file upload if new image is provided
        $image_path = $_POST['current_image'];
        if ($_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $file_name = basename($_FILES['item_image']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_path)) {
                // Delete old image if it exists
                if (!empty($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }
                $image_path = $target_path;
            }
        }
        
        $sql = "UPDATE inventory SET 
                item_name = '$item_name', 
                quantity = $quantity, 
                unit_cost = $unit_cost, 
                image_path = '$image_path' 
                WHERE item_id = $item_id";
        mysqli_query($conn, $sql);
    }
    
    // Delete operation
    if (isset($_POST['delete_item'])) {
        $item_id = intval($_POST['item_id']);
        
        // First get the image path to delete the file
        $sql = "SELECT image_path FROM inventory WHERE item_id = $item_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        
        if (!empty($row['image_path']) && file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
        
        $sql = "DELETE FROM inventory WHERE item_id = $item_id";
        mysqli_query($conn, $sql);
    }
    
    // Generate PDF report
    if (isset($_POST['generate_report'])) {
        require('fpdf/fpdf.php');
        
        // Get inventory data
        $sql = "SELECT * FROM inventory ORDER BY item_name";
        $result = mysqli_query($conn, $sql);
        $inventory_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Calculate grand total
        $grand_total = 0;
        foreach ($inventory_items as $item) {
            $grand_total += $item['quantity'] * $item['unit_cost'];
        }
        
        // Create PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Tecroot Inventory Report', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Report date
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1);
        $pdf->Ln(5);
        
        // Inventory summary
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Inventory Summary', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Total Items: ' . count($inventory_items), 0, 1);
        $pdf->Cell(0, 10, 'Total Inventory Value: $' . number_format($grand_total, 2), 0, 1);
        $pdf->Ln(10);
        
        // Inventory table header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Item Name', 1, 0, 'L');
        $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Unit Cost', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Total Value', 1, 1, 'C');
        
        // Inventory table rows
        $pdf->SetFont('Arial', '', 12);
        foreach ($inventory_items as $item) {
            $total_value = $item['quantity'] * $item['unit_cost'];
            $pdf->Cell(80, 10, $item['item_name'], 1, 0, 'L');
            $pdf->Cell(30, 10, $item['quantity'], 1, 0, 'C');
            $pdf->Cell(30, 10, '$' . number_format($item['unit_cost'], 2), 1, 0, 'C');
            $pdf->Cell(40, 10, '$' . number_format($total_value, 2), 1, 1, 'C');
        }
        
        // Simple bar chart (simulated with text)
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Top 5 Most Valuable Items', 0, 1);
        
        // Get top 5 items by total value
        $top_items = array_slice($inventory_items, 0, 5);
        usort($top_items, function($a, $b) {
            $val_a = $a['quantity'] * $a['unit_cost'];
            $val_b = $b['quantity'] * $b['unit_cost'];
            return $val_b - $val_a;
        });
        
        foreach ($top_items as $item) {
            $value = $item['quantity'] * $item['unit_cost'];
            $width = ($value / $grand_total) * 150; // Scale to max 150 units
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 8, substr($item['item_name'], 0, 20), 0, 0, 'L');
            $pdf->Cell(10, 8, '$' . number_format($value, 2), 0, 0, 'R');
            $pdf->Cell(1, 8, '', 0, 0);
            $pdf->SetFillColor(50, 200, 50);
            $pdf->Cell($width, 8, '', 'F');
            $pdf->Ln(8);
        }
        
        // Output PDF
        $pdf->Output('D', 'Tecroot_Inventory_Report_' . date('Y-m-d') . '.pdf');
        exit();
    }
}

// Read operation - get all inventory items (with search filter if applicable)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sql = "SELECT * FROM inventory ";
if (!empty($search)) {
    $sql .= "WHERE item_name LIKE '%$search%' ";
}
$sql .= "ORDER BY item_name";
$result = mysqli_query($conn, $sql);
$inventory_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Management | Tecroot</title>
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
        
        .gamer-form-control {
            background-color: rgba(0, 0, 0, 0.5);
            border: 1px solid #333;
            color: #fff;
            border-radius: 0;
            transition: all 0.3s;
        }
        
        .gamer-form-control:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--gamer-green);
            color: var(--gamer-green);
            box-shadow: 0 0 0 0.25rem rgba(0, 255, 0, 0.25);
        }
        
        .gamer-btn {
            background-color: var(--gamer-green);
            color: #000;
            border: none;
            border-radius: 0;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            padding: 10px 20px;
            transition: all 0.3s;
            text-transform: uppercase;
        }
        
        .gamer-btn:hover {
            background-color: var(--gamer-accent);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 0, 0.3);
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            background-color: var(--gamer-green);
            color: #000;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        
        .quantity-input {
            width: 60px;
            text-align: center;
            margin: 0 5px;
        }
        
        .inventory-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .inventory-table th {
            background-color: rgba(0, 0, 0, 0.7);
            color: var(--gamer-green);
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid var(--gamer-green);
        }
        
        .inventory-table td {
            padding: 12px;
            border-bottom: 1px solid #333;
            vertical-align: middle;
        }
        
        .inventory-table tr:hover {
            background-color: rgba(0, 255, 0, 0.05);
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid var(--gamer-green);
        }
        
        .action-btn {
            padding: 5px 10px;
            margin: 0 3px;
            font-size: 0.8rem;
        }
        
        .edit-btn {
            background-color: #ffc107;
            color: #000;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: #fff;
        }
        
        .total-value {
            font-family: 'Press Start 2P', cursive;
            color: var(--gamer-green);
            font-size: 1.2rem;
        }

                .report-btn {
            background-color: var(--gamer-green);
            color: #000;
            border: none;
            border-radius: 0;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            padding: 10px 20px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .report-btn:hover {
            background-color: var(--gamer-accent);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 0, 0.3);
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
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .inventory-table {
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
            <a class="navbar-brand d-flex align-items-center" href="adminIndex.html">
                <img src="2.png" alt="Tecroot Logo" class="logo-img me-2" style="height: 30px;">
                Tecroot
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="adminIndex.html"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php"><i class="fas fa-gamepad me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="customerProfile.php"><i class="fas fa-boxes me-1"></i> Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="account.php"><i class="fas fa-sign-out-alt me-1"></i> Add controls</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Search and Report Buttons -->
        <div class="search-report-container">
            <form method="get" action="inventory.php" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control gamer-form-control" name="search" placeholder="Search items..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
            
            <form method="post" action="inventory.php">
                <button type="submit" name="generate_report" class="btn report-btn">
                    <i class="fas fa-file-pdf me-1"></i> Generate PDF Report
                </button>
            </form>
        </div>

        <!-- Add New Item Form -->
        <div class="gamer-card mb-5">
            <div class="gamer-card-header">
                <i class="fas fa-plus-circle me-2"></i> ADD NEW INVENTORY ITEM
            </div>
            <div class="card-body">
                <form action="inventory.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control gamer-form-control" name="item_name" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Quantity</label>
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn minus-btn">-</button>
                                <input type="number" class="form-control gamer-form-control quantity-input" name="quantity" value="1" min="0" required>
                                <button type="button" class="quantity-btn plus-btn">+</button>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Unit Cost ($)</label>
                            <input type="number" step="0.01" class="form-control gamer-form-control" name="unit_cost" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Item Image</label>
                            <input type="file" class="form-control gamer-form-control" name="item_image" accept="image/*">
                        </div>
                        <div class="col-md-1 d-flex align-items-end mb-3">
                            <button type="submit" name="add_item" class="btn gamer-btn w-100">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="gamer-card">
            <div class="gamer-card-header">
                <i class="fas fa-box-open me-2"></i> CURRENT INVENTORY
                <?php if (!empty($search)): ?>
                    <span class="float-end" style="font-size: 0.8rem;">
                        Showing results for: "<?php echo htmlspecialchars($search); ?>"
                        <a href="inventory.php" class="text-danger ms-2"><i class="fas fa-times"></i> Clear</a>
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($inventory_items)): ?>
                    <div class="table-responsive">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Total Value</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grand_total = 0;
                                foreach ($inventory_items as $item): 
                                    $total_value = $item['quantity'] * $item['unit_cost'];
                                    $grand_total += $total_value;
                                ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($item['image_path'])): ?>
                                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" class="item-image" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                            <?php else: ?>
                                                <div class="text-center">
                                                    <i class="fas fa-image" style="font-size: 2rem; color: #333;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td>$<?php echo number_format($item['unit_cost'], 2); ?></td>
                                        <td>$<?php echo number_format($total_value, 2); ?></td>
                                        <td>
                                            <button class="btn btn-sm action-btn edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" 
                                                    data-id="<?php echo $item['item_id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($item['item_name']); ?>"
                                                    data-quantity="<?php echo $item['quantity']; ?>"
                                                    data-cost="<?php echo $item['unit_cost']; ?>"
                                                    data-image="<?php echo htmlspecialchars($item['image_path']); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm action-btn delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                    data-id="<?php echo $item['item_id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                                    <td class="total-value">$<?php echo number_format($grand_total, 2); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x mb-3" style="color: #333;"></i>
                        <p>No inventory items found. <?php if (!empty($search)): ?>Try a different search term.<?php else: ?>Add your first item above.<?php endif; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit and Delete Modals remain the same as before -->
    <!-- Edit Item Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="inventory.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Inventory Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="item_id" id="edit_item_id">
                        <input type="hidden" name="current_image" id="current_image">
                        
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control gamer-form-control" name="item_name" id="edit_item_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn minus-btn">-</button>
                                <input type="number" class="form-control gamer-form-control quantity-input" name="quantity" id="edit_quantity" min="0" required>
                                <button type="button" class="quantity-btn plus-btn">+</button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Unit Cost ($)</label>
                            <input type="number" step="0.01" class="form-control gamer-form-control" name="unit_cost" id="edit_unit_cost" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Item Image</label>
                            <input type="file" class="form-control gamer-form-control" name="item_image" accept="image/*">
                            <div class="mt-2" id="current_image_container"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_item" class="btn gamer-btn">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="inventory.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="item_id" id="delete_item_id">
                        <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_item" class="btn btn-danger">Delete Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Quantity control buttons
        document.querySelectorAll('.plus-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                input.value = parseInt(input.value) + 1;
            });
        });
        
        document.querySelectorAll('.minus-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                if (parseInt(input.value) > 0) {
                    input.value = parseInt(input.value) - 1;
                }
            });
        });
        
        // Edit modal setup
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const itemId = button.getAttribute('data-id');
                const itemName = button.getAttribute('data-name');
                const quantity = button.getAttribute('data-quantity');
                const cost = button.getAttribute('data-cost');
                const imagePath = button.getAttribute('data-image');
                
                document.getElementById('edit_item_id').value = itemId;
                document.getElementById('edit_item_name').value = itemName;
                document.getElementById('edit_quantity').value = quantity;
                document.getElementById('edit_unit_cost').value = cost;
                document.getElementById('current_image').value = imagePath;
                
                const imageContainer = document.getElementById('current_image_container');
                if (imagePath) {
                    imageContainer.innerHTML = `
                        <p>Current Image:</p>
                        <img src="${imagePath}" class="item-image" alt="Current item image">
                    `;
                } else {
                    imageContainer.innerHTML = '<p>No image currently uploaded</p>';
                }
            });
        }
        
        // Delete modal setup
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const itemId = button.getAttribute('data-id');
                document.getElementById('delete_item_id').value = itemId;
            });
        }
        
        // Auto-submit search form when typing (with delay)
        let searchTimer;
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>