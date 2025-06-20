<?php
include '../includes/db.php';
include '../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $otp = rand(100000, 999999);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, otp) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $otp);

    if ($stmt->execute()) {
        sendOTP($email, $otp);
        header("Location: verify-otp.php?email=$email");
    } else {
        echo "<p style='color:red; text-align:center;'>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Matrimonial Site</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://images.pexels.com/photos/256737/pexels-photo-256737.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(2px);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            color: #d63384;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 12px;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
        }

        input[type="submit"] {
            background-color: #ff6f91;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #ff3f75;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <h1>Start Your Journey 💌</h1>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
