<?php
// Start session only once at the very beginning
session_start();

// Check if order was successfully placed
if (!isset($_SESSION['order_success'])) {
    header('Location: cart.php');
    exit();
}

// Get order details from session
$order_id = $_SESSION['order_success']['order_id'];
$total = $_SESSION['order_success']['total'];
$customer_name = $_SESSION['order_success']['customer_name'];
$payment_method = $_SESSION['order_success']['payment_method'];

// Format the total with 2 decimal places
$formatted_total = number_format($total, 2);

// Clear the order success session to prevent refresh issues
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - Tecroot</title>
    <link rel="icon" href="2.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Anta&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2e7d32;
            --primary-light: #4caf50;
            --primary-dark: #1b5e20;
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
            padding: 2rem;
            animation: fadeIn 0.6s ease-out;
        }

        .confirmation-card {
            background: var(--card-color);
            padding: 3rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            border: var(--border);
            transition: var(--transition);
        }

        .confirmation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .success-icon {
            color: var(--primary-light);
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 1s;
        }

        h1 {
            color: var(--primary-light);
            margin-bottom: 1.5rem;
            font-size: 2.2rem;
            position: relative;
            display: inline-block;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        .thank-you {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            color: var(--text-light);
        }

        .order-number {
            font-size: 1.5rem;
            margin: 2rem 0;
            color: var(--text-light);
            background: rgba(46, 125, 50, 0.1);
            padding: 1rem;
            border-radius: var(--radius);
            border: 1px solid var(--primary-color);
        }

        .order-number strong {
            color: var(--primary-light);
            font-size: 1.8rem;
        }

        .order-details {
            background: var(--card-hover);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin: 2.5rem 0;
            text-align: left;
            border: var(--border);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #333;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .detail-value {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .total-row {
            font-size: 1.3rem;
            color: var(--primary-light);
            margin-top: 1.5rem;
            padding-top: 1rem;
        }

        .next-steps {
            margin-top: 3rem;
            text-align: left;
            background: var(--card-hover);
            padding: 1.5rem;
            border-radius: var(--radius);
            border: var(--border);
        }

        .next-steps h3 {
            color: var(--primary-light);
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            border-bottom: 1px solid var(--primary-color);
            padding-bottom: 0.5rem;
            display: inline-block;
        }

        .next-steps ul {
            padding-left: 1.5rem;
        }

        .next-steps li {
            margin-bottom: 1rem;
            color: var(--text-light);
            position: relative;
        }

        .next-steps li::before {
            content: '•';
            color: var(--primary-light);
            font-weight: bold;
            display: inline-block;
            width: 1em;
            margin-left: -1em;
            position: absolute;
        }

        .btn-container {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2rem;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.4);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.6);
        }

        .btn-secondary {
            background: #252525;
            color: var(--text-light);
            border: 1px solid #444;
        }

        .btn-secondary:hover {
            background: #2e2e2e;
            transform: translateY(-3px);
        }

        .btn i {
            margin-right: 10px;
        }

        /* Bank details for bank transfer */
        .bank-details {
            background: rgba(46, 125, 50, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-top: 2rem;
            display: <?php echo ($payment_method == 'bank') ? 'block' : 'none'; ?>;
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

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                width: 95%;
            }
            
            .confirmation-card {
                padding: 1.5rem;
            }
            
            .success-icon {
                font-size: 3.5rem;
            }
            
            h1 {
                font-size: 1.8rem;
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
        <div class="confirmation-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>Order Confirmed!</h1>
            
            <div class="thank-you">
                Thank you for your purchase, <?php echo htmlspecialchars($customer_name); ?>!
            </div>
            
            <div class="order-number">
                Your order number is: <strong>#<?php echo htmlspecialchars($order_id); ?></strong>
            </div>
            
            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">
                        <?php 
                        echo htmlspecialchars(
                            $payment_method == 'bank' ? 'Bank Transfer' : 
                            ($payment_method == 'cod' ? 'Cash on Delivery' : 'Unknown')
                        ); 
                        ?>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Order Status:</span>
                    <span class="detail-value">Processing</span>
                </div>
                
                <div class="detail-row total-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">LKR <?php echo htmlspecialchars($formatted_total); ?></span>
                </div>
            </div>
            
            <?php if ($payment_method == 'bank'): ?>
            <div class="bank-details">
                <h3 style="color: var(--primary-light); margin-top: 0;">Bank Transfer Instructions</h3>
                <p>Please complete your payment to the following account details:</p>
                
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
                <div class="bank-detail-item">
                    <span class="bank-detail-label">Reference:</span>
                    <span class="bank-detail-value">TEC-<?php echo htmlspecialchars($order_id); ?></span>
                </div>
                
                <p style="margin-top: 1.5rem;">Your order will be processed once we receive your payment confirmation.</p>
            </div>
            <?php endif; ?>
            
            <div class="next-steps">
                <h3>What happens next?</h3>
                <ul>
                    <?php if ($payment_method == 'bank'): ?>
                        <li>We've sent you an email with our bank details</li>
                        <li>Please complete your payment within 24 hours</li>
                        <li>Send us the payment receipt via email or WhatsApp for faster processing</li>
                    <?php elseif ($payment_method == 'cod'): ?>
                        <li>Your order is being prepared for delivery</li>
                        <li>You'll pay when you receive your order</li>
                        <li>Our delivery agent will contact you to schedule delivery</li>
                    <?php else: ?>
                        <li>Your payment has been received</li>
                        <li>Your order is being processed</li>
                    <?php endif; ?>
                    <li>You'll receive shipping confirmation when your order is dispatched</li>
                    <li>For any questions, contact our support team</li>
                </ul>
            </div>
            
            <div class="btn-container">
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <script>
        // Add any necessary JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            // You can add any client-side functionality here
            console.log('Order confirmation page loaded');
        });
    </script>
</body>
</html>