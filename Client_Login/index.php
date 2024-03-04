<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection (replace with your database credentials)
    $servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "database";

    // Get input data
    $client_user = $_POST['client_user'];
    $client_key = $_POST['client_key'];

    // Create connection using PDO (secure method)
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("SELECT * FROM ClientLogin WHERE ClientUser = :client_user");
        $stmt->execute(['client_user' => $client_user]);

        // Fetch user from database
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($client_key, $user['ClientKey'])) {
            // Password is correct, redirect to dashboard or another page
            $_SESSION['client_user'] = $client_user; // Store username in session
            header("Location: dashboard.php"); // Redirect to dashboard
            exit;
        } else {
            // Invalid username or password
            $login_error = "Invalid username or password";
        }
    } catch(PDOException $e) {
        // Error handling
        die("Connection failed: " . $e->getMessage());
    }
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
        <p style="color: red;"><?php echo htmlspecialchars($login_error); ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="client_user">Username:</label>
        <input type="text" id="client_user" name="client_user" required><br><br>
        <label for="client_key">Password:</label>
        <input type="password" id="client_key" name="client_key" required><br><br>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
        <button type="submit">Login</button>
    </form>
</body>
</html>

<?php
// Function to generate CSRF token
function generateCSRFToken() {
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes(32));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes(32));
    } else {
        return uniqid();
    }
}
?>
