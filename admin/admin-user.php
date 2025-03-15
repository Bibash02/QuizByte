<?php
include('../include/db.php'); // Database connection
session_start();

// Check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle user deletion if requested
if(isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_sql = "DELETE FROM users WHERE id = '$delete_id'";
    
    if(mysqli_query($conn, $delete_sql)) {
        $success_message = "User deleted successfully!";
    } else {
        $error_message = "Error deleting user: " . mysqli_error($conn);
    }
}

// Fetch all users from database
$sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizByte Admin - User Management</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
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
        
        .user-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .user-table th, .user-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .user-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .user-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .user-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit, .btn-delete {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .btn-edit {
            background-color: #3498db;
            color: white;
            border: none;
        }
        
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
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
        
        .user-count {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .admin-sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #2c3e50;
            padding-top: 80px;
            z-index: 1;
        }
        
        .admin-sidebar ul {
            list-style: none;
            padding: 0;
        }
        
        .admin-sidebar ul li {
            padding: 0;
        }
        
        .admin-sidebar ul li a {
            padding: 15px 20px;
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .admin-sidebar ul li a:hover, .admin-sidebar ul li a.active {
            background-color: #34495e;
        }
        
        .admin-sidebar ul li a i {
            margin-right: 10px;
        }
        
        .admin-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                position: relative;
                height: auto;
                padding-top: 0;
            }
            
            .admin-content {
                margin-left: 0;
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
            <h2><i class="fas fa-users"></i> User Management</h2>
            <a href="../admin/add-user.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New User</a>
        </div>
        
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="user-count">
            <i class="fas fa-user-friends"></i> Total Users: <?php echo mysqli_num_rows($result); ?>
        </div>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge <?php echo ($row['role'] == 'admin') ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td class="user-actions">
                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="admin-user.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        // Add confirmation for delete action
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                if(!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>