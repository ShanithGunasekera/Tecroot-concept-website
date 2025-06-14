<?php
require_once 'api_config.php';
require_once 'db_configorder.php';

// Fix for session notice - check if session is already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

// Create tables if they don't exist
try {
    $conn->exec("
        CREATE TABLE IF NOT EXISTS carts (
            cart_id VARCHAR(255) PRIMARY KEY,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS cart_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cart_id VARCHAR(255),
            product_id VARCHAR(255),
            product_name VARCHAR(255),
            quantity INT DEFAULT 1,
            price DECIMAL(10,2),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
            INDEX (cart_id, product_id)
        )
    ");
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database setup failed: ' . $e->getMessage()], 500);
    exit;
}

try {
    // Initialize or get cart ID
    $cartId = $_SESSION['cart_id'] ?? uniqid('cart_', true);
    $_SESSION['cart_id'] = $cartId;

    // Ensure cart exists in database
    $conn->prepare("INSERT IGNORE INTO carts (cart_id) VALUES (?)")->execute([$cartId]);

    switch ($requestMethod) {
        case 'GET':
            // Get cart items from database
            $stmt = $conn->prepare("
                SELECT product_id, product_name, quantity, price 
                FROM cart_items 
                WHERE cart_id = ?
            ");
            $stmt->execute([$cartId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate totals
            $totalPrice = 0;
            $totalItems = 0;
            $cart = [];
            
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $cart[$productId] = [
                    'id' => $productId,
                    'name' => $item['product_name'],
                    'price' => (float)$item['price'],
                    'quantity' => (int)$item['quantity']
                ];
                
                $totalPrice += $item['price'] * $item['quantity'];
                $totalItems += $item['quantity'];
            }
            
            jsonResponse([
                'cart' => $cart,
                'totalItems' => $totalItems,
                'totalPrice' => $totalPrice
            ]);
            break;
            
        case 'POST':
            // Add to cart
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['product_id']) || !isset($data['product_name']) || !isset($data['price'])) {
                jsonResponse(['error' => 'Missing required fields'], 400);
            }
            
            $productId = $data['product_id'];
            $productName = $data['product_name'];
            $price = (float)$data['price'];
            $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
            
            // Check if item already exists
            $stmt = $conn->prepare("
                SELECT quantity FROM cart_items 
                WHERE cart_id = ? AND product_id = ?
            ");
            $stmt->execute([$cartId, $productId]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update quantity
                $newQuantity = $existing['quantity'] + $quantity;
                $stmt = $conn->prepare("
                    UPDATE cart_items 
                    SET quantity = ? 
                    WHERE cart_id = ? AND product_id = ?
                ");
                $stmt->execute([$newQuantity, $cartId, $productId]);
            } else {
                // Add new item
                $stmt = $conn->prepare("
                    INSERT INTO cart_items 
                    (cart_id, product_id, product_name, quantity, price) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$cartId, $productId, $productName, $quantity, $price]);
            }
            
            jsonResponse(['success' => 'Product added to cart']);
            break;
            
        case 'PUT':
            // Update quantity
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['product_id']) || !isset($data['quantity'])) {
                jsonResponse(['error' => 'Missing product_id or quantity'], 400);
            }
            
            $productId = $data['product_id'];
            $quantity = max(1, (int)$data['quantity']);
            
            $stmt = $conn->prepare("
                UPDATE cart_items 
                SET quantity = ? 
                WHERE cart_id = ? AND product_id = ?
            ");
            $stmt->execute([$quantity, $cartId, $productId]);
            
            if ($stmt->rowCount() === 0) {
                jsonResponse(['error' => 'Product not found in cart'], 404);
            }
            
            jsonResponse(['success' => 'Cart updated']);
            break;
            
        case 'DELETE':
            // Remove item
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['product_id'])) {
                jsonResponse(['error' => 'Missing product_id'], 400);
            }
            
            $stmt = $conn->prepare("
                DELETE FROM cart_items 
                WHERE cart_id = ? AND product_id = ?
            ");
            $stmt->execute([$cartId, $data['product_id']]);
            
            jsonResponse(['success' => 'Product removed from cart']);
            break;
            
        default:
            jsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>