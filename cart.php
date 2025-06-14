<?php
session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle quantity update
if (isset($_POST['update_qty'])) {
    $id = $_POST['id'];
    $quantity = max(1, intval($_POST['quantity']));
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] = $quantity;
    }
    // After update, immediately recalculate totals and send back updated data
    $totalPrice = 0;
    $totalItems = 0;
    foreach ($_SESSION['cart'] as $product) {
        $totalPrice += $product['price'] * $product['quantity'];
        $totalItems += $product['quantity'];
    }
    echo json_encode([
        'itemTotal' => number_format($_SESSION['cart'][$id]['price'] * $_SESSION['cart'][$id]['quantity'], 2),
        'totalPrice' => number_format($totalPrice, 2),
        'totalItems' => $totalItems
    ]);
    exit;
}

// Calculate totals
$totalPrice = 0;
$totalItems = 0;
foreach ($_SESSION['cart'] as $product) {
    $totalPrice += $product['price'] * $product['quantity'];
    $totalItems += $product['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Tecroot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="2.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2e7d32;
            --primary-dark: #1b5e20;
            --primary-light: #4caf50;
            --bg-color: #121212;
            --card-color: #1e1e1e;
            --card-hover: #252525;
            --text-light: #e0e0e0;
            --text-dark: #ffffff;
            --text-muted: #9e9e9e;
            --border: 1px solid #333;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }
        .cart-item { 
            transition: all 0.3s; 
            background-color: var(--card-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .cart-item:hover { 
            transform: translateY(-5px); 
            background-color: var(--card-hover);
        }
        .product-image {
            width: 150px;
            height: 150px;
            object-fit: contain;
        }
        .qty-btn {
            width: 30px;
            height: 30px;
            background-color: #333;
            color: var(--text-light);
            border: var(--border);
        }
        .qty-btn:hover {
            background-color: #444;
        }
        .checkout-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        }
        .checkout-btn:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }
        .card {
            background-color: var(--card-color);
            border: var(--border);
            border-radius: 10px;
        }
        .text-muted {
            color: var(--text-muted) !important;
        }
        .navbar {
            background-color: var(--primary-color) !important;
        }
        .btn-outline-secondary {
            color: var(--text-light);
            border-color: var(--text-muted);
        }
        .btn-outline-secondary:hover {
            background-color: #333;
            border-color: var(--text-light);
        }
        hr {
            border-color: var(--text-muted);
        }
        .form-control {
            background-color: #252525;
            border: var(--border);
            color: var(--text-dark);
        }
        .form-control:focus {
            background-color: #252525;
            color: var(--text-dark);
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
        }
        .badge {
            color: #121212 !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="home.html">
            <img src="1.png" alt="Tecroot Logo" height="30">
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="products.php"><i class="fas fa-arrow-left me-1"></i> Continue Shopping</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i> Cart 
                        <span class="badge bg-warning text-dark" id="cart-count"><?= $totalItems ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4 text-light"><i class="fas fa-shopping-cart"></i> Your Cart</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <?php foreach ($_SESSION['cart'] as $id => $product): ?>
                    <div class="row mb-4 align-items-center cart-item" data-id="<?= $id ?>">
                        <div class="col-md-2">
                            <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid product-image">
                        </div>
                        <div class="col-md-5">
                            <h5 class="text-light"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($product['brand']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary qty-btn minus" type="button">-</button>
                                <input type="text" class="form-control text-center qty-input" value="<?= $product['quantity'] ?>" readonly>
                                <button class="btn btn-outline-secondary qty-btn plus" type="button">+</button>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <p class="fw-bold item-total text-light">LKR <?= number_format($product['price'] * $product['quantity'], 2) ?></p>
                            <a href="cartHandler.php?remove=<?= $id ?>" class="text-danger"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-light">Order Summary</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-light">Subtotal (<span id="item-count" class="text-light"><?= $totalItems ?></span> items)</span>
                        <span id="subtotal" class="text-light">LKR <?= number_format($totalPrice, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-light">Shipping</span>
                        <span class="text-success">FREE</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold mb-4">
                        <span class="text-light">Total</span>
                        <span id="total" class="text-light">LKR <?= number_format($totalPrice, 2) ?></span>
                    </div>
                    <a href="checkout.php">
                        <button class="btn checkout-btn text-white w-100 py-2 mb-2">
                            <i class="fas fa-lock me-2"></i> Proceed to Checkout
                        </button>
                    </a>
                    <a href="products.php" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-5x mb-4" style="color: var(--primary-color);"></i>
        <h3 class="text-light">Your cart is empty</h3>
        <p class="text-muted mb-4">Browse our products and add some items to your cart</p>
        <a href="products.php" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i> Continue Shopping
        </a>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.qty-btn').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('.cart-item');
        const id = row.getAttribute('data-id');
        const input = row.querySelector('.qty-input');
        let quantity = parseInt(input.value);

        if (this.classList.contains('minus') && quantity > 1) {
            quantity--;
        } else if (this.classList.contains('plus')) {
            quantity++;
        }

        input.value = quantity;

        // AJAX to update quantity in session
        const formData = new FormData();
        formData.append('update_qty', true);
        formData.append('id', id);
        formData.append('quantity', quantity);

        fetch('cart.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            row.querySelector('.item-total').innerText = 'LKR ' + data.itemTotal;
            document.getElementById('subtotal').innerText = 'LKR ' + data.totalPrice;
            document.getElementById('total').innerText = 'LKR ' + data.totalPrice;
            document.getElementById('item-count').innerText = data.totalItems;
            document.getElementById('cart-count').innerText = data.totalItems;
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity update buttons
    document.querySelectorAll('.qty-btn').forEach(button => {
        button.addEventListener('click', async function() {
            const row = this.closest('.cart-item');
            const productId = row.dataset.id;
            const input = row.querySelector('.qty-input');
            let quantity = parseInt(input.value);

            if (this.classList.contains('minus') && quantity > 1) {
                quantity--;
            } else if (this.classList.contains('plus')) {
                quantity++;
            }

            input.value = quantity;
            await updateCartItem(productId, quantity, row);
        });
    });

    // Remove item buttons
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            if (confirm('Are you sure you want to remove this item?')) {
                await removeCartItem(productId);
                location.reload(); // Refresh to show updated cart
            }
        });
    });

    async function updateCartItem(productId, quantity, row) {
        try {
            const response = await fetch('api_cart.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });
            
            const result = await response.json();
            
            if (response.ok) {
                // Update the displayed totals
                row.querySelector('.item-total').textContent = 'LKR ' + result.itemTotal;
                document.getElementById('subtotal').textContent = 'LKR ' + result.totalPrice;
                document.getElementById('total').textContent = 'LKR ' + result.totalPrice;
                document.getElementById('item-count').textContent = result.totalItems;
                document.getElementById('cart-count').textContent = result.totalItems;
            } else {
                alert('Error: ' + (result.error || 'Failed to update quantity'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to update quantity');
        }
    }

    async function removeCartItem(productId) {
        try {
            const response = await fetch('api_cart.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId
                })
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                alert('Error: ' + (result.error || 'Failed to remove item'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to remove item');
        }
    }
});
</script>
</body>
</html>