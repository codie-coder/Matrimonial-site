<?php
include '../includes/db.php';

$user = $_GET['user'] ?? '';
$msg = $_GET['msg'] ?? '';

// Avoid recursive call
if (!empty($user) && !empty($msg)) {
    // Insert notification
    $stmt = $conn->prepare("INSERT INTO notifications (user_email, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $msg);
    $stmt->execute();
    echo "✅ Notification added.";

    // Don't call itself here — that would be recursive!
    // However, you can log, trigger webhook, or notify admin if needed.
} else {
    echo "❌ Missing params.";
}
?>
