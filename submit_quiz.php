<?php
session_start();
require 'db_connect.php';

// Only students can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quiz_id'], $_POST['answers'])) {
    $quiz_id = (int)$_POST['quiz_id'];
    $answers = $_POST['answers'];
    $student_id = $_SESSION['user_id'];
    $score = 0;

    // Fetch correct answers from database
    $stmt = $conn->prepare("SELECT id, correct_option FROM questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Get the total number of questions
    $total_questions = $result->num_rows;

    while ($row = $result->fetch_assoc()) {
        $q_id = $row['id'];
        $correct = $row['correct_option'];

        if (isset($answers[$q_id]) && strtoupper($answers[$q_id]) == $correct) {
            $score++;
        }
    }

    // Calculate percentage
    $percentage = 0;
    if ($total_questions > 0) {
        $percentage = ($score / $total_questions) * 100;
    }

    // Insert result into results table
    $insert = $conn->prepare("INSERT INTO results (quiz_id, student_id, score) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $quiz_id, $student_id, $score);
    $insert->execute();

    // Display result with a link to the external stylesheet
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Quiz Result</title>
        <link rel='stylesheet' href='style.css'>
        <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #e9ecef;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    color: #343a40;
}

.container {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 500px;
    width: 90%;
}

h2 {
    color: #fff;
    background-color: #28a745;
    padding: 15px;
    border-radius: 5px;
    margin-top: 0;
    margin-bottom: 25px;
}

p {
    font-size: 1.1em;
    margin-bottom: 15px;
}

strong {
    color: #1a753d;
}

.score, .percentage {
    font-weight: bold;
    color: #007bff;
}

.links {
    margin-top: 30px;
}

.links a {
    text-decoration: none;
    color: #ffffff;
    background-color: #007bff;
    padding: 10px 20px;
    border-radius: 5px;
    margin: 0 10px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    display: inline-block;
}

.links a:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}</style>
    </head>
    <body>
        <div class='container'>
            <h2>Quiz Completed!</h2>
            <p>Hi <strong>" . htmlspecialchars($_SESSION['user_name']) . "</strong>, your score is: <span class='score'><strong>$score out of $total_questions</strong></span></p>
            <p>Percentage: <span class='percentage'><strong>" . number_format($percentage, 2) . "%</strong></span></p>
            <div class='links'>
                <a href='quiz.php'>Back to Quizzes</a>
                <a href='logout.php'>Logout</a>
            </div>
        </div>
    </body>
    </html>";
} else {
    header("Location: quiz.php");
    exit();
}
?>