<?php
session_start();
$admin_email = "admin@matrimonial.com";
$admin_password = "admin123";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Invalid admin credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2016/11/21/15/47/roses-1846125_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(255,255,255,0.8);
            background-blend-mode: lighten;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #c2185b;
            margin-bottom: 25px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-size: 1rem;
        }

        input[type="submit"] {
            background-color: #d63384;
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #a3195b;
        }

        .error {
            color: #b00020;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>🔐 Admin Login</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>
    </div>

</body>
</html>
