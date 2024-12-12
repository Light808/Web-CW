<?php
session_start();
require 'db.php'; // Include the database connection

// Get the post ID from the URL
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id) {
    // Fetch the post from the database along with the username and avatar of the post author
    $stmt = $pdo->prepare("SELECT posts.*, users.username, users.avatar FROM posts 
        JOIN users ON posts.user_id = users.user_id 
        WHERE posts.id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        echo "Post not found.";
        exit;
    }

    // Fetch answers related to the post, along with the username and avatar of the users who wrote the answer
    $stmt = $pdo->prepare("SELECT answers.*, users.username, users.avatar FROM answers 
        JOIN users ON answers.user_id = users.user_id 
        WHERE answers.post_id = ? ORDER BY answers.created_at DESC");
    $stmt->execute([$post_id]);
    $answers = $stmt->fetchAll();
} else {
    echo "Invalid post ID.";
    exit;
}

// Handle form submission for adding an answer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer_content'])) {
    $answer_content = $_POST['answer_content'];
    $user_id = $_SESSION['user_id'] ?? 1; // Get the logged-in user's ID (default to 1 if not set)

    // Insert the answer into the database
    $stmt = $pdo->prepare("INSERT INTO answers (post_id, user_id, answer_content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $answer_content]);
    
    // Redirect to the same post page to show the new answer
    header("Location: each_post.php?post_id=" . $post_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to your CSS file -->
    <style>
        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Delete Button for the Post -->
        <?php if ($_SESSION['user_id'] == $post['user_id'] || $_SESSION['is_admin'] == 1): ?>
            <div style="text-align: right; padding-right:5px;">
                <a href="delete.php?post_id=<?php echo $post['id']; ?>" class="delete-btn">X</a>
            </div>
        <?php endif; ?>

        <!-- Post with avatar and username -->
        <div class="user-info">
            <?php
            $post_avatar = !empty($post['avatar']) ? htmlspecialchars($post['avatar']) : 'default-avatar.jpg';
            ?>
            <img src="<?php echo $post_avatar; ?>" alt="User Avatar" class="avatar">
            <h3><?php echo htmlspecialchars($post['username']); ?></h3>
        </div>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p> <!-- Display the full post content -->
        <?php if (!empty($post['image'])): ?>
            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" style="width: 200px; margin-top: 10px;">
        <?php endif; ?>

        <!-- Display the answers for this post -->
        <h2>Answers</h2>
        <?php if ($answers): ?>
            <ul>
                <?php foreach ($answers as $answer): ?>
                    <li>
                        <div class="user-info">
                            <?php
                            $answer_avatar = !empty($answer['avatar']) ? htmlspecialchars($answer['avatar']) : 'default-avatar.jpg';
                            ?>
                            <img src="<?php echo $answer_avatar; ?>" alt="User Avatar" class="avatar">
                            <big><strong><?= htmlspecialchars($answer['username']); ?> </strong></big>&nbsp;&nbsp; 
                            <small style="font-size: 0.65em;">posted on <?= $answer['created_at']; ?></small>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($answer['answer_content'])); ?></p>
                        <!-- Delete button for answers -->
                        <?php if ($_SESSION['user_id'] == $answer['user_id'] || $_SESSION['is_admin'] == 1): ?>
                            <div style="text-align: right;">
                                <a href="delete.php?answer_id=<?= $answer['id']; ?>" class="delete-btn">X</a>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No answers yet. Be the first to answer!</p>
        <?php endif; ?>

        <!-- Form to add a new answer -->
        <h3>Add Your Answer</h3>
        <form action="each_post.php?post_id=<?php echo $post_id; ?>" method="POST">
            <textarea name="answer_content" rows="1" cols="120" required></textarea>
            <br>
            <button type="submit" class="btn" >Submit Answer</button>
        </form>
        <br><br>

        <!-- Back to home button -->
        <a href="home.html.php" class="btn">Back to Home</a>
        <br><br>
    </div>
</body>
</html>
