<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_GET['user'])) {
    header("Location: inbox.php");
    exit;
}
include '../includes/db.php';

$sender = $_SESSION['email'];
$receiver = $_GET['user'];

// Fetch receiver's name
$name_stmt = $conn->prepare("SELECT name FROM users WHERE email = ?");
$name_stmt->bind_param("s", $receiver);
$name_stmt->execute();
$name_result = $name_stmt->get_result();
$receiver_name = $name_result->fetch_assoc()['name'] ?? 'User';

// Fetch messages
$stmt = $conn->prepare("SELECT * FROM messages WHERE 
    (sender_email=? AND receiver_email=?) OR 
    (sender_email=? AND receiver_email=?) ORDER BY sent_at ASC");
$stmt->bind_param("ssss", $sender, $receiver, $receiver, $sender);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with <?= htmlspecialchars($receiver_name) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2017/10/18/18/20/wedding-2860898_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-blend-mode: overlay;
            background-color: rgba(255, 255, 255, 0.7);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .chat-container {
            width: 100%;
            max-width: 700px;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            height: 90vh;
            overflow: hidden;
        }

        .chat-header {
            padding: 20px;
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: #c2185b;
            border-bottom: 1px solid #eee;
        }

        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: #fafafa;
        }

        .message {
            padding: 12px 18px;
            border-radius: 20px;
            max-width: 70%;
            word-wrap: break-word;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .me {
            align-self: flex-end;
            background-color: #ffd6e6;
            color: #880e4f;
        }

        .other {
            align-self: flex-start;
            background-color: #f0f0f0;
            color: #333;
        }

        .chat-form {
            display: flex;
            padding: 15px 20px;
            border-top: 1px solid #eee;
            background: #fff;
        }

        .chat-form input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-size: 1rem;
            outline: none;
        }

        .chat-form input[type="submit"] {
            margin-left: 10px;
            padding: 12px 18px;
            background-color: #d63384;
            color: white;
            font-weight: 500;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .chat-form input[type="submit"]:hover {
            background-color: #b31c6d;
        }

        .back-link {
            text-align: center;
            padding: 10px;
            background: #fff0f7;
            color: #d63384;
            text-decoration: none;
            display: block;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">💬 Chat with <?= htmlspecialchars($receiver_name) ?></div>
        <div class="chat-box" id="chat-box">
            <?php while ($msg = $result->fetch_assoc()): ?>
                <div class="message <?= $msg['sender_email'] == $sender ? 'me' : 'other' ?>">
                    <strong><?= $msg['sender_email'] == $sender ? 'You' : htmlspecialchars($receiver_name) ?>:</strong><br>
                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                </div>
            <?php endwhile; ?>
        </div>

        <form class="chat-form" action="send.php" method="POST">
            <input type="hidden" name="receiver" value="<?= htmlspecialchars($receiver) ?>">
            <input type="text" name="message" placeholder="Type your message..." required autocomplete="off">
            <input type="submit" value="Send">
        </form>
        <a href="inbox.php" class="back-link">⬅️ Back to Inbox</a>
    </div>

    <script>
        // Auto-scroll to bottom
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>
