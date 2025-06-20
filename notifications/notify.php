<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$email = $_SESSION['email'];
$result = $conn->query("SELECT COUNT(*) FROM notifications WHERE user_email='$email' AND is_read=0");
$count = $result->fetch_row()[0];
echo json_encode(['count' => $count]);
