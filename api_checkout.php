<?php
require_once 'api_config.php';
require_once 'checkoutHandler.php';

session_start();

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    // Authenticate user
    $user = authenticateUser();
    
    // Check if cart exists and is not empty
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        jsonResponse(['error' => 'Your cart is empty'], 400);
    }
    
    // Get request data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'street_address', 'city', 'postcode', 'country', 'payment_method'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            jsonResponse(['error' => "Missing required field: $field"], 400);
        }
    }
    
    // Validate postcode (5 digits)
    if (!preg_match('/^\d{5}$/', $data['postcode'])) {
        jsonResponse(['error' => 'Postcode must be exactly 5 digits'], 400);
    }
    
    // Validate phone (Sri Lankan format: +94 followed by 9 digits)
    if (!preg_match('/^\+94\s?\d{2}\s?\d{3}\s?\d{4}$/', $data['phone'])) {
        jsonResponse(['error' => 'Phone must be in Sri Lankan format (+94 77 123 4567)'], 400);
    }
    
    $conn = getDBConnection();
    $conn->beginTransaction();

    try {
        // 1. Save order information
        $stmt = $conn->prepare("
            INSERT INTO orders (
                first_name, last_name, email, phone, street_address, 
                apartment, city, postcode, country, company,
                total_amount, total_items, status, user_id, payment_method
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)
        ");
        
        $totalAmount = 0;
        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }
        
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'],
            $data['street_address'],
            $data['apartment'] ?? null,
            $data['city'],
            $data['postcode'],
            $data['country'],
            $data['company'] ?? null,
            $totalAmount,
            $totalItems,
            $user['user_id'],
            $data['payment_method']
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

        // Clear cart
        unset($_SESSION['cart']);
        
        jsonResponse([
            'success' => 'Order placed successfully',
            'order_id' => $order_id,
            'total' => $totalAmount
        ]);
        
    } catch(PDOException $e) {
        $conn->rollBack();
        jsonResponse(['error' => 'Error processing your order: ' . $e->getMessage()], 500);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>