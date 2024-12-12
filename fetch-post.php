<?php
require 'db.php'; // Ensure this file is included for database connection

// Check if the modules parameter is set in the query string
$module_ids = isset($_GET['modules']) ? explode(',', $_GET['modules']) : [];

// Prepare the SQL query to fetch posts along with the username and avatar
if (empty($module_ids)) {
    // If no modules are selected, fetch all posts
    $stmt = $pdo->prepare("
        SELECT posts.*, users.username, users.avatar 
        FROM posts 
        JOIN users ON posts.user_id = users.user_id 
        ORDER BY posts.created_at DESC
    ");
} else {
    // If modules are selected, fetch posts that match the selected modules
    $inClause = str_repeat('?,', count($module_ids) - 1) . '?';
    $stmt = $pdo->prepare("
        SELECT posts.*, users.username, users.avatar 
        FROM posts 
        JOIN users ON posts.user_id = users.user_id 
        WHERE posts.module_id IN ($inClause) 
        ORDER BY posts.created_at DESC
    ");
}

// Execute the query with the module IDs
$stmt->execute($module_ids);
$posts = $stmt->fetchAll();

// Output the posts
if ($posts) {
    foreach ($posts as $post) {
        echo '<li class="post-card">';
        
        // Display the avatar if it exists, otherwise use a default avatar
        $avatar = !empty($post['avatar']) ? htmlspecialchars($post['avatar']) : 'default-avatar.jpg';
        
        echo '<p>';
        echo '<img src="' . $avatar . '" alt="User Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">';
        echo '<big><strong>' . htmlspecialchars($post['username']) . '</strong></big>';
        echo '</p>';
        
        echo '<h3><a href="each_post.php?post_id=' . htmlspecialchars($post['id']) . '">'
             . htmlspecialchars($post['title']) . '</a></h3>';
        echo '<p>' . substr(htmlspecialchars($post['content']), 0, 100) . '...</p>';
        
        if (!empty($post['image'])) {
            echo '<img src="' . htmlspecialchars($post['image']) . '" alt="Post Image" class="post-card img">';
        }
        
        echo '</li>';
    }
} else {
    echo '<p>No posts available for the selected module(s).</p>';
}
?>
