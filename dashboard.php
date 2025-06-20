<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?auto=format&fit=crop&w=1600&q=80'); /* Couple BG */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 600px;
        }

        h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            color: #d63384;
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            margin-top: 1rem;
            background-color: #ff6f91;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #ff3f75;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <h2>Welcome to the Dashboard,<br> <?= htmlspecialchars($_SESSION['email']) ?> 💌</h2>
        <a href="profile/index.php" class="btn">Go to Profile Section</a>
    </div>
</body>
</html>
