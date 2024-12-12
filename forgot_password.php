<?php
session_start();
require 'db.php'; // Database connection
require 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

// Step 1: User submits their email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate the random 6-digit reset code
        $reset_code = rand(100000, 999999);

        // Store the reset code and email in the session
        $_SESSION['reset_code'] = $reset_code;
        $_SESSION['reset_email'] = $email;

        // Send the code via PHPMailer
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'webcwwebsite@gmail.com';  // Your Gmail address
            $mail->Password = 'hefd icib sslp hogt';  // App-specific password (use this instead of your Gmail password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('webcwwebsite@gmail.com', 'Web-CW');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body    = "Your password reset code is: <b>$reset_code</b>";

            $mail->send();
            $message = "A reset code has been sent to your email.";

            // Move to step 2 (Enter the code)
            $_SESSION['step'] = 2;
        } catch (Exception $e) {
            $message = "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Can't find any user related to this email.";
    }
}

// Step 2: User submits the reset code
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_code'])) {
    $entered_code = $_POST['reset_code'];

    if ($entered_code == $_SESSION['reset_code']) {
        // Code matches, redirect to the password reset page
        header("Location: change_password.php");
        exit();
    } else {
        $message = "Invalid reset code!";
        $_SESSION['step'] = 2;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <p><?= $message ?></p>
        <br>
        <?php if (!isset($_SESSION['step']) || $_SESSION['step'] == 1): ?>
        <!-- Step 1: Enter email -->
        <form action="forgot_password.php" method="POST">
            <label for="email">Enter your email:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Send Code</button>
        </form>

        <?php elseif ($_SESSION['step'] == 2): ?>
        <!-- Step 2: Enter reset code -->
        <form action="forgot_password.php" method="POST">
            <label for="reset_code">Enter the code sent to your email:</label>
            <input type="text" id="reset_code" name="reset_code" required>
            <button type="submit">Verify Code</button>
        </form>

        <?php endif; ?>
    </div>
</body>
</html>
