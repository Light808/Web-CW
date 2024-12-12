<?php
// Include the database connection (db.php)
require 'db.php'; // Ensure this file is included at the top
session_start(); // Add session start to ensure session data is available

try {
    // Fetch posts from the database using the correct variable ($pdo)
    $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC");
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error fetching posts: " . $e->getMessage();
}

$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch current user data from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

// Fetch modules from the database
$module_stmt = $pdo->query("SELECT id, module_name FROM modules");
$modules = $module_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Posts</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .module-container {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        .btn-module {
            padding: 10px 25px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            background: linear-gradient(135deg, #ff7e00, #ff1c00);
            border-radius: 15px;
            margin-right: 10px;
            cursor: pointer;
        }

        .btn-module.active {
            background: linear-gradient(135deg, #b35900, #931404);
        }

        .post-card {
            background-color: #f5f5f5;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .post-card img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .avatar-button-container {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 50px;
            height: 50px;
        }

        .avatar-button-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%; /* Make the image circular */
            object-fit: cover; /* Ensure the image covers the button */
            cursor: pointer; /* Make it clickable */
            border: 2px solid white; /* Optional: add a border */
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Our Website</h1>
            <p>Here are the latest posts:</p>
        </div>

        <!-- Module Selector -->
        <div class="module-container" id="module-container">
            <?php foreach ($modules as $module): ?>
                <button class="btn-module" data-id="<?= $module['id']; ?>">
                    <?= htmlspecialchars($module['module_name']); ?>
                </button>
            <?php endforeach; ?>
            <a href="create_module.php"><button class="btn-module">+</button></a>
        </div>

        <!-- Avatar Button -->
        <div class="avatar-button-container">
            <a href="profile.php">
                <!-- Dynamically show the avatar from the database -->
                <img src="<?php echo htmlspecialchars($user && $user['avatar'] ? $user['avatar'] : 'default-avatar.jpg'); ?>" alt="Avatar" class="avatar-button">
            </a>
        </div>

        <!-- Posts Section -->
        <div class="dashboard">
            <h2>Latest Posts</h2>
            <div id="posts-list">
                <ul>
                    <?php if ($posts): ?>
                        <?php foreach ($posts as $post): ?>
                            <li class="post-card">
                                <h3>
                                    <a href="each_post.php?post_id=<?= $post['id']; ?>">
                                        <?= htmlspecialchars($post['title']); ?>
                                    </a>
                                </h3>
                                <p><?= substr(htmlspecialchars($post['content']), 0, 100); ?>...</p>
                                <?php if (!empty($post['image'])): ?>
                                    <img src="<?= htmlspecialchars($post['image']); ?>" alt="Post Image">
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No posts available.</p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Add New Post Button -->
        <a href="post.php"><button class="btn-add"><big>+</big></button></a>
    </div>

    <script>
    // Store selected modules
    let selectedModules = [];

    // Function to fetch posts based on selected modules
    function fetchPosts() {
        let url = 'fetch-post.php';
        if (selectedModules.length > 0) {
            url += '?modules=' + selectedModules.join(',');
        }

        // Fetch posts and update the page
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById('posts-list').innerHTML = html;
            });
    }

    // Add event listener for each module button
    document.querySelectorAll('.btn-module').forEach(button => {
        button.addEventListener('click', function () {
            const moduleId = this.getAttribute('data-id');

            // Toggle the active state
            if (selectedModules.includes(moduleId)) {
                // Remove module from the selectedModules array
                selectedModules = selectedModules.filter(id => id !== moduleId);
                this.classList.remove('active'); // Remove active class to reset button color
            } else {
                // Add module to the selectedModules array
                selectedModules.push(moduleId);
                this.classList.add('active'); // Add active class to change button color
            }

            // Fetch posts based on the updated selected modules
            fetchPosts();
        });
    });

    // Initial load: fetch all posts without any module filtering
    fetchPosts();
    </script>
</body>
</html>
