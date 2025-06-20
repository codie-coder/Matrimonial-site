<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $user_otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($otp);
    $stmt->fetch();
    $stmt->close();

    if ($otp == $user_otp) {
        $update = $conn->prepare("UPDATE users SET verified = 1 WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();
        $message = "<div class='message success'>✅ OTP Verified. You can now login.</div>";
    } else {
        $message = "<div class='message error'>❌ Invalid OTP. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Open Sans', sans-serif;
            background-image: url('https://images.pexels.com/photos/1028726/pexels-photo-1028726.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');

            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .otp-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 90%;
            max-width: 400px;
        }

        .otp-card h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 38px;
            color: #b97b85;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 12px;
            width: 100%;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #b97b85;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #a26570;
        }

        .message {
            margin-top: 15px;
            font-size: 15px;
            font-weight: bold;
        }

        .success {
            color: #2e7d32;
        }

        .error {
            color: #d32f2f;
        }

        @media (max-width: 480px) {
            .otp-card {
                padding: 25px;
            }

            .otp-card h2 {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="otp-card">
        <h2>OTP Verification</h2>
        <?php if (isset($message)) echo $message; ?>
        <form method="POST">
            <input type="text" name="otp" placeholder="Enter your OTP" required>
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
            <input type="submit" value="Verify OTP">
        </form>
    </div>
</body>
</html>
