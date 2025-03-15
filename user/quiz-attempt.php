<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../home/login.php");
    exit();
}

// Include database connection file
include('../include/db.php');

// Get quiz ID from URL parameter
$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($quiz_id == 0) {
    die("Invalid quiz ID.");
}

// Fetch quiz details from the database
$query_quiz = "SELECT * FROM quizzes WHERE id = ?";
$stmt = $conn->prepare($query_quiz);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz_result = $stmt->get_result();
$quiz = $quiz_result->fetch_assoc();
if (!$quiz) {
    die("Quiz not found.");
}

// Fetch questions for the quiz from the database
$query_questions = "SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC";
$stmt2 = $conn->prepare($query_questions);
$stmt2->bind_param("i", $quiz_id);
$stmt2->execute();
$questions_result = $stmt2->get_result();
$questions = array();
while ($row = $questions_result->fetch_assoc()) {
    // Fetch options for each question
    $question_id = $row['id'];
    $query_options = "SELECT * FROM options WHERE question_id = ? ORDER BY id ASC";
    $stmt3 = $conn->prepare($query_options);
    $stmt3->bind_param("i", $question_id);
    $stmt3->execute();
    $options_result = $stmt3->get_result();
    $options = array();
    while ($opt = $options_result->fetch_assoc()) {
        $options[] = $opt['option_text']; // Change 'option_text' if your column name differs.
    }
    $row['options'] = $options;
    $questions[] = $row;
}

// Update total questions count for display purposes
$quiz['total_questions'] = count($questions);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizByte - Attempt Quiz</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Quiz attempt specific styles */
        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .quiz-info {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .question-card {
            background: #f1f8ff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid #4285f4;
        }
        .option-label {
            display: block;
            padding: 12px 15px;
            margin: 8px 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .option-label:hover {
            background: #f0f7ff;
            border-color: #a3c9ff;
        }
        .option-input:checked + .option-label {
            background: #e3efff;
            border-color: #4285f4;
            font-weight: 500;
        }
        .option-input {
            display: none;
        }
        .quiz-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .timer-container {
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 50px;
            border: 1px solid #ddd;
            display: inline-flex;
            align-items: center;
        }
        .timer-icon {
            margin-right: 8px;
            color: #dc3545;
        }
        .progress-container {
            margin: 20px 0;
        }
        .progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #4285f4;
            width: 0%;
        }
        .progress-text {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-top: 5px;
        }
        .question-number {
            font-weight: 500;
            margin-bottom: 5px;
            color: #4285f4;
        }
        .btn-submit {
            background: #4caf50;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #43a047;
        }
        .btn-navigate {
            background: #f1f3f5;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-navigate:hover {
            background: #e2e6ea;
        }
        /* Back button styles */
        .back-button {
            display: inline-flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 8px 16px;
            background: #f1f3f5;
            border: 1px solid #ddd;
            border-radius: 6px;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .back-button:hover {
            background: #e2e6ea;
            color: #333;
        }
        .back-button i {
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>QuizByte</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../home/index.php">Home</a></li>
                    <li><a href="../quiz.php" class="active">Quizzes</a></li>
                    <li><a href="../logout.php" class="btn btn-primary">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="main-content">
        <div class="container">
            <div class="quiz-container">
                <!-- Back button -->
                <a href="../user/user.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back to Quizzes
                </a>
                
                <div class="quiz-header">
                    <div>
                        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
                        <span class="category-badge"><?php echo htmlspecialchars($quiz['category']); ?></span>
                    </div>
                    <?php if ($quiz['time_limit'] === 'entire_quiz'): ?>
                    <div class="timer-container">
                        <i class="fas fa-clock timer-icon"></i>
                        <span id="timer">30:00</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="quiz-info">
                    <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                    <p><i class="fas fa-question-circle"></i> Total Questions: <?php echo $quiz['total_questions']; ?></p>
                </div>
                
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <div class="progress-text">
                        <span>Question 1 of <?php echo $quiz['total_questions']; ?></span>
                        <span>0% Complete</span>
                    </div>
                </div>
                
                <form id="quizForm" method="POST" action="submit_quiz.php">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                    
                    <div id="questionsContainer">
                        <?php foreach ($questions as $index => $question): ?>
                        <div class="question-card" id="question-<?php echo $index+1; ?>" <?php if($index !== 0) echo 'style="display: none;"'; ?>>
                            <div class="question-number">Question <?php echo $index+1; ?></div>
                            <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
                            
                            <?php foreach ($question['options'] as $option_index => $option): ?>
                            <div class="option">
                                <input type="radio" id="q<?php echo $index+1; ?>_option<?php echo $option_index; ?>" name="answers[<?php echo $question['id']; ?>]" value="<?php echo $option_index; ?>" class="option-input" required>
                                <label for="q<?php echo $index+1; ?>_option<?php echo $option_index; ?>" class="option-label"><?php echo htmlspecialchars($option); ?></label>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="quiz-navigation">
                                <?php if ($index > 0): ?>
                                    <button type="button" class="btn-navigate" onclick="showQuestion(<?php echo $index; ?>)">Previous</button>
                                <?php else: ?>
                                    <button type="button" class="btn-navigate" disabled>Previous</button>
                                <?php endif; ?>
                                
                                <?php if ($index < count($questions) - 1): ?>
                                    <button type="button" class="btn-navigate" onclick="showQuestion(<?php echo $index+2; ?>)">Next</button>
                                <?php else: ?>
                                    <button type="submit" class="btn-submit">Submit Quiz</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2>QuizMaster</h2>
                    <p>Challenge your mind, expand your knowledge.</p>
                </div>
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/home/index.php">Home</a></li>
                        <li><a href="/home/quiz.php">Quizzes</a></li>
                        <li><a href="/home/logout.php">Logout</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-envelope"></i> bayalkotibibash@gmail.com</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 QuizMaster. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Define total number of questions from PHP
        var totalQuestions = <?php echo count($questions); ?>;
        
        // Function to show a specific question and hide others
        function showQuestion(questionNumber) {
            // Hide all question cards
            document.querySelectorAll('.question-card').forEach(card => {
                card.style.display = 'none';
            });
            
            // Show the selected question card
            document.getElementById('question-' + questionNumber).style.display = 'block';
            
            // Update progress bar and text
            const progress = (questionNumber / totalQuestions) * 100;
            document.querySelector('.progress-fill').style.width = progress + '%';
            document.querySelector('.progress-text span:first-child').textContent = 'Question ' + questionNumber + ' of ' + totalQuestions;
            document.querySelector('.progress-text span:last-child').textContent = Math.round(progress) + '% Complete';
        }
        
        // Timer functionality (if needed)
        function startTimer(duration, display) {
            let timer = duration, minutes, seconds;
            let interval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    alert("Time's up! Quiz will be submitted automatically.");
                    document.getElementById('quizForm').submit();
                }
            }, 1000);
        }
        
        // Initialize timer if the entire quiz has a time limit
        <?php if ($quiz['time_limit'] === 'entire_quiz'): ?>
        window.onload = function () {
            let thirtyMinutes = 60 * 30,
                display = document.querySelector('#timer');
            startTimer(thirtyMinutes, display);
        };
        <?php endif; ?>
    </script>
</body>
</html>
