
<?php
session_start();
require 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['create_quiz'])) {
    $title = trim($_POST['title']);
    $duration = (int)$_POST['duration'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Simple validation
    if ($title == "" || $duration <= 0 || $start_time == "" || $end_time == "") {
        $error = "All fields are required and duration must be greater than 0.";
    } else {
        $stmt = $conn->prepare("INSERT INTO quizzes (title, duration_minutes, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $title, $duration, $start_time, $end_time);

        if ($stmt->execute()) {
            $success = "Quiz created successfully! You can now add questions.";
        } else {
            $error = "Error creating quiz: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Quiz</title>
    <link rel="stylesheet" href="add_quiz.css">
</head>
<body>

<div class="form-container">
    <h2>Create New Quiz</h2>

    <?php
    if (!empty($error)) echo "<p class='error'>$error</p>";
    if (!empty($success)) echo "<p class='message'>$success</p>";
    ?>

    <form method="POST">
        <label>Quiz Title:</label>
        <input type="text" name="title" required>

        <label>Duration (minutes):</label>
        <input type="number" name="duration" min="1" required>

        <label>Start Time:</label>
        <input type="datetime-local" name="start_time" required>

        <label>End Time:</label>
        <input type="datetime-local" name="end_time" required>

        <button type="submit" name="create_quiz">Create Quiz</button>
    </form>

    <p><a href="admin_dashboard.php">⬅ Back to Dashboard</a></p>
</div>

</body>
</html>
