<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepare the SQL statement using placeholders
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

    // Execute the query and pass the parameters
    if ($stmt->execute([$username, $email, $password])) {
        echo "User registered successfully!";
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . $stmt->errorInfo()[2]; // Show error message if something goes wrong
    }
}
