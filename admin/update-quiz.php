<?php
header("Content-Type: application/json");
include("../include/db.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["quiz_id"])) {
    $quiz_id = $data["quiz_id"];
    $title = $data["title"];
    $category = $data["category"];
    $description = $data["description"];
    $status = $data["status"];
    $time_limit = $data["time_limit"];
    $questions = $data["questions"];

    $stmt = $conn->prepare("UPDATE quizzes SET title=?, category=?, description=?, status=?, time_limit=? WHERE id=?");
    $stmt->bind_param("sssssi", $title, $category, $description, $status, $time_limit, $quiz_id);

    if ($stmt->execute()) {
        $conn->query("DELETE FROM questions WHERE quiz_id = $quiz_id");

        foreach ($questions as $question) {
            $stmt2 = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
            $stmt2->bind_param("is", $quiz_id, $question);
            $stmt2->execute();
        }

        echo json_encode(["success" => true, "message" => "Quiz updated successfully!"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
