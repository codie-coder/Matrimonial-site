<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../includes/db.php';

$result = $conn->query("SELECT id, name, email, age, gender, verified FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2016/12/28/09/36/wedding-1937027_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(255, 255, 255, 0.85);
            background-blend-mode: lighten;
            padding: 40px 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.1);
        }

        h2 {
            color: #b03060;
            text-align: center;
            margin-bottom: 30px;
        }

        a.logout {
            display: inline-block;
            background-color: #d63384;
            color: white;
            padding: 10px 18px;
            border-radius: 12px;
            text-decoration: none;
            float: right;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #fce4ec;
            color: #880e4f;
        }

        tr:nth-child(even) {
            background-color: #fff0f5;
        }

        .delete-link {
            color: #d63333;
            text-decoration: none;
            font-weight: bold;
        }

        .delete-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            table, th, td {
                font-size: 0.85rem;
            }

            a.logout {
                float: none;
                display: block;
                text-align: center;
                margin: 0 auto 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout">Logout</a>
        <h2>🛡️ Admin Dashboard - User List</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Verified</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['age']) ?></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td><?= $row['verified'] ? '✅' : '❌' ?></td>
                <td>
                    <a class="delete-link" href="delete-user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
