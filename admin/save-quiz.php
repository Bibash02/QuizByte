<?php
session_start();
include('../include/db.php'); // Include your existing database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quiz_title  = mysqli_real_escape_string($conn, $_POST['quiz_title']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $time_limit  = mysqli_real_escape_string($conn, $_POST['time_limit']);

    // Insert quiz details with a default total_questions value
    $quiz_query = "INSERT INTO quizzes (title, category, description, time_limit, total_questions, status) 
                   VALUES (?, ?, ?, ?, 0, 'Draft')";
    $stmt = mysqli_prepare($conn, $quiz_query);
    mysqli_stmt_bind_param($stmt, "ssss", $quiz_title, $category, $description, $time_limit);
    mysqli_stmt_execute($stmt);
    $quiz_id = mysqli_insert_id($conn); // Get the inserted quiz ID
    mysqli_stmt_close($stmt);

    if ($quiz_id) {
        $question_count = 0; // Counter for total questions

        // Insert questions and their options
        foreach ($_POST['questions'] as $index => $question_text) {
            $question_text = mysqli_real_escape_string($conn, $question_text);
            $question_query = "INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $question_query);
            mysqli_stmt_bind_param($stmt, "is", $quiz_id, $question_text);
            mysqli_stmt_execute($stmt);
            $question_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            $question_count++; // Increase counter for each inserted question

            // Insert options for the current question
            if (isset($_POST['options'][$index])) {
                foreach ($_POST['options'][$index] as $option_index => $option_text) {
                    $option_text = mysqli_real_escape_string($conn, $option_text);
                    $is_correct = (isset($_POST['correct_option'][$index]) && $_POST['correct_option'][$index] == $option_index) ? 1 : 0;

                    $option_query = "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $option_query);
                    mysqli_stmt_bind_param($stmt, "isi", $question_id, $option_text, $is_correct);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }

        // Update the quizzes table with the total number of questions
        $update_query = "UPDATE quizzes SET total_questions = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ii", $question_count, $quiz_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<script>alert('Quiz successfully added!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Failed to add quiz!'); window.location.href='add_quiz.php';</script>";
    }

    mysqli_close($conn);
}
?>
