<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Form</title>
</head>
<body>
    <h2>Request Form</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>

        <label for="country">Country:</label><br>
        <input type="text" id="country" name="country" required><br>

        <label for="firstname">First Name:</label><br>
        <input type="text" id="firstname" name="firstname" required><br>

        <label for="lastname">Last Name:</label><br>
        <input type="text" id="lastname" name="lastname" required><br>

        <label for="company">Company:</label><br>
        <input type="text" id="company" name="company"><br>

        <label for="request">Request:</label><br>
        <input type="text" id="request" name="request" required><br>

        <input type="submit" value="Submit">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $email = $_POST['email'];
        $country = $_POST['country'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $company = isset($_POST['company']) ? $_POST['company'] : '';
        $request = $_POST['request'];

        // Email information
        $to = "mateo@gmail.com"; // ton email
        $subject = "Requête de site";
        $body = "Email: $email\nCountry: $country\nFirst Name: $firstname\nLast Name: $lastname\nCompany: $company\nRequest: $request";

        // Send email
        if (mail($to, $subject, $body)) {
            echo "Request sent successfully!";
        } else {
            echo "Failed to make request. Please try again later.";
        }
    }
    ?>
</body>
</html>
