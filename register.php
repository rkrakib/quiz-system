<?php
session_start();
require 'db_connect.php';

// Enable error reporting (remove later in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if ($name == "" || $email == "" || $password == "" || $confirm_password == "") {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Email already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'student'; // default role

                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
                    if ($stmt->execute()) {
                        $success = "Registration successful! You can now log in.";
                    } else {
                        $error = "Error inserting data: " . $stmt->error;
                    }
                } else {
                    $error = "Prepare failed: " . $conn->error;
                }
            }
            $stmt->close();
        } else {
            $error = "Prepare failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* same CSS as before */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            width: 350px;
            text-align: center;
            animation: fadeIn 0.7s ease-in-out;
        }
        .form-container h2 { margin-bottom: 20px; font-size: 24px; color: #333; }
        label { display: block; text-align: left; margin: 10px 0 5px; font-weight: bold; color: #444; }
        input {
            width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;
            outline: none; transition: 0.3s;
        }
        input:focus { border-color: #6a11cb; box-shadow: 0 0 5px rgba(106, 17, 203, 0.4); }
        button {
            margin-top: 15px; width: 100%; padding: 12px; border: none;
            border-radius: 8px; background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        button:hover { transform: scale(1.05); background: linear-gradient(135deg, #2575fc, #6a11cb); }
        p { margin-top: 15px; color: #555; }
        a { color: #2575fc; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
        .error {
            background: #ffe0e0; color: #d9534f; padding: 10px;
            border-radius: 6px; margin-bottom: 15px;
        }
        .message {
            background: #e0ffe6; color: #28a745; padding: 10px;
            border-radius: 6px; margin-bottom: 15px;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-10px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Student Registration</h2>

    <?php
    if (!empty($error)) echo "<p class='error'>$error</p>";
    if (!empty($success)) echo "<p class='message'>$success</p>";
    ?>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
