<?php
// submit_answer.php - Handles answering a post
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to submit an answer.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $answer_content = $_POST['answer_content'];

    // Use PDO and bind parameters or directly pass values in execute
    $stmt = $pdo->prepare("INSERT INTO answers (post_id, username, user_id, answer_content) VALUES (?, ?, ?, ?)");
    
    // Pass the values directly to execute (no need for bind_param)
    if ($stmt->execute([$post_id, $user_id, $answer_content])) {
        echo "Answer submitted successfully!";
    } else {
        // PDO uses errorInfo() to retrieve error messages
        $error = $stmt->errorInfo();
        echo "Error: " . $error[2];
    }
}
?>
