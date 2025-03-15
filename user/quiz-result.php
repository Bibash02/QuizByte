<?php
session_start();
require_once('../include/db.php');

// Validate GET parameters
if (!isset($_GET['quiz_id']) || !isset($_GET['score'])) {
    die("Invalid request.");
}

$quiz_id = intval($_GET['quiz_id']);
$score = intval($_GET['score']);

// Fetch quiz title using a prepared statement
$query = "SELECT title FROM quizzes WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$quiz = $result->fetch_assoc();
$stmt->close();

if (!$quiz) {
    die("Quiz not found.");
}

// Fetch total number of questions for this quiz
$query_count = "SELECT COUNT(*) AS total FROM questions WHERE quiz_id = ?";
$stmt2 = $conn->prepare($query_count);
if (!$stmt2) {
    die("Prepare failed: " . $conn->error);
}
$stmt2->bind_param("i", $quiz_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row = $result2->fetch_assoc();
$total_questions = $row['total'];
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - <?= htmlspecialchars($quiz['title']) ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }
        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Quiz Results</h2>
    <p>You scored <?= $score ?> out of <?= $total_questions ?></p>
    <p>Quiz Title: <?= htmlspecialchars($quiz['title']) ?></p>
    
    <a href="../user/user.php" class="btn btn-secondary btn-back">Back to Home</a>
</div>

</body>
</html>
