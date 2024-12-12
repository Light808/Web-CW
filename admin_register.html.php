

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Assuming you have the same style.css file -->
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Register</h1>
            <p>Create a new admin account</p>
        </div>
        <div class="main-content">
            <form action="admin_register.html.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
session_start(); // Start session to manage registration

require 'db.php'; // Include the database connection

// Handle registration logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            echo "Email already exists! Please log in.";
        } else {
            // Insert the new admin into the database with is_admin = 1
            try {
                $stmt = $pdo->prepare("INSERT INTO users (email, password, is_admin) VALUES (?, ?, 1)");
                $stmt->execute([$email, $hashed_password]);

                echo "Admin account created successfully!";

                // Redirect to admin login after successful registration
                header("Location: admin_login.html.php");
                exit;
            } catch (PDOException $e) {
                echo "Error creating account: " . $e->getMessage();
            }
        }
    }
}
?>