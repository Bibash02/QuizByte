<?php
session_start();
include '../include/db.php'; // Make sure this file correctly connects to your database

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../home/login.php");
    exit();
}

// Query to count total users
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$result = mysqli_query($conn, $totalUsersQuery);
$row = mysqli_fetch_assoc($result);
$totalUsers = $row['total_users'] ?? 0;

// Query to count new users in the last 24 hours
$newUsersQuery = "SELECT COUNT(*) AS new_users FROM users WHERE created_at >= NOW() - INTERVAL 1 DAY";
$result = mysqli_query($conn, $newUsersQuery);
$row = mysqli_fetch_assoc($result);
$newUsers = $row['new_users'];

// Count total active quizzes
$activeQuizzesQuery = "SELECT COUNT(*) AS total_active FROM quizzes WHERE status = 'Active'";
$activeQuizzesResult = mysqli_query($conn, $activeQuizzesQuery);
$activeQuizzes = mysqli_fetch_assoc($activeQuizzesResult)['total_active'];

// Fetch the number of quiz completions by counting entries in the results table
$quizCompletionsQuery = "SELECT COUNT(*) AS total_completions FROM results WHERE score IS NOT NULL";
$completionsResult = mysqli_query($conn, $quizCompletionsQuery);
$quizCompletions = mysqli_fetch_assoc($completionsResult)['total_completions'];

// Fetch recently created quizzes (limit to last 5)
$recentQuizzesQuery = "SELECT q.id, q.title, q.category, q.status, q.created_at, 
                        (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
                        FROM quizzes q 
                        ORDER BY q.created_at DESC 
                        LIMIT 5";

$recentQuizzesResult = mysqli_query($conn, $recentQuizzesQuery);

// Fetch recent quiz activities
$query = "SELECT u.username, q.title, r.score, r.attempted_at 
        FROM results AS r
        JOIN users AS u ON r.user_id = u.id
        JOIN quizzes AS q ON r.quiz_id = q.id
        ORDER BY r.attempted_at DESC 
        LIMIT 5";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizByte - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: calc(100vh - 150px);
        }
        
        .sidebar {
            width: 250px;
            background-color: #f5f5f5;
            padding: 20px 0;
            border-right: 1px solid #ddd;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #4e73df;
            color: white;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #fff;
        }
        
        .dashboard-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #4e73df;
        }
        
        .stat-card.users {
            border-left-color: #36b9cc;
        }
        
        .stat-card.quizzes {
            border-left-color: #1cc88a;
        }
        
        .stat-card.completions {
            border-left-color: #f6c23e;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            text-transform: uppercase;
            color: #888;
        }
        
        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 0;
        }
        
        .stat-card .stat-icon {
            float: right;
            color: #dddfeb;
            font-size: 2rem;
        }
        
        .content-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .content-card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e3e6f0;
            background-color: #f8f9fc;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .content-card-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #4e73df;
        }
        
        .content-card-body {
            padding: 20px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e3e6f0;
        }
        
        table th {
            background-color: #f8f9fc;
            color: #4e73df;
            font-weight: 600;
        }
        
        table tbody tr:hover {
            background-color: #f8f9fc;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        
        .btn-info {
            background-color: #36b9cc;
            color: white;
        }
        
        .btn-danger {
            background-color: #e74a3b;
            color: white;
        }
        
        .btn-warning {
            background-color: #f6c23e;
            color: white;
        }
        
        .admin-welcome {
            background-color: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4e73df;
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
                    <li><a href="../admin/admin.php">Home</a></li>
                    <li><a href="../admin/quiz.php">Quizzes</a></li>
                    <li><a href="../admin/admin.php" class="active">Admin</a></li>
                    <li><a href="../home/logout.php" class="btn btn-primary">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <div class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage-quiz.php"><i class="fas fa-question-circle"></i> Manage Quizzes</a></li>
                <li><a href="../admin/add_quiz.php"><i class="fas fa-folder-plus"></i> Add New Quiz</a></li>
                <li><a href="../admin/admin-user.php"><i class="fas fa-users"></i> Users</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="dashboard-header">
                <h2>Admin Dashboard</h2>
                <a href="../admin/add_quiz.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Quiz</a>
            </div>
            
            <div class="admin-welcome">
                <h3>Welcome back, Admin!</h3>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card users">
                    <h3>Total Users</h3>
                    <p class="stat-value"><?php echo $totalUsers; ?></p>
                    <i class="fas fa-users stat-icon"></i>
                </div>
                <div class="stat-card quizzes">
                    <h3>Active Quizzes</h3>
                    <p class="stat-value"><?php echo $activeQuizzes; ?></p>
                    <i class="fas fa-question-circle stat-icon"></i>
                </div>
                <div class="stat-card completions">
                    <h3>Quiz Completions</h3>
                    <p class="stat-value"><?php echo $quizCompletions; ?></p>
                    <i class="fas fa-check-circle stat-icon"></i>
                </div>
                <div class="stat-card">
                    <h3>New Users (24h)</h3>
                    <p class="stat-value"><?php echo $newUsers; ?></p>
                    <i class="fas fa-user-plus stat-icon"></i>
                </div>
            </div>
            
            <div class="content-card">
                    <div class="content-card-header">
                        <h3>Recent Quiz Activity</h3>
                    </div>
                    <div class="content-card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Quiz Title</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody> <!-- Added missing tbody tag -->
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['score']); ?></td>
                                            <td><?php echo htmlspecialchars($row['attempted_at']); ?></td>
                                            <td>
                                                <div class='action-buttons'>
                                                    <a href='delete-quiz.php?id=<?php echo $row['id']; ?>' class='btn btn-sm btn-danger' 
                                                    onclick='return confirm("Are you sure you want to delete this quiz?")'>
                                                        <i class='fas fa-trash'></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <div class="content-card">
                <div class="content-card-header">
                    <h3>Recently Created Quizzes</h3>
                </div>
                <div class="content-card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Quiz Title</th>
                                    <th>Category</th>
                                    <th>Questions</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($recentQuizzesResult) > 0) {
                                    while ($quiz = mysqli_fetch_assoc($recentQuizzesResult)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($quiz['title']) . "</td>";
                                        echo "<td>" . htmlspecialchars($quiz['category']) . "</td>";
                                        echo "<td>" . $quiz['question_count'] . "</td>";
                                        echo "<td>" . date("M d, Y", strtotime($quiz['created_at'])) . "</td>";
                                        
                                        // Status label
                                        $statusClass = ($quiz['status'] == 'Active') ? 'bg-success' : 'bg-warning';
                                        echo "<td><span class='badge $statusClass'>" . htmlspecialchars($quiz['status']) . "</span></td>";

                                        // Action buttons
                                        echo "<td>
                                            <div class='action-buttons'>
                                                <a href='edit-quiz.php?id=" . $quiz['id'] . "' class='btn btn-sm btn-info'><i class='fas fa-edit'></i></a>
                                                <a href='delete-quiz.php?id=" . $quiz['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this quiz?\")'><i class='fas fa-trash'></i></a>
                                            </div>
                                        </td>";

                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No quizzes created yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/scripts.js"></script>
</body>
</html>