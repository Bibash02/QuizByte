<?php
session_start();
require_once '../include/db.php'; // Database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../home/login.php");
    exit();
}

// Fetch quizzes from the database
$quiz_query = "SELECT * FROM quizzes ORDER BY created_at DESC LIMIT 6";
$quiz_result = $conn->query($quiz_query);

$quiz_query = "
    SELECT q.id, q.title, q.category, q.description, q.created_at, 
                        (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
                        FROM quizzes q 
                        ORDER BY q.created_at DESC 
                        
";
$quiz_result = $conn->query($quiz_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizByte - Quizzes</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>QuizByte</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../user/user.php">Home</a></li>
                    <li><a href="quiz.php" class="active">Quizzes</a></li>
                    <li><a href="../home/logout.php" class="btn btn-primary">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="page-header">
        <div class="container">
            <h2>Explore Quizzes</h2>
            <p>Choose from a variety of quizzes and test your knowledge.</p>
        </div>
    </section>

    <section class="quiz-listing">
    <div class="container">
            <div class="quiz-grid">
                <?php while ($quiz = $quiz_result->fetch_assoc()): ?>
                <div class="quiz-card">
                    <div class="quiz-header">
                        <span class="category"> <?php echo htmlspecialchars($quiz['category']); ?> </span>
                    </div>
                    <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                    <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                    <div class="quiz-footer">
                        <span><i class="fas fa-question-circle"></i> <?php echo $quiz['question_count']; ?> Questions</span>
                        <a href="../user/quiz-attempt.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm">Start Quiz</a>
                    </div>
                </div>
                <?php endwhile; ?>
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
                        <li><a href="../user/user.php">Home</a></li>
                        <li><a href="../user/quiz.php">Quizzes</a></li>
                        <li><a href="../user/logout.php">Logout</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-envelope"></i> support@quizmaster.com</p>
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

    <script src="../assets/scripts.js"></script>
</body>
</html>
