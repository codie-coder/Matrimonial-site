<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

$email = $_SESSION['email'];
$uploadMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    $targetDir = "../uploads/";
    $fileName = basename($file['name']);
    $targetFile = $targetDir . time() . "_" . $fileName;

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (in_array($fileType, $allowed) && $file['size'] <= 2 * 1024 * 1024) {
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $relativePath = str_replace("../", "", $targetFile);
            $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE email=?");
            $stmt->bind_param("ss", $relativePath, $email);
            $stmt->execute();
            $uploadMessage = "✅ Uploaded successfully!";
        } else {
            $uploadMessage = "❌ Failed to upload file.";
        }
    } else {
        $uploadMessage = "❌ Only JPG/PNG under 2MB allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Profile Picture</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2017/06/19/21/58/wedding-2426788_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(255,255,255,0.8);
            background-blend-mode: lighten;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .upload-box {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            max-width: 450px;
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #b03060;
            margin-bottom: 20px;
        }

        input[type="file"] {
            padding: 10px;
            background: #fff;
            border: 2px dashed #ccc;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 15px;
        }

        input[type="submit"] {
            background-color: #d63384;
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #a3195b;
        }

        .message {
            margin-top: 15px;
            color: #333;
            font-size: 0.95rem;
        }

        a {
            display: block;
            margin-top: 25px;
            color: #d63384;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="upload-box">
        <h2>📷 Upload Your Profile Picture</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="photo" accept="image/*" required>
            <br>
            <input type="submit" value="Upload">
        </form>
        <?php if (!empty($uploadMessage)): ?>
            <div class="message"><?= htmlspecialchars($uploadMessage) ?></div>
        <?php endif; ?>
        <a href="index.html">⬅️ Back to Profile</a>
    </div>

</body>
</html>
