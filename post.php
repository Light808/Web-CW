<?php
require 'db.php'; // Include database connection

session_start(); // Start session to access user session data

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and get the form input data
    $title = $_POST['title'] ?? '';
    $module = $_POST['module'] ?? '';
    $content = $_POST['content'] ?? '';

    // Retrieve the user_id from the session (assuming the user is logged in)
    if (!isset($_SESSION['user_id'])) {
        echo "User not logged in.";
        exit;
    }
    $user_id = $_SESSION['user_id'];

    // Validate required fields
    if (empty($title)) {
        $error_message = "You need to fill in the title.";
    } elseif (empty($module)) {
        $error_message = "You need to choose a module.";
    } elseif (empty($content)) {
        $error_message = "You need to fill in the content.";
    } else {
        // Handle the image upload (optional)
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileType = $_FILES['image']['type'];

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (in_array($fileType, $allowedTypes)) {
                $destination = 'uploads/' . basename($fileName);

                if (!file_exists('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $imagePath = $destination;
                } else {
                    $error_message = "Failed to upload image.";
                }
            } else {
                $error_message = "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
            }
        }

        // If no validation errors, insert post into the database
        if (!isset($error_message)) {
            try {
                // Prepare the SQL query with the user_id
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, module_id, content, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $title, $module, $content, $imagePath]);
                echo "Post successfully saved!";
                header("Location: home.html.php");
                exit;
            } catch (PDOException $e) {
                $error_message = "Error saving post: " . $e->getMessage();
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Article</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<script>
function previewImage(event) {
    var file = event.target.files[0];
    var reader = new FileReader();

    reader.onload = function() {
        var output = document.getElementById('image-preview');
        output.src = reader.result;
        output.style.display = 'block'; // Show the preview image
    };

    if (file) {
        reader.readAsDataURL(file); // Convert the image file to a data URL
    }
}
</script>

<body>
    <div class="container">
        <header class="header">
            <h1>Post Your Article</h1>
            <p>Share your thoughts with others</p>
        </header>

        <div class="main-content">
            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <form action="post.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                </div>

                <div class="input-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="10" cols="50" required><?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                </div>

                <label for="image-upload" class="upload-btn"><u>Upload Image</u></label>
                <input type="file" id="image-upload" name="image" accept="image/*" style="display: none;" onchange="previewImage(event)">
                <br>
                <img id="image-preview" src="" alt="Image Preview" style="display: none; width: 200px; margin-top: 10px;">
                <br>
                <div class="input-group">
                    <label for="module">Module</label>
                    <select id="module" name="module" required>
                        <option value="">Select a Module</option>
                        <?php
                        // Fetch modules from the database
                        $stmt = $pdo->query("SELECT id, module_name FROM modules");
                        while ($module = $stmt->fetch()) {
                            echo "<option value='" . htmlspecialchars($module['id']) . "'>" . htmlspecialchars($module['module_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn">Post Article</button>
            </form>
        </div>
    </div>
</body>
</html>
