<?php
session_start();
require 'db.php'; // Include the database connection

// Handle form submission for creating a new module
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $module_name = $_POST['module_name'];

    // Insert the new module into the database
    $stmt = $pdo->prepare("INSERT INTO modules (module_name) VALUES (?)");
    $stmt->execute([$module_name]);

    // Redirect back to the home page after creating the module
    header("Location: index.html.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Module</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Create a New Module</h1>
        <br>
        <form action="create_module.php" method="POST">
            <label for="module_name">Module Name</label>
            
            <input type="text" id="module_name" name="module_name" required>
            <br><br>
            <button type="submit" class="btn">Create Module</button>
        </form>
    </div>
</body>
</html>
