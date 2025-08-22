<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require 'db_connect.php';

if (!isset($_GET['id'])) {
    die("Quiz ID is required.");
}

$id = intval($_GET['id']);

// Optional: Delete associated questions first to maintain referential integrity
$delete_questions = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
$delete_questions->bind_param("i", $id);
$delete_questions->execute();

// Delete the quiz itself
$delete_quiz = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
$delete_quiz->bind_param("i", $id);

if ($delete_quiz->execute()) {
    header("Location: admin_dashboard.php?msg=Quiz+deleted");
    exit();
} else {
    die("Error deleting quiz: " . $conn->error);
}
