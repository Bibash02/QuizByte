<?php
include('../include/db.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = mysqli_real_escape_string($conn, $_POST['role']); // Get role

    // Insert user data into database
    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizByte - Register</title>
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
                    <li><a href="../home/index.php">Home</a></li>
                    <li><a href="../home/quiz.php">Quizzes</a></li>
                    <li><a href="../home/leaderboard.php">Leaderboard</a></li>
                    <li><a href="../home/login.php" class="btn btn-primary">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="auth-container">
        <div class="auth-form">
            <div class="auth-header">
                <h2>Create Your Account</h2>
                <p>Join QuizMaster and test your knowledge!</p>
            </div>
            <form id="register-form" action="register.php" method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password"><i class="fas fa-check-circle"></i> Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                </div>
                <div class="form-group">
                    <label for="role"><i class="fas fa-user-shield"></i> Select Role</label>
                    <select id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>
            <div class="auth-footer">
                <p>Already have an account? <a href="../home/login.php">Login here</a></p>
            </div>
        </div>
    </section>

    <script src="assets/js/register.js"></script>
</body>
</html>
