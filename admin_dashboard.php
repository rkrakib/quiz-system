<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background-color: #f4f7f6;
        }

        .header {
            background-color: #333;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .header nav {
            display: flex;
            gap: 1.5rem;
        }

        .header nav a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .header nav a:hover {
            background-color: #575757;
        }
    </style>
</head>
<body>

    <header class="header">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="add_quiz.php">Add Quiz</a>
            <a href="add_question.php">Add Question</a>
            <a href="view_results.php">View Results</a>
            <a href="upload_ebook.html">Upload E-Book</a>
            <a href="upload_video.php">Upload Video</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main style="padding: 2rem;">
        <p>Welcome to the Admin Dashboard. Use the navigation bar above to manage your quizzes.</p>
    </main>

</body>
</html>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            background-color: #f9f9f9;
        }
        .dashboard h2 {
            margin-bottom: 20px;
        }
        .dashboard a {
            display: block;
            padding: 10px;
            margin: 8px 0;
            background-color: white;
            border: 1px solid #000;
            border-radius: 4px;
            text-decoration: none;
            color: black;
        }
        .dashboard a:hover {
            background-color: #eee;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> 👋</h2>

    <a href="add_quiz.php">➕ Create New Quiz</a>
    <a href="add_question.php">📝 Add Questions</a>
    <a href="view_results.php">📊 View Results</a>
    <a href="upload_ebook.html">📚 Upload E-Book</a>
    <a href="upload_video.php">🎥 Upload Video</a>
    <a href="logout.php">🚪 Logout</a>
</div>

</body>
</html>
<?php
require 'db_connect.php';

// ✅ Fixed SQL Query
$sql = "SELECT q.id, q.title, COUNT(que.id) AS total_questions, q.duration_minutes
        FROM quizzes q
        LEFT JOIN questions que ON q.id = que.quiz_id
        GROUP BY q.id, q.title, q.duration_minutes
        ORDER BY q.id DESC";

$result = $conn->query($sql);
?>

<main style="padding: 2rem;">
    <h2>📋 Available Quizzes</h2>

    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse: collapse; background:white;">
        <tr style="background:#333; color:white;">
            <th>ID</th>
            <th>Title</th>
            <th>Total Questions</th>
            <th>Duration (mins)</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo $row['total_questions']; ?></td>
                    <td><?php echo $row['duration_minutes']; ?></td>
                    <td>
                        <a href="delete_quiz.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this quiz?');" style="color:red;">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No quizzes available.</td></tr>
        <?php endif; ?>
    </table>
</main>
