<?php
session_start();
require_once 'db_configorder.php';

// Check if cart exists and is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['checkout_error'] = "Your cart is empty. Please add items before checkout.";
    header('Location: cart.php');
    exit();
}

// Process checkout when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getDBConnection();
    
    try {
        // Begin transaction
        $conn->beginTransaction();

        // 1. Save order information
        $stmt = $conn->prepare("
            INSERT INTO orders (
                first_name, last_name, email, phone, company,
                country, street_address, apartment, city, postcode, 
                payment_method, total_amount, total_items, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        // Calculate totals
        $totalAmount = 0;
        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }
        
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['company'] ?? null, // Use null if company not provided
            $_POST['country'],
            $_POST['street_address'],
            $_POST['apartment'] ?? null, // Use null if apartment not provided
            $_POST['city'],
            $_POST['postcode'],
            $_POST['payment_method'],
            $totalAmount,
            $totalItems
        ]);
        
        $order_id = $conn->lastInsertId();

        // 2. Save order items
        $stmt = $conn->prepare("
            INSERT INTO order_items (
                order_id, product_id, product_name, quantity, price, subtotal
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->execute([
                $order_id,
                $product_id,
                $item['name'],
                $item['quantity'],
                $item['price'],
                $subtotal
            ]);
        }

        // Commit transaction
        $conn->commit();

        // Clear cart and set success message
        unset($_SESSION['cart']);
        $_SESSION['order_success'] = [
            'order_id' => $order_id,
            'total' => $totalAmount,
            'customer_name' => $_POST['first_name'] . ' ' . $_POST['last_name'],
            'payment_method' => $_POST['payment_method']
        ];
        
        // Redirect based on payment method
        if ($_POST['payment_method'] == 'bank_transfer') {
            header('Location: payment_bank.php');
        } else {
            header('Location: order_confirmation.php');
        }
        exit();
        
    } catch(PDOException $e) {
        // Rollback transaction on error
        if (isset($conn)) {
            $conn->rollBack();
        }
        $_SESSION['checkout_error'] = "Error processing your order: " . $e->getMessage();
        header('Location: checkout.php');
        exit();
    }
} else {
    // Redirect to checkout page if not POST request
    header('Location: checkout.php');
    exit();
}
?>