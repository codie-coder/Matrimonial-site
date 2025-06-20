<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../includes/db.php';

$sender = $_SESSION['email'];
$receiver = $_POST['receiver'] ?? '';
$message = trim($_POST['message'] ?? '');

// Prevent sending empty messages
if (empty($receiver) || empty($message)) {
    header("Location: chat.php?user=" . urlencode($receiver));
    exit;
}

// Premium check
$res = $conn->prepare("SELECT is_premium FROM users WHERE email = ?");
$res->bind_param("s", $sender);
$res->execute();
$premium_result = $res->get_result();
$is_premium = $premium_result->fetch_assoc()['is_premium'] ?? 0;

if (!$is_premium) {
    $today = date('Y-m-d');

    $limit_stmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE sender_email = ? AND DATE(sent_at) = ?");
    $limit_stmt->bind_param("ss", $sender, $today);
    $limit_stmt->execute();
    $limit_stmt->bind_result($count);
    $limit_stmt->fetch();
    $limit_stmt->close();

    if ($count >= 3) {
        echo "⚠️ Free users can send only 3 messages per day. <a href='../premium/subscribe.php'>Go Premium</a>";
        exit;
    }
}

// Insert the message
$stmt = $conn->prepare("INSERT INTO messages (sender_email, receiver_email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $sender, $receiver, $message);
$stmt->execute();
$stmt->close();

// Send notification (best-effort, don't block if fails)
$notifMsg = urlencode("You have a new message!");
@file_get_contents("../notifications/add.php?user=" . urlencode($receiver) . "&msg=" . $notifMsg);

// Redirect back to chat
header("Location: chat.php?user=" . urlencode($receiver));
exit;
