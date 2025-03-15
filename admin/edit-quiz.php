<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            max-width: 700px;
            margin: auto;
            border-radius: 10px;
        }
        h2, h4 {
            font-weight: bold;
        }
        .btn {
            border-radius: 5px;
        }
        textarea {
            resize: none;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="card p-4 shadow-lg">
        <button class="btn btn-dark mb-3" onclick="history.back()">← Back to Home</button>

        <div class="d-flex justify-content-between align-items-center">
            <h2>Edit Quiz</h2>
            <span class="badge bg-success" id="quizStatus">Active</span>
        </div>

        <div class="mt-3">
            <h4 class="text-primary">Quiz Details</h4>
            <form id="quizForm">
                <input type="hidden" id="quizId" value="1"> <!-- Replace with dynamic quiz ID -->

                <div class="mb-3">
                    <label class="form-label">Quiz Title</label>
                    <input type="text" class="form-control" id="quizTitle" placeholder="Enter quiz title">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" id="quizCategory">
                        <option selected>Select a category</option>
                        <option value="math">Math</option>
                        <option value="science">Science</option>
                        <option value="history">History</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="quizDescription" rows="3" placeholder="Enter description"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Time Limit</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="timeLimit" value="no_limit" checked>
                        <label class="form-check-label">No time limit</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="timeLimit" value="per_question">
                        <label class="form-check-label">Per question</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="timeLimit" value="entire_quiz">
                        <label class="form-check-label">Entire quiz</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" value="active" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" value="inactive">
                        <label class="form-check-label">Inactive</label>
                    </div>
                </div>

                <h4 class="text-primary">Quiz Questions</h4>
                <div id="questionsContainer">
                    <input type="text" class="form-control mb-2" name="question[]" placeholder="Enter question">
                </div>
                <button type="button" class="btn btn-primary w-100 mb-3" id="addQuestion">Add Another Question</button>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success px-4">Save Changes</button>
                    <button type="button" class="btn btn-danger px-4" id="deleteQuiz">Delete Quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById("addQuestion").addEventListener("click", function() {
    let questionInput = document.createElement("input");
    questionInput.type = "text";
    questionInput.className = "form-control mb-2";
    questionInput.name = "question[]";
    questionInput.placeholder = "Enter question";
    document.getElementById("questionsContainer").appendChild(questionInput);
});

document.getElementById("quizForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let quizId = document.getElementById("quizId").value;
    let quizTitle = document.getElementById("quizTitle").value;
    let quizCategory = document.getElementById("quizCategory").value;
    let quizDescription = document.getElementById("quizDescription").value;
    let quizStatus = document.querySelector('input[name="status"]:checked').value;
    let timeLimit = document.querySelector('input[name="timeLimit"]:checked').value;

    let questions = Array.from(document.querySelectorAll('input[name="question[]"]')).map(q => q.value);

    fetch("update-quiz.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            quiz_id: quizId, 
            title: quizTitle, 
            category: quizCategory, 
            description: quizDescription, 
            status: quizStatus, 
            time_limit: timeLimit,
            questions: questions
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => console.error("Error:", error));
});

document.getElementById("deleteQuiz").addEventListener("click", function() {
    let quizId = document.getElementById("quizId").value;

    if (confirm("Are you sure you want to delete this quiz?")) {
        fetch("delete-quiz.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ quiz_id: quizId })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                window.location.href = "quiz_list.php"; // Redirect after deletion
            }
        })
        .catch(error => console.error("Error:", error));
    }
});
</script>

</body>
</html>
