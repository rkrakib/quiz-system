<?php
session_start();
require 'db_connect.php';

// Only students can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// If a quiz has been selected
if (isset($_GET['quiz_id'])) {
    $quiz_id = (int)$_GET['quiz_id'];

    // Get quiz info
    $quizStmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
    $quizStmt->bind_param("i", $quiz_id);
    $quizStmt->execute();
    $quiz = $quizStmt->get_result()->fetch_assoc();

    // Fetch questions
    $questionResult = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
    $questionResult->bind_param("i", $quiz_id);
    $questionResult->execute();
    $questions = $questionResult->get_result();

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?php echo htmlspecialchars($quiz['title']); ?></title>
        <link rel="stylesheet" href="style.css">
        <style>
            .question { margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
            .timer { font-size: 20px; font-weight: bold; color: red; }
        </style>
    </head>
    <head>
    <link rel="stylesheet" href="quiz.css">
</head>

<body>
    <div class="quiz-container">
        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
        <p>Time Remaining: <span class="timer" id="timer"></span></p>

        <form id="quizForm" method="POST" action="submit_quiz.php">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

           <ol class="question-list">
<?php while ($q = $questions->fetch_assoc()): ?>
    <li class="question">
        <p><strong><?php echo htmlspecialchars($q['question_text']); ?></strong></p>
        <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A"> <?php echo htmlspecialchars($q['option_a']); ?></label>
        <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B"> <?php echo htmlspecialchars($q['option_b']); ?></label>
        <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C"> <?php echo htmlspecialchars($q['option_c']); ?></label>
        <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D"> <?php echo htmlspecialchars($q['option_d']); ?></label>
    </li>
<?php endwhile; ?>
</ol>


            <button type="submit">Submit Quiz</button>
        </form>
    </div>

    <script>
        let totalSeconds = <?php echo $quiz['duration_minutes'] * 60; ?>;
        let timerDisplay = document.getElementById("timer");

        function updateTimer() {
            let minutes = Math.floor(totalSeconds / 60);
            let seconds = totalSeconds % 60;
            timerDisplay.textContent = minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

            if (totalSeconds <= 0) {
                document.getElementById("quizForm").submit();
            }
            totalSeconds--;
        }
        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>

    </html>
    <?php
    exit();
}

// Show available quizzes
$now = date('Y-m-d H:i:s');
$quizList = $conn->prepare("SELECT * FROM quizzes");
// The bind_param line is no longer needed because there are no parameters
$quizList->execute();
$result = $quizList->get_result();
$quizList->execute();
$result = $quizList->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IXL Math</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <div class="background">
      
   <nav class="navbar">
    <div class="nav-left">
        MyLearningSite
    </div>
    <div class="nav-right">
        <div class="dropdown">
            <a href="home.html" class="has-arrow">&#10148; Quiz</a>
            <div class="dropdown-content">
            </div>
        </div>
        <div class="dropdown">
            <a href="learn.html">Learn</a>
            <div class="dropdown-content">
               
            </div>
        </div>
        
        <div class="dropdown">
            <a href="#">E-Book</a>
            <div class="dropdown-content">
               
            </div>
        </div>
        <div class="dropdown">
            <a href="#">Video Lesson</a>
            <div class="dropdown-content">
                
            </div>
        </div>
    </div>
</nav>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Available Quizzes</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    .center-div {
    background: white;
    padding: 15px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 60%;
    max-width: 700px;
    margin: 50px auto 0 auto;  /* This centers it horizontally */
}

        .blank{
            height: 150px;

        }
        .avl-quiz{
            text-align: center;
            color:#2196f3;
        }
        /* .start-button a {
            display:flex;
    background: orange;
    color: white;
    padding: 5px 5px;
    border-radius: 4px;
    margin-left: 550px;
    text-decoration: none;
    font-weight: bold;
        } */
         .start-button ol {
    list-style-type: upper-alpha; /* keeps A, B, C... */
    padding-left: 0;
}

.start-button li {
    display: flex;               /* horizontal layout */
    justify-content: space-between; /* space between text and button */
    align-items: center;         /* vertically align text and button */
    margin-bottom: 10px;
}

.start-button a {
    background: orange;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
}

</style>
<body>
<div class="blank"> </div>
<div class="center-div">
<h2 class="avl-quiz" >Available Quizzes</h2>
<div class="start-button">        
<?php if ($result->num_rows > 0): ?>
    <ol type="A">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <?php echo htmlspecialchars($row['title']); ?> 
                <a href="quiz.php?quiz_id=<?php echo $row['id']; ?>">Start Quiz</a>
            </li>
        <?php endwhile; ?>
    </ol>
</div>

        <?php else: ?>
            <p>No quizzes available right now.</p>
        <?php endif; ?>

            <p><a href="logout.php">Logout</a></p>
</div>

    
</body>
</html>
