<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../home/login.php");
    exit();
}

require_once('../include/db.php');

// Validate required POST data
if (!isset($_POST['answers']) || !isset($_POST['quiz_id'])) {
    die("Invalid form submission.");
}

$user_id = $_SESSION['user_id'];
$quiz_id = intval($_POST['quiz_id']);
$answers = $_POST['answers'];

$score = 0;

// Prepare statement to fetch the correct option for a given question.
// Assumes the questions table uses "id" as the primary key.
$query = "SELECT correct_option FROM questions WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Loop through each submitted answer
foreach ($answers as $question_id => $answer) {
    $question_id = intval($question_id);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($question = $result->fetch_assoc()) {
        if ($question['correct_option'] == $answer) {
            $score++;
        }
    }
}
$stmt->close();

// Insert the overall quiz result into the database using a prepared statement
$query_insert = "INSERT INTO results (user_id, quiz_id, score) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($query_insert);
if (!$stmt_insert) {
    die("Prepare failed: " . $conn->error);
}
$stmt_insert->bind_param("iii", $user_id, $quiz_id, $score);
$stmt_insert->execute();
$stmt_insert->close();

// Redirect to the results page with quiz ID and score as query parameters
header("Location: quiz-result.php?quiz_id=$quiz_id&score=$score");
exit();
?>
