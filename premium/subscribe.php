<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upgrade to Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2017/03/06/13/48/wedding-2122356_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 90%;
            max-width: 500px;
        }

        h2 {
            color: #b22260;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.1rem;
            color: #444;
            margin-bottom: 20px;
        }

        .price {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 25px;
        }

        button {
            background-color: #e91e63;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #c2185b;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #b22260;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>💎 Upgrade to Premium</h2>
    <p>Unlock full features like:</p>
    <ul style="text-align: left; color: #555; font-size: 1rem;">
        <li>✅ Unlimited messages</li>
        <li>✅ Full profile & photo access</li>
        <li>✅ Appear first in searches</li>
        <li>✅ Connect with verified members</li>
    </ul>
    <div class="price">Only ₹99 (Fake)</div>

    <form action="confirm.php" method="POST">
        <button type="submit">Pay Now (Simulated)</button>
    </form>

    <a href="../profile/index.html">⬅️ Back to Profile</a>
</div>

</body>
</html>
