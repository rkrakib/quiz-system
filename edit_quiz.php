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

// Fetch quiz data
$sql = "SELECT * FROM quizzes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Quiz not found.");
}

$quiz = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $duration = intval($_POST['duration']);

    if ($title === "" || $duration <= 0) {
        $error = "Title is required and duration must be greater than 0.";
    } else {
        $update = $conn->prepare("UPDATE quizzes SET title = ?, duration_minutes = ? WHERE id = ?");
        $update->bind_param("sii", $title, $duration, $id);

        if ($update->execute()) {
            $success = "Quiz updated successfully!";
            // Reload updated data
            $quiz['title'] = $title;
            $quiz['duration_minutes'] = $duration;
        } else {
            $error = "Error updating quiz: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<htm
