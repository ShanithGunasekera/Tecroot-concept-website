<?php
$conn = mysqli_connect("localhost", "root", "", "tecroot");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $new_password = $_POST["new_password"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } elseif (strlen($new_password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long.');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Password reset successful. Please login.'); window.location.href='login.php?tab=signin';</script>";
        } else {
            echo "<script>alert('Error updating password. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - TecRoot</title>
    <link rel="icon" href="2.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0a0a0a;
            color: #d4d4d4;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 400px;
            margin-top: 100px;
            background: #1c1c1c;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 255, 0, 0.3);
        }
        .btn {
            background-color: #00ff00;
            color: black;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #00cc00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center mb-4">Reset Password</h3>
        <form method="POST" onsubmit="return validateResetForm()">
            <div class="mb-3">
                <label>Email address</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>
            <button type="submit" class="btn w-100">Reset Password</button>
        </form>
    </div>

    <script>
    function validateResetForm() {
        const email = document.querySelector('input[name="email"]').value.trim();
        const password = document.querySelector('input[name="new_password"]').value;

        const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
        if (!emailRegex.test(email)) {
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