<?php
session_start();
require_once '../include/db.php'; // Include database connection

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
                        LIMIT 3
";
$quiz_result = $conn->query($quiz_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizByte - Home</title>
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
                    <li><a href="/index.php" class="active">Home</a></li>
                    <li><a href="../user/quiz.php">Quizzes</a></li>
                    <li><a href="/logout.php" class="btn btn-primary">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Challenge Your Knowledge</h2>
                <p>Test your skills, compete with friends, and climb the leaderboard with our collection of quizzes.</p>
                <div class="hero-buttons">
                    <a href="../user/quiz.php" class="btn btn-primary">Start Quiz</a>
                </div>
            </div>
        </div>
    </section>

    <section class="popular-quizzes">
        <div class="container">
            <h2 class="section-title">Popular Quizzes</h2>
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
            <div class="see-more">
                <a href="quiz.php" class="btn btn-secondary">See All Quizzes</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2>QuizByte</h2>
                    <p>Challenge your mind, expand your knowledge.</p>
                </div>
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="../user/index.php">Home</a></li>
                        <li><a href="../user/quiz.php">Quizzes</a></li>
                        <li><a href="../user/logout.php">Logout</a></li>
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

    <script src="../assets/scripts.js"></script>
</body>
</html>
