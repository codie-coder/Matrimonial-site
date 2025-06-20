<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password, verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashed_pass, $verified);
    $stmt->fetch();

    if ($verified && password_verify($password, $hashed_pass)) {
        session_start();
        $_SESSION['email'] = $email;
        header("Location: ../profile/index.php");
        exit;
    } else {
        $error = "❌ Invalid credentials or email not verified.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Matrimonial Site</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://images.pexels.com/photos/265947/pexels-photo-265947.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(4px);
            padding: 2.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 420px;
            text-align: center;
            animation: fadeIn 1.2s ease forwards;
            opacity: 0;
        }

        h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 2.8rem;
            color: #d63384;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        input[type="email"],
        input[type="password"] {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
        }

        input[type="submit"] {
            background-color: #ff6f91;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #ff3f75;
        }

        .register-link {
            margin-top: 2rem;
        }

        .register-btn {
            display: inline-block;
            background: linear-gradient(45deg, #f78fb3, #ff6f91);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 6px 15px rgba(247, 143, 179, 0.4);
        }

        .register-btn:hover {
            background: linear-gradient(45deg, #ff6f91, #f78fb3);
            transform: scale(1.05);
        }

        .error {
            color: red;
            margin-top: 1rem;
            font-weight: bold;
        }

        /* Hidden label for accessibility */
        label {
            position: absolute;
            left: -9999px;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <h1>Welcome Back 💖</h1>
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>

            <input type="submit" value="Login">
        </form>

        <div class="register-link">
            <a href="register.php" class="register-btn">💍 Begin Your Story</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
