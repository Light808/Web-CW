<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Contact Me</h1>
            <p>I would love to hear from you! Reach out to us with any questions or comments.</p>
        </div>

        <div class="avatar-button-container">
            <a href="profile.php">
                <img src="<?php echo htmlspecialchars($user['avatar'] ? $user['avatar'] : 'default-avatar.jpg'); ?>" alt="Avatar" class="avatar-button">
            </a>
        </div>

        <div class="navigation">
            <a href="Home.html.php" class="btn">Home</a>
            <a href="about.html" class="btn">About</a>
            <a href="service.html" class="btn">Services</a>
            <a href="contact.html.php" class="btn">Contact</a>
        </div>

        <div class="main-content">
            <h2>Contact with me</h2>
            <br>
            <!-- Button to open Gmail compose window -->
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=webcwwebsite@gmail.com" class="btn" target="_blank">Send a Message via Gmail</a>
        </div>
    </div>
</body>
</html>
