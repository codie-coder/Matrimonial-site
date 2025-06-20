<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

$email = $_SESSION['email'];

// Update premium status
$stmt = $conn->prepare("UPDATE users SET is_premium = 1 WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premium Activated</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2016/11/29/04/17/wedding-1866758_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 90%;
            max-width: 480px;
        }

        h2 {
            color: #b22260;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.1rem;
            color: #444;
            margin-bottom: 30px;
        }

        a {
            text-decoration: none;
            background-color: #e91e63;
            color: #fff;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        a:hover {
            background-color: #c2185b;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>💎 Premium Activated!</h2>
    <p>You're now a premium member with full access to exclusive features 🎉</p>
    <a href="../profile/index.html">Go to Your Profile</a>
</div>

</body>
</html>
