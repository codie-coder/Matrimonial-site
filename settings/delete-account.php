<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

$email = $_SESSION['email'];
$feedback = "";

if (isset($_POST['confirm']) && strtoupper(trim($_POST['confirm'])) === 'YES') {
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    session_destroy();
    $feedback = "✅ Your account has been deleted permanently.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2016/03/27/21/16/wedding-1284244_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-box {
            background: rgba(255, 255, 255, 0.96);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 480px;
            text-align: center;
        }

        h3 {
            color: #b71c1c;
            margin-bottom: 20px;
        }

        p {
            color: #333;
            font-size: 1rem;
            margin-bottom: 25px;
        }

        input[type="text"], input[type="submit"] {
            width: 90%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-size: 1rem;
            margin: 10px 0;
        }

        input[type="submit"] {
            background-color: #e53935;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #c62828;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #555;
            text-decoration: none;
            background: #eee;
            padding: 10px 18px;
            border-radius: 10px;
        }

        .back-link:hover {
            background: #ddd;
        }

        .feedback {
            margin-top: 15px;
            font-size: 1rem;
            color: #006400;
        }
    </style>
</head>
<body>

<div class="form-box">
    <h3>💔 Delete Account</h3>

    <?php if (!empty($feedback)): ?>
        <div class="feedback"><?= htmlspecialchars($feedback) ?></div>
    <?php else: ?>
        <p>Are you sure you want to delete your account?<br>This action is <strong>permanent</strong>.</p>
        <form method="POST">
            <input type="text" name="confirm" placeholder='Type "YES" to confirm' required>
            <input type="submit" value="Delete Account">
        </form>
        <a href="index.php" class="back-link">⬅️ Back to Settings</a>
    <?php endif; ?>
</div>

</body>
</html>
