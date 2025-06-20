<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../admin/login.php");
    exit;
}
include '../includes/db.php';

// Total Users
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];

// Verified Users
$verified_users = $conn->query("SELECT COUNT(*) FROM users WHERE verified = 1")->fetch_row()[0];

// Gender breakdown
$gender_stats = $conn->query("SELECT gender, COUNT(*) as count FROM users GROUP BY gender");

// New registrations (last 7 days)
$new_users = $conn->query("SELECT COUNT(*) FROM users WHERE created_at >= CURDATE() - INTERVAL 7 DAY")->fetch_row()[0];
?>

<h2>📊 Matrimonial Site Analytics</h2>
<ul>
    <li><strong>Total Users:</strong> <?= $total_users ?></li>
    <li><strong>Verified Users:</strong> <?= $verified_users ?></li>
    <li><strong>New Users (last 7 days):</strong> <?= $new_users ?></li>
</ul>

<h3>👫 Gender Distribution:</h3>
<table border="1" cellpadding="6">
    <tr><th>Gender</th><th>Count</th></tr>
    <?php while($row = $gender_stats->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= $row['count'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="../admin/dashboard.php">⬅️ Back to Admin</a>
