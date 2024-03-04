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
    
    $sqlProjects = "SELECT * FROM ClientProjects WHERE ClientId = ?";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <style>
        .container {
            margin-bottom: 20px;
        }
        .project-container, .ticket-container {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            width: 300px;
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($client_user); ?>!</h1>

    <div class="container">
        <h2>Your Projects</h2>
        <?php if (!empty($resultProjects)): ?>
            <?php foreach ($resultProjects as $project): ?>
                <div class="project-container">
                    <strong>Project ID:</strong> <?php echo htmlspecialchars($project['ProjectId']); ?><br>
                    <strong>Project Folder:</strong> <?php echo htmlspecialchars($project['ProjectFolder']); ?><br>
                    <strong>Project Description:</strong> <?php echo htmlspecialchars($project['ProjectDesc']); ?><br>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            No projects found
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>Create New Project</h2>
        <!-- Form to create a new project -->
        <form action="create_project.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="project_folder">Project Folder:</label>
            <input type="text" id="project_folder" name="project_folder" required><br><br>
            <label for="project_desc">Project Description:</label>
            <textarea id="project_desc" name="project_desc" required></textarea><br><br>
            <button type="submit">Create Project</button>
        </form>
    </div>
    
    <div class="container">
        <h2>Your Tickets</h2>
        <?php if (!empty($resultTickets)): ?>
            <?php foreach ($resultTickets as $ticket): ?>
                <div class="ticket-container">
                    <strong>Ticket ID:</strong> <?php echo htmlspecialchars($ticket['TicketId']); ?><br>
                    <strong>Ticket:</strong> <?php echo htmlspecialchars($ticket['Ticket']); ?><br>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            No tickets found
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>Create New Ticket</h2>
        <!-- Form to create a new ticket -->
        <form action="create_ticket.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="ticket">Ticket:</label>
            <input type="text" id="ticket" name="ticket" required><br><br>
            <button type="submit">Create Ticket</button>
        </form>
    </div>
</body>
</html>

