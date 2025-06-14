<?php
// api_config.php updates
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // Add this line

// Enhanced session handling
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_samesite', 'None');
    ini_set('session.cookie_secure', true); // If using HTTPS
    session_start();
}



require_once 'db_configorder.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function authenticateUser() {
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        jsonResponse(['error' => 'Authorization header missing'], 401);
    }

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        jsonResponse(['error' => 'Authorization header format should be: Bearer [token]'], 401);
    }

    $token = $matches[1];
    // In a real application, validate the JWT token here
    return ['user_id' => $token]; // Placeholder
}

function authenticateAdmin() {
    $user = authenticateUser();
    // Check admin privileges in real app
    return $user;
}
?>