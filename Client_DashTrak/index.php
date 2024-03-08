<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['client_user'])) {
    header("Location: login_page.php"); // Redirect to login page if not logged in
    exit();
}

// Include a separate configuration file containing sensitive information
require_once 'config.php';

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed!");
    }
}

// Get client username securely
$client_user = $_SESSION['client_user'];

// Create a PDO connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$sqlClientInfo = "SELECT * FROM ClientInfo WHERE ClientUser = ?";
$stmt = $conn->prepare($sqlClientInfo);
$stmt->execute([$client_user]);
$clientInfo = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

if ($clientInfo) {
    $clientId = $clientInfo['ClientId'];
    
    $sqlProjects = "SELECT * FROM ClientProject WHERE ClientId = ?";
    $stmt = $conn->prepare($sqlProjects);
    $stmt->execute([$clientId]);
    $resultProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $sqlTickets = "SELECT * FROM ClientTicket WHERE ClientId = ?";
    $stmt = $conn->prepare($sqlTickets);
    $stmt->execute([$clientId]);
    $resultTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
}

// Close the database connection
$conn = null;
?>


