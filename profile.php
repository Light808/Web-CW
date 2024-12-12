<?php
session_start();
require 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle form submission for updating username, password, and avatar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Update username
    if ($username != $user['username']) {
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE user_id = ?");
        $stmt->execute([$username, $user_id]);
        
        // Update the session variable with the new username
        $_SESSION['username'] = $username; // <-- Added line to update the session
    }

    // Update password (if provided)
    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->execute([$hashed_password, $user_id]);
    }

    // Handle avatar upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileType = $_FILES['avatar']['type'];

        // Allowed file types for avatar
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($fileType, $allowedTypes)) {
            $avatarPath = 'uploads/avatars/' . basename($fileName);

            // Ensure the 'uploads/avatars/' directory exists
            if (!file_exists('uploads/avatars')) {
                mkdir('uploads/avatars', 0777, true);
            }

            // Move the uploaded file
            if (move_uploaded_file($fileTmpPath, $avatarPath)) {
                // Update avatar in the database
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE user_id = ?");
                $stmt->execute([$avatarPath, $user_id]);
            } else {
                echo "Failed to upload avatar.";
            }
        } else {
            echo "Invalid avatar file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Redirect to profile page after update
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .h1 {
            text-align: center;
            display: flex;
        }
        .avatar-section {
            text-align: center;
        }
        /* Optional styling for the avatar preview */
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ccc;
        }
    </style>
    <script>
        // Function to preview the selected avatar before uploading
        function previewAvatar(event) {
            var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('avatar-preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Update Your Profile</h1>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <!-- Avatar Section -->
            <div class="avatar-section">
                <h3>Your Avatar</h3>
                <!-- Display the avatar if it exists, otherwise show a default avatar -->
                <img src="<?php echo htmlspecialchars($user['avatar'] ? $user['avatar'] : 'default-avatar.jpg'); ?>" alt="User Avatar" class="avatar" id="avatar-preview">
                <br>
                <!-- Set Avatar Link -->
                <label for="avatar-upload" class="set-avatar-link" style="cursor: pointer; color: #2a90e2;"><u>Set Avatar</u></label>
                <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(event)">
            </div>

            <!-- Username Field -->
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= $_SESSION['username']; ?>" required>
            </div>

            <!-- Password Field -->
            <div class="input-group">
                <label for="password">New Password (Leave blank to keep current)</label>
                <input type="password" id="password" name="password">
            </div>

            <!-- Confirm Button -->
            <button type="submit" class="btn">Confirm Changes</button>
        </form>

        <br><br>
        <a href="home.html.php" class="btn" style="text-decoration: none;">Back to Home</a>
        <br><br>
    </div>

    <script>
        // Trigger file input click when "Set Avatar" is clicked
        document.querySelector('.set-avatar-link').addEventListener('click', function() {
            document.getElementById('avatar-upload').click();
        });
    </script>
</body>
</html>
