<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['client_user'])) {
    header("Location: login_page.php"); // Redirect to login page if not logged in
    exit();
}

// Include a separate configuration file containing sensitive information
require_once 'config.php';

// Get the project ID from the query parameter
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];
} else {
    // Redirect to an error page if project ID is not provided
    header("Location: error_page.php");
    exit();
}

// Create a PDO connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Query to fetch project details
$sqlProjectDetails = "SELECT * FROM ClientProject WHERE ProjectId = ?";
$stmt = $conn->prepare($sqlProjectDetails);
$stmt->execute([$project_id]);
$projectDetails = $stmt->fetch(PDO::FETCH_ASSOC);

// Close the database connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
</head>
<body>
    <h1>Project Details</h1>
    <?php if ($projectDetails): ?>
        <p><strong>Project ID:</strong> <?php echo htmlspecialchars($projectDetails['ProjectId']); ?></p>
        <p><strong>Project Name:</strong> <?php echo htmlspecialchars($projectDetails['ProjectName']); ?></p>
        <p><strong>Project Folder:</strong> <?php echo htmlspecialchars($projectDetails['ProjectFolder']); ?></p>
        <p><strong>Project Description:</strong> <?php echo htmlspecialchars($projectDetails['ProjectDesc']); ?></p>
        <!-- Add more details here as needed -->
    <?php else: ?>
        <p>No project found with the provided ID</p>
    <?php endif; ?>
</body>
</html>
