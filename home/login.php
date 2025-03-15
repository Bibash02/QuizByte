<?php
session_start();
include('../include/db.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Secure session
            session_regenerate_id(true);

            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: /quiz/admin/admin.php");
            } else {
                header("Location: /quiz/user/user.php");
            }
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "User not found!";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizByte - Login</title>
    <link rel="stylesheet" href="/quiz/assets/style.css">
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
                    <li><a href="../home/index.php">Home</a></li>
                    <li><a href="../home/quiz.php">Quizzes</a></li>
                    <li><a href="../home/register.php" class="btn btn-secondary">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="auth-container">
        <div class="auth-form">
            <div class="auth-header">
                <h2>Welcome Back</h2>
                <p>Login to continue your quiz adventure!</p>
            </div>
            <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="form-group">
                    <label class="checkbox-container">
                        <input type="checkbox" id="remember-me" name="remember-me">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <div class="auth-footer">
                <p>Don't have an account? <a href="../home/register.php">Register here</a></p>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>QuizMaster</h3>
                    <p>Test your knowledge, challenge friends!</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h4>Stay Updated</h4>
                    <p>Subscribe to our newsletter for new quizzes and features.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Your email address">
                        <button type="submit" class="btn btn-secondary">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 QuizMaster. All rights reserved.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
