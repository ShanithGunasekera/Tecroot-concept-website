<?php
require_once 'api_config.php';
require_once 'db_configorder.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

// Create tables if not exist
try {
    $conn->exec("
        CREATE TABLE IF NOT EXISTS carts (
            cart_id VARCHAR(255) PRIMARY KEY,
            user_id VARCHAR(255),
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
            image VARCHAR(255),
            brand VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
            INDEX (cart_id, product_id)
        )
    ");
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database setup failed: ' . $e->getMessage()], 500);
    exit;
}

// Accept cart_id from query, body, or session
$input = json_decode(file_get_contents('php://input'), true);
$cartId = $_GET['cart_id'] 
    ?? ($input['cart_id'] ?? ($_SESSION['cart_id'] ?? uniqid('cart_', true)));
$_SESSION['cart_id'] = $cartId;

// Ensure cart exists
$conn->prepare("INSERT IGNORE INTO carts (cart_id) VALUES (?)")->execute([$cartId]);

try {
    switch ($requestMethod) {
        case 'GET':
            $stmt = $conn->prepare("
                SELECT product_id, product_name, quantity, price, image, brand 
                FROM cart_items 
                WHERE cart_id = ?
            ");
            $stmt->execute([$cartId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalPrice = 0;
            $totalItems = 0;
            $cart = [];

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $cart[$productId] = [
                    'id' => $productId,
                    'name' => $item['product_name'],
                    'price' => (float)$item['price'],
                    'quantity' => (int)$item['quantity'],
                    'image' => $item['image'],
                    'brand' => $item['brand']
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
            if (!isset($input['product_id'], $input['product_name'], $input['price'])) {
                jsonResponse(['error' => 'Missing required fields (product_id, product_name, price)'], 400);
            }

            $productId = $input['product_id'];
            $productName = $input['product_name'];
            $price = (float)$input['price'];
            $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
            $image = $input['image'] ?? 'default.jpg';
            $brand = $input['brand'] ?? 'Tecroot';

            $stmt = $conn->prepare("
                SELECT quantity FROM cart_items 
                WHERE cart_id = ? AND product_id = ?
            ");
            $stmt->execute([$cartId, $productId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $newQuantity = $existing['quantity'] + $quantity;
                $stmt = $conn->prepare("
                    UPDATE cart_items 
                    SET quantity = ? 
                    WHERE cart_id = ? AND product_id = ?
                ");
                $stmt->execute([$newQuantity, $cartId, $productId]);
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO cart_items 
                    (cart_id, product_id, product_name, quantity, price, image, brand) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$cartId, $productId, $productName, $quantity, $price, $image, $brand]);
            }

            jsonResponse(['success' => 'Product added to cart']);
            break;

        case 'PUT':
            if (!isset($input['product_id'], $input['quantity'])) {
                jsonResponse(['error' => 'Missing product_id or quantity'], 400);
            }

            $productId = $input['product_id'];
            $quantity = max(1, (int)$input['quantity']);

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
            if (!isset($input['product_id'])) {
                jsonResponse(['error' => 'Missing product_id'], 400);
            }

            $stmt = $conn->prepare("
                DELETE FROM cart_items 
                WHERE cart_id = ? AND product_id = ?
            ");
            $stmt->execute([$cartId, $input['product_id']]);

            jsonResponse(['success' => 'Product removed from cart']);
            break;

        default:
            jsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
