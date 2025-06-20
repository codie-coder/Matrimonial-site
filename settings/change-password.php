<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email'];
    $current = $_POST['current'];
    $new = $_POST['new'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_pass);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($current, $db_pass)) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed, $email);
        $update->execute();
        $feedback = "✅ Password changed successfully.";
    } else {
        $feedback = "❌ Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2019/07/09/10/51/wedding-4326261_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-box {
            background: rgba(255, 255, 255, 0.97);
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            width: 90%;
            max-width: 450px;
            text-align: center;
        }

        h3 {
            color: #c2185b;
            margin-bottom: 25px;
        }

        input[type="password"], input[type="submit"] {
            padding: 12px;
            width: 90%;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 1rem;
        }

        input[type="submit"] {
            background-color: #d63384;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #a91f63;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            background: #eee;
            color: #444;
            padding: 10px 20px;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .back-link:hover {
            background: #ddd;
        }

        .feedback {
            margin-top: 10px;
            font-size: 0.95rem;
            color: #b2004d;
        }
    </style>
</head>
<body>

    <div class="form-box">
        <h3>🔐 Change Password</h3>

        <?php if (!empty($feedback)): ?>
            <div class="feedback"><?= htmlspecialchars($feedback) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="password" name="current" placeholder="Current Password" required>
            <input type="password" name="new" placeholder="New Password" required>
            <input type="submit" value="Update Password">
        </form>

        <a href="index.php" class="back-link">⬅️ Back to Settings</a>
    </div>

</body>
</html>
