<?php
// Include the database connection file
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the email and password are set
    if (isset($_POST['email'], $_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare the SQL statement using PDO (use $pdo instead of $db)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        
        // Bind the parameters using PDO's bindParam() method
        $stmt->bindParam(1, $email, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];  // Add this line to set the admin flag

            // Redirect to homepage
            header("Location: Home.html.php");
            exit();
        } else {
            echo "Invalid email or password!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color:aqua;"><big><big> Free2Ask</big></big> </h1>
            <Br>
            <h1>Login to Your Account</h1>
            <p>Welcome back, please login to access your account.</p>
        </div>
        <div class="main-content">
            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Your email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn">Login</button>
                <p><a href="forgot_password.php" style="color: white;"> Forget your Password ?</a></p>
                <p class="signup-link">Don't have an account? <a href="register.html">Sign up</a></p>
            </form>
        </div>
    </div>
</body>
</html>
