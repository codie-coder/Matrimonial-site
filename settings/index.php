<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2017/01/06/19/15/wedding-1958935_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(255, 255, 255, 0.85);
            background-blend-mode: lighten;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .settings-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
            text-align: center;
        }

        h2 {
            color: #c2185b;
            margin-bottom: 30px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-bottom: 30px;
        }

        li {
            margin: 15px 0;
        }

        a {
            text-decoration: none;
            font-weight: 500;
            color: #b2004d;
            font-size: 1.05rem;
            background-color: #ffe4ec;
            padding: 10px 16px;
            border-radius: 12px;
            display: inline-block;
            transition: all 0.3s ease;
        }

        a:hover {
            background-color: #ffbad2;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
            background-color: #d63384;
            color: #fff;
            padding: 10px 18px;
            border-radius: 12px;
        }

        .back-link:hover {
            background-color: #a3195b;
        }
    </style>
</head>
<body>

    <div class="settings-box">
        <h2>⚙️ Account Settings</h2>
        <ul>
            <li><a href="change-password.php">🔐 Change Password</a></li>
            <li><a href="delete-account.php">❌ Delete Account</a></li>
        </ul>
        <a class="back-link" href="../profile/index.html">⬅️ Back to Profile</a>
    </div>

</body>
</html>
