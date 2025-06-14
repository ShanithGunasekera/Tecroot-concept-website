<?php
session_start();
require_once 'db_configorder.php';

// Check if cart exists and is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['checkout_error'] = "Your cart is empty. Please add items before checkout.";
    header('Location: cart.php');
    exit();
}

// Calculate total price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $id => $product) {
    $totalPrice += $product['price'] * $product['quantity'];
}

// Generate a random order reference number
$orderRef = 'TEC-' . strtoupper(uniqid());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bank Transfer Payment - Tecroot</title>
    <link rel="icon" href="2.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Anta&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2e7d32;
            --primary-light: #4caf50;
            --bg-color: #121212;
            --card-color: #1e1e1e;
            --card-hover: #252525;
            --text-light: #e0e0e0;
            --text-dark: #ffffff;
            --text-muted: #9e9e9e;
            --radius: 12px;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --border: 1px solid #333;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Anta', sans-serif;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 3rem auto;
        }

        .card {
            background: var(--card-color);
            padding: 2.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        h1 {
            color: var(--primary-light);
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            padding-bottom: 1rem;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        .order-info {
            background: rgba(46, 125, 50, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(46, 125, 50, 0.3);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-muted);
        }

        .info-value {
            font-weight: bold;
        }

        .bank-details {
            background: rgba(46, 125, 50, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .bank-detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(46, 125, 50, 0.3);
        }

        .bank-detail-item:last-child {
            border-bottom: none;
        }

        .bank-detail-label {
            color: var(--text-muted);
        }

        .bank-detail-value {
            font-weight: bold;
            color: var(--primary-light);
        }

        .instructions {
            margin: 2rem 0;
            line-height: 1.8;
        }

        .instructions ol {
            padding-left: 1.5rem;
        }

        .instructions li {
            margin-bottom: 1rem;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.4);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.6);
        }

        .btn-secondary {
            background: #333;
            color: var(--text-light);
            border: 1px solid #444;
        }

        .btn-secondary:hover {
            background: #3a3a3a;
            transform: translateY(-2px);
        }

        .icon {
            margin-right: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.6s ease-out forwards;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                margin: 1.5rem auto;
            }

            .card {
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            .btn-container {
                flex-direction: column;
                gap: 1rem;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>Bank Transfer Payment</h1>
            
            <div class="order-info">
                <div class="info-item">
                    <span class="info-label">Order Reference:</span>
                    <span class="info-value"><?= $orderRef ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Amount:</span>
                    <span class="info-value">LKR <?= number_format($totalPrice, 2) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">Bank Transfer</span>
                </div>
            </div>
            
            <div class="bank-details">
                <h3 style="margin-top: 0; color: var(--primary-light);">Our Bank Details</h3>
                <div class="bank-detail-item">
                    <span class="bank-detail-label">Account Name:</span>
                    <span class="bank-detail-value">Safwan Mansoor</span>
                </div>
                <div class="bank-detail-item">
                    <span class="bank-detail-label">Account Number:</span>
                    <span class="bank-detail-value">80465421</span>
                </div>
                <div class="bank-detail-item">
                    <span class="bank-detail-label">Bank:</span>
                    <span class="bank-detail-value">Commercial Bank</span>
                </div>
                <div class="bank-detail-item">
                    <span class="bank-detail-label">Branch:</span>
                    <span class="bank-detail-value">Kollupitiya</span>
                </div>
            </div>
            
            <div class="instructions">
                <h3>Payment Instructions:</h3>
                <ol>
                    <li>Make a transfer to our bank account using the details above.</li>
                    <li>Use the Order Reference <strong><?= $orderRef ?></strong> as the payment reference.</li>
                    <li>After completing the payment, please click the "Confirm Payment" button below.</li>
                    <li>You may also send us the payment receipt via email or WhatsApp for faster verification.</li>
                    <li>Your order will be processed once we confirm the payment in our account (usually within 24 hours).</li>
                </ol>
                <p>We will send you an email confirmation once your payment is verified.</p>
            </div>
            
            <div class="btn-container">
                <a href="place_order.php?ref=<?= $orderRef ?>&method=bank" class="btn btn-primary">
                    <i class="fas fa-check-circle icon"></i> Confirm Payment
                </a>
                <a href="checkout.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left icon"></i> Back to Checkout
                </a>
            </div>
        </div>
    </div>
</body>

</html>