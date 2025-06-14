<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "tecroot");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // SIGN UP
    if (isset($_POST['signup'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $mobile_number = trim($_POST['phone_number']);
        $location = trim($_POST['location']);

        // Basic validations
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['login_status'] = 'error';
            header("Location: login.php");
            exit();
        }

        if (!preg_match('/^\d{10}$/', $mobile_number)) {
            $_SESSION['login_status'] = 'error';
            header("Location: login.php");
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['login_status'] = 'error';
            header("Location: login.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (full_name, email, password, mobile_number, location) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $full_name, $email, $hashed_password, $mobile_number, $location);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['login_status'] = 'registered';
            header("Location: login.php?tab=signin");
        } else {
            $_SESSION['login_status'] = 'error';
            header("Location: login.php");
        }

        mysqli_stmt_close($stmt);
    }

    // SIGN IN
    elseif (isset($_POST['signin'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['email'] = $row['email'];

                if (str_starts_with($row['email'], "admin")) {
                    $_SESSION['login_status'] = 'success_admin';
                    header("Location: adminIndex.html");
                } elseif (str_starts_with($row['email'], "emp")) {
                    $_SESSION['login_status'] = 'success_employee';
                    header("Location: empIndex.html");
                } else {
                    $_SESSION['login_status'] = 'success_customer';
                    header("Location: products.php");
                }
                exit();
            }
        }

        $_SESSION['login_status'] = 'error';
        header("Location: login.php");
        exit();
    }
}

mysqli_close($conn);
?>
