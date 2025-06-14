<?php
session_start();

// Hardcoded admin email
$admin_email = "admin___@gmail.com";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    if ($email === $admin_email) {
        $_SESSION["admin"] = $email;
        header("Location: adminIndex.html");
        exit();
    } else {
        echo "<script>alert('Invalid admin email');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Admin Login</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Admin Email</label>
            <input type="email" name="email" class="form-control" required placeholder="admin___@gmail.com">
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</body>
</html>
