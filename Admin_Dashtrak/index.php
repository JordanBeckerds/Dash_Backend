<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Projects, Tickets, and Info</title>
    <style>
        .container {
            margin-bottom: 20px;
        }
        .project-container, .ticket-container, .info-container {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            width: 300px;
        }
    </style>
</head>
<body>
    <h1>Client Projects, Tickets, and Info</h1>

    <div class="container">
        <h2>Client Projects</h2>
        <form action="" method="get">
            <label for="project_search">Search by Project ID, Client ID, or Name:</label>
            <input type="text" id="project_search" name="project_search">
            <button type="submit">Search</button>
        </form>
        <?php
        // Database connection
        $servername = "localhost";
        $username = "username";
        $password = "password";
        $dbname = "database";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (isset($_GET['project_search'])) {
            $search = $_GET['project_search'];
            // Prepare and bind parameters
            $sqlProjects = "SELECT CP.ProjectFolder, CI.Name, CI.Company
                            FROM ClientProjects CP
                            INNER JOIN ClientInfo CI ON CP.ClientId = CI.ClientId
                            WHERE CP.ProjectId = ? OR CP.ClientId = ? OR CI.Name LIKE ?";
            $stmt = $conn->prepare($sqlProjects);
            $stmt->bind_param("iss", $search, $search, $search);
            $stmt->execute();
            $resultProjects = $stmt->get_result();

            if ($resultProjects->num_rows > 0) {
                // Output data of each project
                while ($row = $resultProjects->fetch_assoc()) {
                    echo '<div class="project-container">';
                    echo '<strong>Project Folder:</strong> ' . htmlspecialchars($row["ProjectFolder"]) . '<br>';
                    echo '<strong>Client Name:</strong> ' . htmlspecialchars($row["Name"]) . '<br>';
                    echo '<strong>Client Company:</strong> ' . htmlspecialchars($row["Company"]);
                    echo '</div>';
                }
            } else {
                echo "No projects found";
            }
            $stmt->close();
        }
        ?>
    </div>

    <div class="container">
        <h2>Client Tickets</h2>
        <form action="" method="get">
            <label for="ticket_search">Search by Client ID or Name:</label>
            <input type="text" id="ticket_search" name="ticket_search">
            <button type="submit">Search</button>
        </form>
        <?php
        // Database connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (isset($_GET['ticket_search'])) {
            $search = $_GET['ticket_search'];
            // Prepare and bind parameters
            $sqlTickets = "SELECT CT.Ticket, CI.Name, CI.Company
                           FROM ClientTicket CT
                           INNER JOIN ClientInfo CI ON CT.ClientId = CI.ClientId
                           WHERE CT.ClientId = ? OR CI.Name LIKE ?";
            $stmt = $conn->prepare($sqlTickets);
            $stmt->bind_param("is", $search, $search);
            $stmt->execute();
            $resultTickets = $stmt->get_result();

            if ($resultTickets->num_rows > 0) {
                // Output data of each ticket
                while ($row = $resultTickets->fetch_assoc()) {
                    echo '<div class="ticket-container">';
                    echo '<strong>Ticket:</strong> ' . htmlspecialchars($row["Ticket"]) . '<br>';
                    echo '<strong>Client Name:</strong> ' . htmlspecialchars($row["Name"]) . '<br>';
                    echo '<strong>Client Company:</strong> ' . htmlspecialchars($row["Company"]);
                    echo '</div>';
                }
            } else {
                echo "No tickets found";
            }
            $stmt->close();
        }
        $conn->close();
        ?>
    </div>

    <div class="container">
        <h2>Search Client Info</h2>
        <form action="" method="get">
            <label for="info_search">Search by Client ID or Name:</label>
            <input type="text" id="info_search" name="info_search">
            <button type="submit">Search</button>
        </form>
        <?php
        if (isset($_GET['info_search']))
        {
            $search = $_GET['info_search'];
            // Database connection
            $conn = new mysqli($servername, $username, $password, $dbname);
        
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
        
            // Prepare and bind parameters
            $sqlClientInfo = "SELECT Name, Company, Email, Phone
                              FROM ClientInfo
                              WHERE ClientId = ? OR Name LIKE ?";
            $stmt = $conn->prepare($sqlClientInfo);
            $stmt->bind_param("is", $search, $search);
            $stmt->execute();
            $resultClientInfo = $stmt->get_result();
        
            if ($resultClientInfo->num_rows > 0) {
                // Output data of client info
                while ($row = $resultClientInfo->fetch_assoc()) {
                    echo '<div class="info-container">';
                    echo '<strong>Name:</strong> ' . htmlspecialchars($row["Name"]) . '<br>';
                    echo '<strong>Company:</strong> ' . htmlspecialchars($row["Company"]) . '<br>';
                    echo '<strong>Email:</strong> ' . htmlspecialchars($row["Email"]) . '<br>';
                    echo '<strong>Phone:</strong> ' . htmlspecialchars($row["Phone"]);
                    echo '</div>';
                }
            } else {
                echo "No client found with ID or Name: $search";
            }
            $stmt->close();
            $conn->close();
        }
        ?>
    </div>
</body>
</html>
        