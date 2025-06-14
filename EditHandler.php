<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST["btnSubmit"]) && isset($_POST["id"])) {
    $id = $_POST["id"];
    $title = $_POST["txtTitle"];
    $price = $_POST["txtPrice"];
    $publish = isset($_POST["txtPublish"]) ? 1 : 0;

    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "tecroot");
    if (!$conn) {
        die("DB connection failed: " . mysqli_connect_error());
    }

    // Prevent SQL Injection
    $id = mysqli_real_escape_string($conn, $id);
    $title = mysqli_real_escape_string($conn, $title);
    $price = mysqli_real_escape_string($conn, $price);

    // Check for new image upload
    if (!empty($_FILES["imageFile"]["name"])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file_name = basename($_FILES["imageFile"]["name"]);
        $file_path = $uploadDir . $file_name;

        if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $file_path)) {
            $file_path = mysqli_real_escape_string($conn, $file_path);
            $sql = "UPDATE advertisement SET Product_Name='$title', Price='$price', Image_Path='$file_path', Publish='$publish' WHERE id='$id'";
        } else {
            echo "Error uploading the file.";
            exit();
        }
    } else {
        $sql = "UPDATE advertisement SET Product_Name='$title', Price='$price', Publish='$publish' WHERE id='$id'";
    }

    // Execute SQL query
    if (mysqli_query($conn, $sql)) {
        header("Location: EditAdvertisements.php?success=1");
        exit();
    } else {
        echo "Error updating advertisement: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
