<?php
session_start();
require 'db.php'; // Include the database connection

// Fetch the user data if the user is logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch current user data from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

// Fetch only the 5 newest posts for the home page
try {
    $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT 4");
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error fetching posts: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Example Web Page</title>
    <link rel="stylesheet" href="css/home.css"> 
    <style>
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
       <!-- Avatar Button -->
       <div class="avatar-button-container">
            <a href="profile.php">
                <!-- Dynamically show the avatar from the database -->
                <img src="<?php echo htmlspecialchars($user && $user['avatar'] ? $user['avatar'] : 'default-avatar.jpg'); ?>" alt="Avatar" class="avatar-button">
            </a>
        </div>
        <div class="header">
            <h1>Welcome to Our Website</h1>
            <p>Your go-to platform for amazing content!</p>
        </div>

        <!-- Navigation Bar -->
        <div class="navigation">
            <a href="Home.html.php" class="btn">Home</a>
            <a href="about.html" class="btn">About</a>
            <a href="service.html" class="btn">Services</a>
            <a href="contact.html.php" class="btn">Contact</a>
        </div>

        <!-- Dashboard displaying posts -->
        <div class="dashboard">
            <h2>Latest Posts</h2>
            <div class="posts-list">
                <ul>
                    <?php if ($posts): ?>
                        <?php foreach ($posts as $post): ?>
                            <li class="post-card">
                                <!-- Post title becomes a clickable link -->
                                <h3>
                                    <a href="each_post.php?post_id=<?= $post['id']; ?>">
                                        <?= htmlspecialchars($post['title']); ?>
                                    </a>
                                </h3>
                                <p><?= substr(htmlspecialchars($post['content']), 0, 100); ?>...</p>

                                <!-- Display the image if available -->
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

        <div class="main-content">
            <a href="index.html.php"><button class="btn">Read More</button></a>
            <br>
            <button class="logout-btn" onclick="window.location.href='logout.php';">Logout</button>       
        </div>
    </div>
</body>
</html>
