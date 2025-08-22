<?php
session_start();
require 'db_connect.php';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: home.html");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-image: url('login.jpg'); /* off-white background */
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: rgba(30, 30, 30, 0.8); /* dark glass */
            backdrop-filter: blur(12px);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.35);
            text-align: center;
            color: #fff;
            animation: fadeIn 0.8s ease-in-out;
        }

        .form-container h2 {
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: #f8f8f8;
            letter-spacing: 1px;
        }

        .error {
            color: #ff6b6b;
            font-size: 14px;
            margin-bottom: 10px;
        }

        label {
            display: block;
            text-align: left;
            margin-top: 15px;
            font-weight: 500;
            color: #ddd;
        }

        .input-group {
            position: relative;
            margin-top: 5px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 40px 12px 12px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background: rgba(255,255,255,0.9);
            color: #333;
        }

        .input-group i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c63ff;
            cursor: pointer;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: linear-gradient(135deg, #fbc531, #8c7ae6); /* gold + purple */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(140, 122, 230, 0.4);
        }

        p {
            margin-top: 15px;
            font-size: 14px;
            color: #eee;
        }

        a {
            color: #fbc531;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .form-container {
                padding: 1.5rem;
                width: 90%;
            }
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Login</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <label>Email:</label>
        <div class="input-group">
            <input type="email" name="email" placeholder="Enter your email" required>
            <i class="fa fa-envelope"></i>
        </div>

        <label>Password:</label>
        <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            <i class="fa fa-eye" id="togglePassword"></i>
        </div>

        <button type="submit" name="login">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<script>
    // Toggle password visibility
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");

    togglePassword.addEventListener("click", function () {
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        this.classList.toggle("fa-eye-slash");
    });
</script>
</body>
</html>
