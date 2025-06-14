
<?php
session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $price = floatval($_POST['price']);
    $imagePath = $_POST['image_path'] ?? 'default-product-image.jpg';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Check if product already exists in cart
    if (array_key_exists($productId, $_SESSION['cart'])) {
        // Update quantity if exists
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        // Add new product to cart
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $productName,
            'price' => $price,
            'quantity' => $quantity,
            'image' => $imagePath,
            'brand' => $_POST['brand'] ?? 'Tecroot'
        ];
    }
    
    // Redirect back to products page or to cart
    header('Location: '.($_POST['redirect'] ?? 'products.php'));
    exit();
}

// Handle removing from cart
if (isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    if (array_key_exists($productId, $_SESSION['cart'])) {
        unset($_SESSION['cart'][$productId]);
    }
    header('Location: cart.php');
    exit();
}
?>