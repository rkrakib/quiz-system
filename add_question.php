<?php
session_start();
require 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all quizzes for the dropdown
$quizResult = $conn->query("SELECT id, title FROM quizzes ORDER BY id DESC");

if (isset($_POST['add_question'])) {
    $quiz_id = (int)$_POST['quiz_id'];
    $_SESSION['last_quiz_id'] = $quiz_id; // ✅ remember last selection

    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = strtoupper($_POST['correct_option']); // A, B, C, D

    if ($question_text == "" || $option_a == "" || $option_b == "" || $option_c == "" || $option_d == "" || !in_array($correct_option, ['A','B','C','D'])) {
        $error = "All fields are required and correct option must be A, B, C, or D.";
    } else {
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);

        if ($stmt->execute()) {
            $success = "Question added successfully!";
        } else {
            $error = "Error adding question: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Question</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .form-container input, .form-container select, .form-container button, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }
        .form-container button {
            cursor: pointer;
        }
        .message { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add Question</h2>

    <?php
    if (!empty($error)) echo "<p class='error'>$error</p>";
    if (!empty($success)) echo "<p class='message'>$success</p>";
    ?>

    <form method="POST">
        <label>Select Quiz:</label>
        <select name="quiz_id" required>
            <option value="">-- Select Quiz --</option>
            <?php while ($quiz = $quizResult->fetch_assoc()) : ?>
                <option value="<?php echo $quiz['id']; ?>"
                    <?php 
                        if (
                            (!empty($_SESSION['last_quiz_id']) && $_SESSION['last_quiz_id'] == $quiz['id']) || 
                            (isset($_POST['quiz_id']) && $_POST['quiz_id'] == $quiz['id'])
                        ) echo "selected"; 
                    ?>>
                    <?php echo htmlspecialchars($quiz['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Question:</label>
        <textarea name="question_text" rows="3" required></textarea>

        <label>Option A:</label>
        <input type="text" name="option_a" required>

        <label>Option B:</label>
        <input type="text" name="option_b" required>

        <label>Option C:</label>
        <input type="text" name="option_c" required>

        <label>Option D:</label>
        <input type="text" name="option_d" required>

        <label>Correct Option (A/B/C/D):</label>
        <input type="text" name="correct_option" maxlength="1" required>

        <button type="submit" name="add_question">Add Question</button>
    </form>

    <p><a href="admin_dashboard.php">⬅ Back to Dashboard</a></p>
</div>

</body>
</html>
