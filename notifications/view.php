<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

$email = $_SESSION['email'];
$result = $conn->query("SELECT * FROM notifications WHERE user_email='$email' ORDER BY created_at DESC");

// Mark all as read
$conn->query("UPDATE notifications SET is_read=1 WHERE user_email='$email'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://cdn.pixabay.com/photo/2016/11/23/15/42/wedding-1854074_1280.jpg') no-repeat center center/cover;
            margin: 0;
            padding: 60px 20px;
            min-height: 100vh;
            background-blend-mode: overlay;
            background-color: rgba(255, 255, 255, 0.85);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            color: #d63384;
            margin-bottom: 25px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            padding: 14px 18px;
            margin-bottom: 10px;
            background-color: #fff0f5;
            border-radius: 10px;
            border-left: 5px solid #d63384;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
            color: #333;
        }

        li em {
            font-size: 0.8rem;
            color: #777;
            margin-left: 10px;
        }

        .status {
            margin-right: 10px;
        }

        a.back-link {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            background-color: #d63384;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            transition: 0.3s;
        }

        a.back-link:hover {
            background-color: #b6206b;
        }

        @media (max-width: 600px) {
            li {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🔔 Notifications</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <div>
                    <span class="status"><?= $row['is_read'] ? "🟢" : "🔴" ?></span>
                    <?= htmlspecialchars($row['message']) ?>
                    <em><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></em>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>

    <a class="back-link" href="../profile/index.html">⬅️ Back to Profile</a>
</div>

</body>
</html>
