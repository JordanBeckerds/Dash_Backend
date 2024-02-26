<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        // User does not exist, set error message
        $login_error = "Invalid username or password";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Login</title>
</head>
<body>
    <h2>Client Login</h2>
    <?php if(isset($login_error)): ?>
        <p style="color: red;"><?php echo $login_error; ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="client_user">Username:</label>
        <input type="text" id="client_user" name="client_user" required><br><br>
        <label for="client_key">Password:</label>
        <input type="password" id="client_key" name="client_key" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>

