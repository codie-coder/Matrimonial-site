<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

$user_email = $_SESSION['email'];
$result = $conn->query("SELECT email, name, profile_pic FROM users WHERE email != '$user_email' AND verified = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select a User to Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2016/11/29/06/18/wedding-1867220_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(0, 0, 0, 0.5);
            background-blend-mode: overlay;
            min-height: 100vh;
            padding: 60px 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            font-family: 'Playfair Display', serif;
            color: #b03060;
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .user-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .user-list li {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s, background 0.2s;
        }

        .user-list li:hover {
            background: #ffe3ec;
            transform: translateY(-2px);
        }

        .user-list img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f7c7da;
        }

        .user-list a {
            text-decoration: none;
            font-weight: 500;
            color: #b03060;
            font-size: 1.05rem;
            flex-grow: 1;
        }

        .user-list a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .user-list li {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-list img {
                margin-bottom: 10px;
            }

            .user-list a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>💌 Start a Conversation</h2>
        <ul class="user-list">
            <?php while ($row = $result->fetch_assoc()):
                $pic = !empty($row['profile_pic']) ? "../" . $row['profile_pic'] : "https://cdn-icons-png.flaticon.com/512/847/847969.png";
            ?>
                <li>
                    <img src="<?= htmlspecialchars($pic) ?>" alt="Profile Picture">
                    <a href="chat.php?user=<?= urlencode($row['email']) ?>">
                        💑 <?= htmlspecialchars($row['name']) ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
