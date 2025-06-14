<?php
session_start();
require_once 'db_configorder.php';
$totalPrice = 0;
$totalItems = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $product) {
        $totalPrice += $product['price'] * $product['quantity'];
        $totalItems += $product['quantity'];
    }
} else {
    $_SESSION['checkout_error'] = "Your cart is empty. Please add items before checkout.";
    header('Location: cart.php');
    exit();
}

$error_message = isset($_SESSION['checkout_error']) ? $_SESSION['checkout_error'] : '';
unset($_SESSION['checkout_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout - Tecroot</title>
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
            max-width: 1400px;
            margin: 3rem auto;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 3rem;
        }

        .card {
            background: var(--card-color);
            padding: 2.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        h2 {
            margin: 0 0 1.5rem 0;
            color: var(--primary-light);
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 0.8rem;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        form label {
            display: block;
            margin-bottom: 0.8rem;
            font-size: 1rem;
            color: var(--text-light);
        }

        form input,
        form select,
        form textarea {
            width: 100%;
            padding: 1rem;
            border-radius: var(--radius);
            border: var(--border);
            background: #252525;
            color: var(--text-dark);
            font-size: 1rem;
            transition: var(--transition);
        }

        form input:focus,
        form select:focus,
        form textarea:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.3);
        }

        .payment-methods {
            margin: 2.5rem 0 1rem;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 1.2rem;
            margin: 1rem 0;
            border-radius: var(--radius);
            background: #252525;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .payment-option:hover {
            background: #2a2a2a;
            border-color: var(--primary-light);
        }

        .payment-option.active {
            border-color: var(--primary-color);
            background: rgba(46, 125, 50, 0.1);
        }

        .payment-option input[type="radio"] {
            margin-right: 1rem;
            transform: scale(1.2);
        }

        .payment-icon {
            margin-right: 1rem;
            font-size: 1.5rem;
            color: var(--primary-light);
        }

        .bank-details {
            background: rgba(46, 125, 50, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-top: 1rem;
            display: none;
        }

        .bank-detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }

        .bank-detail-label {
            color: var(--text-muted);
        }

        .bank-detail-value {
            font-weight: bold;
        }

        .place-order {
            padding: 1.2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            margin-top: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.4);
        }

        .place-order:hover {
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.6);
        }

        .order-summary {
            background: var(--card-color);
            padding: 2.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
            position: sticky;
            top: 2rem;
            height: fit-content;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #333;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-light);
            margin-top: 1rem;
        }

        .error-message {
            background: #ff4c4c;
            padding: 1.2rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .error-message i {
            font-size: 1.2rem;
        }

        .validation-error {
            color: #ff4c4c;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: none;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card, .order-summary {
            animation: fadeIn 0.6s ease-out forwards;
        }

        /* Media Query for Mobile Devices */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                gap: 2rem;
                margin: 1.5rem auto;
            }

            .card {
                padding: 1.8rem;
            }

            .order-summary {
                position: static;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <?php if ($error_message) : ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <h2>Billing Details</h2>

            <form id="checkoutForm" action="place_order.php" method="post">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Enter your first name">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Enter your last name">
                </div>

                <div class="form-group">
                    <label for="company">Company Name (optional)</label>
                    <input type="text" id="company" name="company" placeholder="Your company name">
                </div>

                <div class="form-group">
                    <label for="country">Country / Region *</label>
                    <select id="country" name="country" required>
                        <option value="" disabled selected>Select your country</option>
                        <option value="Sri Lanka">Sri Lanka</option>
                        <!-- Add more countries as needed -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="street_address">Street Address *</label>
                    <input type="text" id="street_address" name="street_address" required placeholder="House number and street name">
                </div>

                <div class="form-group">
                    <label for="apartment">Apartment, suite, unit, etc. (optional)</label>
                    <input type="text" id="apartment" name="apartment" placeholder="Optional address details">
                </div>

                <div class="form-group">
                    <label for="city">Town / City / District *</label>
                    <input type="text" id="city" name="city" required placeholder="Your city or district">
                </div>

                <div class="form-group">
                    <label for="postcode">Postcode / ZIP *</label>
                    <input type="text" id="postcode" name="postcode" required placeholder="5 digits (e.g., 00100)" 
                           pattern="\d{5}" title="Please enter exactly 5 digits">
                    <div class="validation-error" id="postcode-error">Postcode must be exactly 5 digits</div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" id="phone" name="phone" required 
                           placeholder="+94 77 123 4567" pattern="^\+94\d{9}$" 
                           title="Please enter a valid Sri Lankan phone number starting with +94 followed by 9 digits">
                    <div class="validation-error" id="phone-error">Phone must start with +94 followed by 9 digits (e.g., +94771234567)</div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="Your email address">
                </div>

                <div class="payment-methods">
                    <h2>Payment Method</h2>
                    
                    <label class="payment-option" onclick="toggleBankDetails(true)">
                        <input type="radio" name="payment_method" value="bank" required style="display: none;">
                        <i class="fas fa-university payment-icon"></i>
                        <div>
                            <div style="font-weight: bold;">Bank Transfer</div>
                            <div style="font-size: 0.9rem; color: var(--text-muted);">Direct bank transfer</div>
                        </div>
                    </label>
                    
                    <div id="bankDetails" class="bank-details">
                        <p>Make your payment directly into our bank account. Please use your Order Number as the payment reference. Your order will not be shipped until we have received the payment in our account.</p>
                        
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
                        
                        <p class="mt-2">Our bank details will also be sent via email along with the order notification.</p>
                    </div>
                    
                    <label class="payment-option" onclick="toggleBankDetails(false)">
                        <input type="radio" name="payment_method" value="cod" required style="display: none;">
                        <i class="fas fa-money-bill-wave payment-icon"></i>
                        <div>
                            <div style="font-weight: bold;">Cash on Delivery</div>
                            <div style="font-size: 0.9rem; color: var(--text-muted);">Pay when you receive</div>
                        </div>
                    </label>
                </div>

                <button type="submit" class="place-order">
                    <i class="fas fa-lock" style="margin-right: 10px;"></i> Place Order
                </button>
            </form>
        </div>

        <div class="order-summary">
            <h2>Your Order</h2>
            
            <?php foreach ($_SESSION['cart'] as $id => $product) : ?>
                <div class="order-item">
                    <span><?= htmlspecialchars($product['name']) ?> × <?= $product['quantity'] ?></span>
                    <strong>LKR <?= number_format($product['price'] * $product['quantity'], 2) ?></strong>
                </div>
            <?php endforeach; ?>

            <div style="margin: 1.5rem 0; border-top: 1px solid #333;"></div>

            <div class="order-item">
                <span>Subtotal:</span>
                <span>LKR <?= number_format($totalPrice, 2) ?></span>
            </div>
            
            <div class="order-item">
                <span>Shipping:</span>
                <span>Free</span>
            </div>
            
            <div style="margin: 1.5rem 0; border-top: 1px solid #333;"></div>
            
            <div class="order-item order-total">
                <span>Total:</span>
                <span>LKR <?= number_format($totalPrice, 2) ?></span>
            </div>
        </div>
    </div>

    <script>
        function toggleBankDetails(show) {
            const bankDetails = document.getElementById('bankDetails');
            if (show) {
                bankDetails.style.display = 'block';
            } else {
                bankDetails.style.display = 'none';
            }
        }

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validate postcode (exactly 5 digits)
            const postcode = document.getElementById('postcode');
            const postcodeError = document.getElementById('postcode-error');
            if (!/^\d{5}$/.test(postcode.value)) {
                postcodeError.style.display = 'block';
                postcode.focus();
                isValid = false;
            } else {
                postcodeError.style.display = 'none';
            }
            
            // Validate phone (Sri Lankan format: +94 followed by 9 digits)
            const phone = document.getElementById('phone');
            const phoneError = document.getElementById('phone-error');
            if (!/^\+94\d{9}$/.test(phone.value)) {
                phoneError.style.display = 'block';
                if (isValid) phone.focus(); // Only focus if this is the first error
                isValid = false;
            } else {
                phoneError.style.display = 'none';
            }
            
            if (!isValid) {
                event.preventDefault();
            }
            
            // If form is valid, it will submit normally to place_order.php
        });

        // Add active class to selected payment method
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Real-time validation for postcode
        document.getElementById('postcode').addEventListener('input', function() {
            const errorElement = document.getElementById('postcode-error');
            // Only allow digits and limit to 5 characters
            this.value = this.value.replace(/\D/g, '').slice(0, 5);
            if (this.value.length === 5) {
                errorElement.style.display = 'none';
            }
        });

        // Real-time validation for phone
        document.getElementById('phone').addEventListener('input', function() {
            const errorElement = document.getElementById('phone-error');
            // Remove all non-digit characters
            let value = this.value.replace(/\D/g, '');
            
            // Ensure it starts with +94
            if (value.startsWith('94')) {
                value = '+' + value;
            } else if (!value.startsWith('+94')) {
                value = '+94' + value;
            }
            
            // Limit to 12 characters (+94 + 9 digits)
            value = value.slice(0, 12);
            
            // Update the input value
            this.value = value;
            
            // Hide error if format is correct
            if (/^\+94\d{9}$/.test(this.value)) {
                errorElement.style.display = 'none';
            }
        });
    </script>
</body>
</html>