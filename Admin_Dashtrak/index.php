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
            // Query to retrieve client projects with client info
            $sqlProjects = "SELECT CP.ProjectFolder, CP.ProjectDesc, CI.Name, CI.Company
                            FROM ClientProjects CP
                            INNER JOIN ClientInfo CI ON CP.ClientId = CI.ClientId
                            WHERE CP.ProjectId = '$search' OR CP.ClientId = '$search' OR CI.Name LIKE '%$search%'";

            $resultProjects = $conn->query($sqlProjects);

            if ($resultProjects->num_rows > 0) {
                // Output data of each project
                while ($row = $resultProjects->fetch_assoc()) {
                    echo '<div class="project-container">';
                    echo '<strong>Project Folder:</strong> ' . $row["ProjectFolder"] . '<br>';
                    echo '<strong>Project Description:</strong> ' . $row["ProjectDesc"] . '<br>';
                    echo '<strong>Client Name:</strong> ' . $row["Name"] . '<br>';
                    echo '<strong>Client Company:</strong> ' . $row["Company"];
                    echo '</div>';
                }
            } else {
                echo "No projects found";
            }
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
            // Query to retrieve client tickets with client info
            $sqlTickets = "SELECT CT.Ticket, CI.Name, CI.Company
                           FROM ClientTicket CT
                           INNER JOIN ClientInfo CI ON CT.ClientId = CI.ClientId
                           WHERE CT.ClientId = '$search' OR CI.Name LIKE '%$search%'";

            $resultTickets = $conn->query($sqlTickets);

            if ($resultTickets->num_rows > 0) {
                // Output data of each ticket
                while ($row = $resultTickets->fetch_assoc()) {
                    echo '<div class="ticket-container">';
                    echo '<strong>Ticket:</strong> ' . $row["Ticket"] . '<br>';
                    echo '<strong>Client Name:</strong> ' . $row["Name"] . '<br>';
                    echo '<strong>Client Company:</strong> ' . $row["Company"];
                    echo '</div>';
                }
            } else {
                echo "No tickets found";
            }
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
        if (isset($_GET['info_search'])) {
            $search = $_GET['info_search'];
            // Database connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to retrieve client info
            $sqlClientInfo = "SELECT Name, Company, Email, Phone, ClientDesc
                              FROM ClientInfo
                              WHERE ClientId = '$search' OR Name LIKE '%$search%'";

            $resultClientInfo = $conn->query($sqlClientInfo);

            if ($resultClientInfo->num_rows > 0) {
                // Output data of client info
                $row = $resultClientInfo->fetch_assoc();
                echo '<div class="info-container">';
                echo '<strong>Name:</strong> ' . $row["Name"] . '<br>';
                echo '<strong>Company:</strong> ' . $row["Company"] . '<br>';
                echo '<strong>Email:</strong> ' . $row["Email"] . '<br>';
                echo '<strong>Phone:</strong> ' . $row["Phone"] . '<br>';
                echo '<strong>Client Description:</strong> ' . $row["ClientDesc"];
                echo '</div>';
            } else {
                echo "No client found with ID: $search";
            }

            $conn->close();
        }
        ?>
    </div>
</body>
</html>

        
