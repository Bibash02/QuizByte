<?php
include('../include/db.php'); // Database connection
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// Get user ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit();
}
$user_id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
if (!$user) {
    header("Location: users.php");
    exit();
}
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET username='$username', email='$email', password='$password', role='$role' WHERE id='$user_id'";
    } else {
        $update_sql = "UPDATE users SET username='$username', email='$email', role='$role' WHERE id='$user_id'";
    }
    if (mysqli_query($conn, $update_sql)) {
        $success_message = "User updated successfully!";
    } else {
        $error_message = "Error updating user: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizByte Admin - Edit User</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .form-select {
            appearance: none;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-primary {
            background-color: #3498db;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .icon-input {
            position: relative;
        }
        
        .icon-input input {
            padding-left: 40px;
        }
        
        .icon-input i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #adb5bd;
        }
        
        .form-footer {
            margin-top: 25px;
            text-align: center;
        }
        
        .back-link {
            display: inline-block;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            margin-top: 15px;
        }
        
        .back-link:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                margin: 20px auto;
                padding: 15px;
            }
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
                    <li><a href="../admin/admin.php">Dashboard</a></li>
                    <li><a href="../admin/quiz.php">Manage Quizzes</a></li>
                    <li><a href="../admin/admin-users.php" class="active">Manage Users</a></li>
                    <li><a href="../admin/logout.php" class="btn btn-primary">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="admin-container">
        <div class="admin-header">
            <h2><i class="fas fa-user-edit"></i> Edit User</h2>
            <a href="../admin/admin-user.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to User List</a>
        </div>
        
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <div class="icon-input">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <div class="icon-input">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">New Password (Leave blank to keep current):</label>
                <div class="icon-input">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" class="form-control form-select" required>
                    <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                    <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> Update User
            </button>
        </form>
        
        <div class="form-footer">
            <a href="../admin/admin-user.php" class="back-link">
                <i class="fas fa-users"></i> Back to User List
            </a>
        </div>
    </div>
</body>
</html>