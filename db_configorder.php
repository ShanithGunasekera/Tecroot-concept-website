<?php
// db_configorder.php
function getDBConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "tecroot";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>