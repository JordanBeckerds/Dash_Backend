<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['client_user'])) {
    header("Location: login_page.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

// Get client username
$client_user = $_SESSION['client_user'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve client info
$sqlClientInfo = "SELECT * FROM ClientInfo WHERE ClientUser = ?";
$stmt = $conn->prepare($sqlClientInfo);
$stmt->bind_param("s", $client_user);
$stmt->execute();
$resultClientInfo = $stmt->get_result();

if ($resultClientInfo->num_rows == 1) {
    $clientInfo = $resultClientInfo->fetch_assoc();
    $clientId = $clientInfo['ClientId'];
}

$stmt->close();

// Retrieve client projects
$sqlProjects = "SELECT * FROM ClientProjects WHERE ClientId = ?";
$stmt = $conn->prepare($sqlProjects);
$stmt->bind_param("i", $clientId);
$stmt->execute();
$resultProjects = $stmt->get_result();

// Retrieve client tickets
$sqlTickets = "SELECT * FROM ClientTicket WHERE ClientId = ?";
$stmt = $conn->prepare($sqlTickets);
$stmt->bind_param("i", $clientId);
$stmt->execute();
$resultTickets = $stmt->get_result();

$conn->close();
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
        <?php
        if ($resultProjects->num_rows > 0) {
            while ($row = $resultProjects->fetch_assoc()) {
                echo '<div class="project-container">';
                echo '<strong>Project ID:</strong> ' . htmlspecialchars($row["ProjectId"]) . '<br>';
                echo '<strong>Project Folder:</strong> ' . htmlspecialchars($row["ProjectFolder"]) . '<br>';
                echo '<strong>Project Description:</strong> ' . htmlspecialchars($row["ProjectDesc"]) . '<br>';
                echo '</div>';
            }
        } else {
            echo "No projects found";
        }
        ?>
    </div>

    <div class="container">
        <h2>Your Tickets</h2>
        <?php
        if ($resultTickets->num_rows > 0) {
            while ($row = $resultTickets->fetch_assoc()) {
                echo '<div class="ticket-container">';
                echo '<strong>Ticket ID:</strong> ' . htmlspecialchars($row["TicketId"]) . '<br>';
                echo '<strong>Ticket:</strong> ' . htmlspecialchars($row["Ticket"]) . '<br>';
                echo '</div>';
            }
        } else {
            echo "No tickets found";
        }
        ?>
    </div>

    <div class="container">
        <h2>Create New Project</h2>
        <!-- Form to create a new project -->
        <form action="create_project.php" method="post">
            <label for="project_folder">Project Folder:</label>
            <input type="text" id="project_folder" name="project_folder" required><br><br>
            <label for="project_desc">Project Description:</label>
            <textarea id="project_desc" name="project_desc" required></textarea><br><br>
            <button type="submit">Create Project</button>
        </form>
    </div>

    <div class="container">
        <h2>Create New Ticket</h2>
        <!-- Form to create a new ticket -->
        <form action="create_ticket.php" method="post">
            <label for="ticket">Ticket:</label>
            <input type="text" id="ticket" name="ticket" required><br><br>
            <button type="submit">Create Ticket</button>
        </form>
    </div>
</body>
</html>

