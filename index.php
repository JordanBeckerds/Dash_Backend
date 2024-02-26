<?php
session_start();

// Database connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

// Get input data
$client_user = $_POST['client_user'];
$client_key = $_POST['client_key'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement
$sql = "SELECT * FROM ClientLogin WHERE ClientUser = ? AND ClientKey = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $client_user, $client_key);

// Execute SQL statement
$stmt->execute();

// Get result
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows == 1) {
    // User exists, redirect to dashboard or another page
    $_SESSION['client_user'] = $client_user; // Store username in session
    header("Location: dashboard.php"); // Redirect to dashboard
} else {
    // User does not exist, redirect back to login page with error message
    $_SESSION['login_error'] = "Invalid username or password";
    header("Location: login_page.php"); // Remplace avec le href de ta page html pour le login
}

$stmt->close();
$conn->close();
?>
