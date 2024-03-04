<?php
session_start();

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create a secure connection using PDO
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement with bound parameters to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM AdminLogin WHERE AdminUser = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Verify password using password_hash
        if (password_verify($password, $row['AdminKey'])) {
            // Admin login successful, set session variables
            $_SESSION['admin_id'] = $row['AdminId'];
            $_SESSION['admin_user'] = $row['AdminUser'];
            // Redirect to admin dashboard or any other page
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Invalid password
            echo "Invalid password";
        }
    } else {
        // Admin user not found
        echo "Admin user not found";
    }
}

// Close the database connection
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form action="" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>


