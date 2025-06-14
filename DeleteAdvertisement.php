<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "tecroot");

if (!$conn) {
    die("DB connection failed: " . mysqli_connect_error());
}

$add_id = $_GET['Add_ID'];

if (isset($add_id)) {
    $sql = "DELETE FROM advertisement WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $add_id);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: ViewMyAdvertisements.php");
            exit();
        } else {
            echo "Error executing query: " . mysqli_error($conn);
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
} else {
    echo "Invalid advertisement ID.";
}

mysqli_close($conn);
?>
