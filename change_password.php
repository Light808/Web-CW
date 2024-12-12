<?php
session_start();
require 'db.php'; // Database connection

$message = "";

// Check if the session has the reset email and code
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php"); // Redirect back if the session is missing
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // User submits the new password
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'];

    // Update the password in the database
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    if ($stmt->execute([$new_password, $email])) {
        $message = "Password successfully updated! Please log in.";
        session_destroy(); // Clear the session after reset

        // Redirect to login page after 5 seconds
        header("refresh:5;url=login.php");
    } else {
        $message = "Error updating password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Change Password</h1>
        <p><?= $message ?></p>
        <br>
        <?php if (empty($message)): ?> <!-- Show the form only if password hasn't been updated -->
        <form action="change_password.php" method="POST">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
