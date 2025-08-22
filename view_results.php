<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <h2><?php echo ($role == 'admin') ? "All Quiz Results" : "My Quiz Results"; ?></h2>
    <table>
        <tr>
            <th>Quiz Title</th>
            <?php if ($role == 'admin') echo "<th>Student Name</th><th>Student Email</th>"; ?>
            <th>Score</th>
            <th>Submitted At</th>
        </tr>

<?php
if ($role == 'admin') {
    // Admin sees all results
    $stmt = $conn->prepare("SELECT r.score, r.submitted_at, r.quiz_id, q.title, u.name, u.email 
                            FROM results r 
                            JOIN quizzes q ON r.quiz_id = q.id 
                            JOIN users u ON r.student_id = u.id
                            ORDER BY r.submitted_at DESC");
} else {
    // Student sees only their own results
    $stmt = $conn->prepare("SELECT r.score, r.submitted_at, r.quiz_id, q.title 
                            FROM results r 
                            JOIN quizzes q ON r.quiz_id = q.id 
                            WHERE r.student_id = ?
                            ORDER BY r.submitted_at DESC");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get the total number of questions for the current quiz
        $quiz_id = $row['quiz_id'];
        $total_q_stmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = ?");
        $total_q_stmt->bind_param("i", $quiz_id);
        $total_q_stmt->execute();
        $total_q_result = $total_q_stmt->get_result();
        $total_questions = $total_q_result->fetch_row()[0];
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        if ($role == 'admin') {
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        }
        echo "<td>" . $row['score'] . " / " . $total_questions . "</td>";
        echo "<td>" . $row['submitted_at'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='" . ($role == 'admin' ? 5 : 3) . "'>No results found.</td></tr>";
}
?>
    </table>

    <p style="text-align:center;"><a href="<?php echo ($role=='admin') ? 'admin_dashboard.php' : 'quiz.php'; ?>">⬅ Back</a></p>
</body>
</html>