

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Login</h1>
            <p>Login to access the admin dashboard</p>
        </div>
        <div class="main-content">
            <form action="admin_login.html.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
session_start(); // Start session

require 'db.php'; 

// Login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user is an admin
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    // Verify the password
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id']; // Set admin ID in session
        $_SESSION['is_admin'] = $admin['is_admin']; // Set admin status in session

        echo "Admin login successful!";
        header("Location: index.html.php"); // Redirect to admin dashboard
        exit;
    } else {
        echo "Access denied! Only admins can log in here.";
    }
}
?>