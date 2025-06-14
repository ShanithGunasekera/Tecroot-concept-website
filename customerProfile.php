<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "tecroot";

$conn = mysqli_connect("localhost", "root", "", "tecroot");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];

// Fetch user details
$query = "SELECT full_name, email, mobile_number, location, profile_picture FROM users WHERE email = ?";
$stmt = $conn->prepare($query);//prepare the SQL query for execution
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle Account Deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $delete_query = "DELETE FROM users WHERE email = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("s", $user_email);

    if ($delete_stmt->execute()) {
        session_destroy();
        echo "<script>alert('Your account has been deleted.'); window.location='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to delete account. Please try again later.');</script>";
    }

    $delete_stmt->close();
}

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_account'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $location = trim($_POST['location']);
    
    // Handle Profile Picture Upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {//checking if a profile pic is added
        $image_name = $_FILES['profile_picture']['name'];//file name
        $image_tmp = $_FILES['profile_picture']['tmp_name'];//temporary file path
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);//extract the file extension
        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        if (in_array(strtolower($image_extension), $allowed_extensions)) {
            $image_new_name = uniqid() . '.' . $image_extension;//provide a unique filename to prevent overwriting
            $image_path = 'uploads/' . $image_new_name;
            move_uploaded_file($image_tmp, $image_path);//move the file from the temporary location to uploads
        } else {
            echo "<script>alert('Invalid image format. Please upload jpg, jpeg, or png files.');</script>";
        }
    } else {
        $image_path = $user['profile_picture']; 
    }

    // Update user details in the database
    $update_query = "UPDATE users SET full_name=?, mobile_number=?, location=?, profile_picture=? WHERE email=?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssss", $name, $phone, $location, $image_path, $user_email);

    if ($update_stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location='customerProfile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - TecRoot</title>
    <!-- Favicon -->
    <link rel="icon" href="2.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #28a745, #ffffff, #000000);
            color: #ffffff;
        }
        .profile-container {
            background-color: #ffffff;
            color: #000000;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-green {
            background-color: #28a745;
            color: white;
        }
        .btn-green:hover {
            background-color: #218838;
        }
        #logout-confirmation {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        #confirmation-box h5 {
            color: black;
        }
        #confirmation-box {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        input[type="text"], input[type="tel"], input[type="email"], input[type="file"] {
            color: black;
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg" style="background-color: #198754;">
    <div class="container">
        <a class="navbar-brand" href="empIndex.html"><img src="1.png" alt="Tecroot Logo" height="30"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="home.html"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-shopping-bag"></i> Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="aboutus.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li class="nav-item"><a class="nav-link" href="Contactpage.html"><i class="fas fa-envelope"></i> Contact</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-end">
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <img src="<?= $user['profile_picture'] ? $user['profile_picture'] : 'https://via.placeholder.com/40' ?>" class="rounded-circle me-2 profile-picture" alt="Profile"> 
                <?= htmlspecialchars($user['full_name'] ?? 'User') ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item text-danger" href="#" onclick="showLogoutConfirmation()"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
            </ul>
        </div>
    </div>

    <div class="profile-container mx-auto mt-4">
        <h4 class="text-center"><i class="bi bi-person-circle"></i> User Profile</h4>
        <div class="text-center">
            <img src="<?= $user['profile_picture'] ? $user['profile_picture'] : 'https://via.placeholder.com/80' ?>" class="rounded-circle mb-3 profile-picture" alt="Profile Picture">
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <label class="form-label"><i class="bi bi-person"></i> Full Name</label>
                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
            </div>
            <div class="mb-2">
                <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
            </div>
            <div class="mb-2">
                <label class="form-label"><i class="bi bi-telephone"></i> Mobile Number</label>
                <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($user['mobile_number'] ?? '') ?>" required>
            </div>            
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-geo-alt"></i> Location</label>
                <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($user['location'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-image"></i> Profile Picture</label>
                <input type="file" class="form-control" name="profile_picture">
            </div>
            <button type="submit" class="btn btn-green w-100"><i class="bi bi-save"></i> Save Changes</button>
        </form>

        <hr>
        <!-- Delete Account Button -->
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
            <input type="hidden" name="delete_account" value="1">
            <button type="submit" class="btn btn-danger w-100"><i class="bi bi-trash"></i> Delete My Account</button>
        </form>
        <?php if (str_starts_with($user_email, 'emp')): ?>
    <div class="container mt-3">
        <div class="text-end">
            <a href="user_report.php" class="btn btn-success">
                <i class="fas fa-file-pdf me-2"></i> Generate User Report
            </a>
        </div>
    </div>
<?php endif; ?>
    </div>
 
</div>

<!-- Logout Confirmation Modal -->
<div id="logout-confirmation">
    <div id="confirmation-box">
        <h5>Are you sure you want to log out?</h5>
        <button class="btn btn-success" onclick="window.location.href='login.php'">Yes</button>
        <button class="btn btn-danger" onclick="closeLogoutConfirmation()">No</button>
    </div>
</div>

<!-- Footer -->
<footer class="text-center py-4" style="background-color: #198754; color: white;">
    <p>&copy; 2025 TecRoot. All rights reserved.</p>
    <p><a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms of Service</a></p>
</footer>

<script>
    function showLogoutConfirmation() {
        document.getElementById('logout-confirmation').style.display = 'flex';
    }

    function closeLogoutConfirmation() {
        document.getElementById('logout-confirmation').style.display = 'none';
    }
</script>

</body>
</html>