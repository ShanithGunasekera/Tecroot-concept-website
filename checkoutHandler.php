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
    // Validate inputs
    $errors = [];
    
    // Validate postcode (5 digits)
    if (!preg_match('/^\d{5}$/', $_POST['postcode'])) {
        $errors[] = "Postcode must be exactly 5 digits";
    }
    
    // Validate phone (Sri Lankan format: +94 followed by 9 digits)
    if (!preg_match('/^\+94\s?\d{2}\s?\d{3}\s?\d{4}$/', $_POST['phone'])) {
        $errors[] = "Phone must be in Sri Lankan format (+94 77 123 4567)";
    }
    
    // If validation errors, redirect back with errors
    if (!empty($errors)) {
        $_SESSION['checkout_error'] = implode("<br>", $errors);
        header('Location: checkout.php');
        exit();
    }
    
    $conn = getDBConnection();
    
    try {
        // Begin transaction
        $conn->beginTransaction();

        // 1. Save order information
        $stmt = $conn->prepare("
            INSERT INTO orders (
                first_name, last_name, email, phone, street_address, 
                apartment, city, postcode, country, company,
                total_amount, total_items, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        
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
            $_POST['street_address'],
            $_POST['apartment'] ?? null,
            $_POST['city'],
            $_POST['postcode'],
            $_POST['country'],
            $_POST['company'] ?? null,
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
            $stmt->execute([
                $order_id,
                $product_id,
                $item['name'],
                $item['quantity'],
                $item['price'],
                $item['price'] * $item['quantity']
            ]);
        }

        // Commit transaction
        $conn->commit();

        // Clear cart and set success message
        unset($_SESSION['cart']);
        $_SESSION['order_success'] = [
            'order_id' => $order_id,
            'total' => $totalAmount
        ];
        
        header('Location: order_confirmation.php');
        exit();
        
    } catch(PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $_SESSION['checkout_error'] = "Error processing your order: " . $e->getMessage();
        header('Location: checkout.php');
        exit();
    }
} else {
    // Redirect to checkout page if not POST request
    header('Location: checkout.php');
    exit();
}