<?php
session_start();

function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function connectToDB() {
    // Replace with your database credentials
    $host = 'your_host';
    $db = 'your_database';
    $user = 'your_username';
    $pass = 'your_password';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function insertClientLogin($client, $clientKey) {
    $pdo = connectToDB();
    $stmt = $pdo->prepare("INSERT INTO ClientLogin (Client, ClientKey) VALUES (:client, :clientKey)");
    $stmt->bindParam(':client', $client);
    $stmt->bindParam(':clientKey', $clientKey);
    $stmt->execute();
}

function getClientId($client) {
    $pdo = connectToDB();
    $stmt = $pdo->prepare("SELECT ClientId FROM ClientLogin WHERE Client = :client");
    $stmt->bindParam(':client', $client);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['ClientId'];
}

function insertClientInfo($clientId, $name, $company, $email, $phone, $clientDesc) {
    $pdo = connectToDB();
    $stmt = $pdo->prepare("INSERT INTO ClientInfo (ClientId, Name, Company, Email, Phone, ClientDesc) VALUES (:clientId, :name, :company, :email, :phone, :clientDesc)");
    $stmt->bindParam(':clientId', $clientId);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':clientDesc', $clientDesc);
    $stmt->execute();
}

function insertClientProject($clientId) {
    $pdo = connectToDB();
    $stmt = $pdo->prepare("INSERT INTO ClientProject (ClientId, ProjectName, ProjectDesc) VALUES (:clientId, 'Nouveau Projet', 'Ajouter une description')");
    $stmt->bindParam(':clientId', $clientId);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }

    $client = $_POST['client'];
    $clientKey = $_POST['clientKey'];
    $name = $_POST['name'];
    $company = $_POST['company'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $clientDesc = $_POST['clientDesc'];

    $pdo = connectToDB();
    $stmt = $pdo->prepare("SELECT * FROM ClientInfo WHERE Company = :company OR Email = :email OR Phone = :phone");
    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        die("Company name, email, or phone number already exists.");
        // header("Location: fail.php");
    }

    insertClientLogin($client, $clientKey);
    $clientId = getClientId($client);
    insertClientInfo($clientId, $name, $company, $email, $phone, $clientDesc);
    insertClientProject($clientId);
    // Redirect or show success message
    // header("Location: success.php");
    // exit();
}
?>
