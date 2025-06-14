<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup - TecRoot</title>
    <link rel="icon" href="2.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /*layout, color and apprence (darker theme)*/
        body {
            background-color: #0a0a0a;
            color: #d4d4d4;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 450px;
            background: #1c1c1c;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 255, 0, 0.3);
        }
        .nav-tabs .nav-link {
            color: #d4d4d4;
            background-color: #0f0f0f;
            border-radius: 5px;
        }
        .nav-tabs .nav-link.active {
            background-color: #00ff00;
            color: black;
        }
        .btn-primary {
            background-color: #00ff00;
            border: none;
            color: black;
            font-weight: bold;
        }
        .btn-primary:hover {
            background-color: #00cc00;
        }
        .btn-success {
            background-color: #007f00;
            border: none;
            color: white;
            font-weight: bold;
        }
        .btn-success:hover {
            background-color: #005f00;
        }
        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #00ff00;
            text-decoration: none;
            font-weight: bold;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
        label {
            color: #d4d4d4;
            font-weight: bold;
        }
        h2 {
            color: white;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">            
        <h2>Welcome to TecRoot</h2>
        <ul class="nav nav-tabs justify-content-center" id="authTabs">  <!-- toogle between signup and signin -->
            <li class="nav-item">
                <a class="nav-link active" id="signup-tab" data-bs-toggle="tab" href="#signup">Sign Up</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="signin-tab" data-bs-toggle="tab" href="#signin">Sign In</a>
            </li>
        </ul>
        <div class="tab-content mt-3">
            <!-- Signup Form -->
            <div class="tab-pane fade show active" id="signup">
                <form action="loginhandler.php" method="POST" id="signupForm" onsubmit="return validateSignupForm()">  <!-- submits the form data into loginhandler.php via POST -->
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="signup">Sign Up</button>
                </form>
            </div>
            <!-- Signin Form -->
            <div class="tab-pane fade" id="signin">
                <form action="loginhandler.php" method="POST" id="signinForm" onsubmit="return validateSigninForm()">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100" name="signin">Sign In</button>
                    <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Alerts -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'signin') {
                document.getElementById("signin-tab").click();
            }

            <?php
            if (isset($_SESSION['login_status'])) {
                $status = $_SESSION['login_status'];
                unset($_SESSION['login_status']);
                echo "var status = '$status';";
                if ($status == 'success_customer') {
                    echo "alert('Successfully logged in! Welcome to TecRoot. Happy shopping!');";
                    echo "window.location.href = 'products.php';";
                } elseif ($status == 'success_employee') {
                    echo "alert('Successfully logged in! Welcome to TecRoot.');";
                    echo "window.location.href = 'empIndex.html';";
                } elseif ($status == 'error') {
                    echo "alert('Invalid login credentials.');";
                } elseif ($status == 'registered') {
                    echo "alert('Registered successfully! Please log in.');";
                    echo "window.location.href = 'login.php?tab=signin';";
                }
            }
            ?>
        });

        function validateEmail(email) {
            const pattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            return pattern.test(email);
        }

        function validatePhone(phone) {
            const pattern = /^\d{10}$/;
            return pattern.test(phone);
        }

        function validateSignupForm() {
            const form = document.getElementById("signupForm");
            const email = form.email.value;
            const password = form.password.value;
            const phone = form.phone_number.value;

            if (!validateEmail(email)) {
                alert("Please enter a valid email (e.g., blank@gmail.com).");
                return false;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters.");
                return false;
            }

            if (!validatePhone(phone)) {
                alert("Phone number must be 10 digits with no letters.");
                return false;
            }

            return true;
        }

        function validateSigninForm() {
            const form = document.getElementById("signinForm");
            const email = form.email.value;
            const password = form.password.value;

            if (!validateEmail(email)) {
                alert("Please enter a valid email.");
                return false;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>