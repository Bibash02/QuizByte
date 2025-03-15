

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Quiz</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }

        h2 {
            text-align: center;
            color: #343a40;
            margin-bottom: 20px;
        }

        .form-control, .form-select {
            border-radius: 5px;
        }

        .btn {
            border-radius: 5px;
        }

        .question-block {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
        }

        .remove-question {
            margin-top: 10px;
        }

        .card {
            border: none;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
        }

        #addQuestionBtn {
            width: 100%;
        }

        /* Back button styling */
        .back-btn {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container">
    <!-- Back to Home Button -->
    <div class="back-btn">
        <a href="../admin/admin.php" class="btn btn-secondary">⬅ Back to Home</a>
    </div>

    <h2>Add New Quiz</h2>
    
    <form id="quizForm" method="POST" action="save-quiz.php">
        <!-- Quiz Details -->
        <div class="card p-3 mb-3">
            <h4 class="text-primary">Quiz Details</h4>
            <div class="mb-3">
                <label class="form-label">Quiz Title</label>
                <input type="text" name="quiz_title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select" required>
                    <option value="">Select a category</option>
                    <option value="Science">Science</option>
                    <option value="Math">Math</option>
                    <option value="History">History</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Time Limit</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="time_limit" value="no_limit" checked>
                    <label class="form-check-label">No time limit</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="time_limit" value="per_question">
                    <label class="form-check-label">Per question</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="time_limit" value="entire_quiz">
                    <label class="form-check-label">Entire quiz</label>
                </div>
            </div>
        </div>

        <!-- Quiz Questions -->
        <div class="card p-3 mb-3">
            <h4 class="text-primary">Quiz Questions</h4>
            <div id="questionsContainer">
                <div class="question-block">
                    <h5>Question 1</h5>
                    <input type="text" name="questions[]" class="form-control mb-2" placeholder="Enter question" required>
                    
                    <div class="options">
                        <label class="form-label">Options</label>
                        <div class="d-flex">
                            <input type="radio" name="correct_option[0]" value="0">
                            <input type="text" name="options[0][]" class="form-control mb-2 ms-2" placeholder="Option 1" required>
                        </div>
                        <div class="d-flex">
                            <input type="radio" name="correct_option[0]" value="1">
                            <input type="text" name="options[0][]" class="form-control mb-2 ms-2" placeholder="Option 2" required>
                        </div>
                        <div class="d-flex">
                            <input type="radio" name="correct_option[0]" value="2">
                            <input type="text" name="options[0][]" class="form-control mb-2 ms-2" placeholder="Option 3">
                        </div>
                        <div class="d-flex">
                            <input type="radio" name="correct_option[0]" value="3">
                            <input type="text" name="options[0][]" class="form-control mb-2 ms-2" placeholder="Option 4">
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-danger remove-question">Remove Question</button>
                </div>
            </div>

            <button type="button" id="addQuestionBtn" class="btn btn-primary mt-3">Add Another Question</button>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-success w-100">Publish Quiz</button>
    </form>
</div>

<script>
$(document).ready(function () {
    let questionCount = 1;

    $("#addQuestionBtn").click(function () {
        let questionHtml = `
            <div class="question-block">
                <h5>Question ${questionCount + 1}</h5>
                <input type="text" name="questions[]" class="form-control mb-2" placeholder="Enter question" required>
                
                <label class="form-label">Options</label>
                <div class="d-flex">
                    <input type="radio" name="correct_option[${questionCount}]" value="0">
                    <input type="text" name="options[${questionCount}][]" class="form-control mb-2 ms-2" placeholder="Option 1" required>
                </div>
                <div class="d-flex">
                    <input type="radio" name="correct_option[${questionCount}]" value="1">
                    <input type="text" name="options[${questionCount}][]" class="form-control mb-2 ms-2" placeholder="Option 2" required>
                </div>
                <div class="d-flex">
                    <input type="radio" name="correct_option[${questionCount}]" value="2">
                    <input type="text" name="options[${questionCount}][]" class="form-control mb-2 ms-2" placeholder="Option 3">
                </div>
                <div class="d-flex">
                    <input type="radio" name="correct_option[${questionCount}]" value="3">
                    <input type="text" name="options[${questionCount}][]" class="form-control mb-2 ms-2" placeholder="Option 4">
                </div>

                <button type="button" class="btn btn-danger remove-question">Remove Question</button>
            </div>
        `;
        $("#questionsContainer").append(questionHtml);
        questionCount++;
    });

    $(document).on("click", ".remove-question", function () {
        $(this).closest(".question-block").remove();
        questionCount--;
    });
});
</script>

</body>
</html>
