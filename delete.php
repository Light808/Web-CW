<?php
session_start();
require 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to delete this.";
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0; // Default to 0 if not set

// Check if we're deleting a post
if (isset($_GET['post_id'])) {
    $post_id = (int)$_GET['post_id'];

    // Fetch the post from the database to verify ownership or admin rights
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if ($post) {
        // Check if the user is either the post owner or an admin
        if ($is_admin == 1 || $post['user_id'] == $user_id) {
            // Delete the post
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            echo "Post deleted successfully.";
        } else {
            echo "You do not have permission to delete this post.";
        }
    } else {
        echo "Post not found.";
    }
}

// Check if we're deleting an answer (comment)
if (isset($_GET['answer_id'])) {
    $answer_id = (int)$_GET['answer_id'];

    // Fetch the answer to verify ownership or admin rights
    $stmt = $pdo->prepare("SELECT user_id FROM answers WHERE id = ?");
    $stmt->execute([$answer_id]);
    $answer = $stmt->fetch();

    if ($answer) {
        if ($is_admin == 1 || $answer['user_id'] == $user_id) {
            // Delete the answer
            $stmt = $pdo->prepare("DELETE FROM answers WHERE id = ?");
            $stmt->execute([$answer_id]);
            echo "Answer deleted successfully.";
        } else {
            echo "You do not have permission to delete this answer.";
        }
    } else {
        echo "Answer not found.";
    }
}
?>
