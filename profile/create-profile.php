<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

$email = $_SESSION['email'];

// Fetch user data
$query = $conn->prepare("SELECT name, age, gender, bio, profile_pic FROM users WHERE email=?");
$query->bind_param("s", $email);
$query->execute();
$user = $query->get_result()->fetch_assoc();

$name = $user['name'] ?? '';
$age = $user['age'] ?? '';
$gender = $user['gender'] ?? '';
$bio = $user['bio'] ?? '';
$pic = $user['profile_pic'] ?? null;

$success = "";
$error = "";

// Default avatar URL (you can replace this with your own)
$defaultAvatar = "https://cdn-icons-png.flaticon.com/512/1077/1077114.png";

// Handle profile update (name, age, gender, bio)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && !isset($_POST['ajax_action'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $bio = $_POST['bio'];

    $stmt = $conn->prepare("UPDATE users SET name=?, age=?, gender=?, bio=? WHERE email=?");
    $stmt->bind_param("sssss", $name, $age, $gender, $bio, $email);
    if ($stmt->execute()) {
        $success = "✅ Profile updated successfully.";
    } else {
        $error = "❌ Failed to update profile.";
    }
    $stmt->close();
    // Refresh data after update
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle AJAX requests for profile pic upload and removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    if ($_POST['ajax_action'] === 'upload_photo') {
        if (isset($_FILES['photo'])) {
            $file = $_FILES['photo'];
            $targetDir = "../uploads/";
            $fileName = basename($file['name']);
            $targetFile = $targetDir . time() . "_" . $fileName;

            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (in_array($fileType, $allowed) && $file['size'] <= 2 * 1024 * 1024) {
                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    $relativePath = str_replace("../", "", $targetFile);

                    // Delete old profile pic file if exists and not default
                    if ($pic && file_exists("../$pic")) {
                        unlink("../$pic");
                    }

                    $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE email=?");
                    $stmt->bind_param("ss", $relativePath, $email);
                    $stmt->execute();
                    $stmt->close();

                    echo json_encode(['status' => 'success', 'msg' => 'Profile picture updated.', 'pic' => $relativePath]);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Failed to upload file.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Only JPG/PNG under 2MB allowed.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'No file uploaded.']);
        }
    } elseif ($_POST['ajax_action'] === 'remove_photo') {
        // Remove profile pic (set to null)
        if ($pic) {
            if (file_exists("../$pic")) {
                unlink("../$pic");
            }
            $stmt = $conn->prepare("UPDATE users SET profile_pic=NULL WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();

            echo json_encode(['status' => 'success', 'msg' => 'Profile picture removed.']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'No profile picture to remove.']);
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet" />
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: url('https://cdn.pixabay.com/photo/2016/12/18/20/08/wedding-1919142_1280.jpg') no-repeat center center/cover;
        background-blend-mode: overlay;
        background-color: rgba(0,0,0,0.5);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 60px 20px;
    }
    .container {
        background: rgba(255,255,255,0.95);
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 500px;
        position: relative;
    }
    h2 {
        text-align: center;
        color: #d63384;
        margin-bottom: 20px;
    }
    .profile-wrapper {
        position: relative;
        width: 130px;
        height: 130px;
        margin: 0 auto 25px;
        cursor: pointer;
    }
    .profile-pic {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #d63384;
        transition: 0.3s ease;
    }
    .upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: 0.3s ease;
        font-weight: 600;
        font-size: 1.1rem;
        user-select: none;
    }
    .profile-wrapper:hover .upload-overlay {
        opacity: 1;
    }
    input[type="file"] {
        display: none;
    }
    label {
        display: block;
        margin: 10px 0 5px;
        color: #333;
        font-weight: 500;
    }
    input, select, textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #bbb;
        border-radius: 8px;
        font-size: 0.95rem;
        margin-bottom: 15px;
        transition: border-color 0.3s;
    }
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #d63384;
        box-shadow: 0 0 6px #d63384aa;
    }
    textarea {
        resize: vertical;
        min-height: 80px;
    }
    .submit-btn {
        background-color: #d63384;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s;
        width: 100%;
        font-size: 1rem;
    }
    .submit-btn:hover {
        background-color: #b31c6d;
    }
    .message {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        text-align: center;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
    }
    .remove-btn {
        display: block;
        margin: 5px auto 25px auto;
        background: #e63946;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background 0.3s;
        max-width: 200px;
        text-align: center;
    }
    .remove-btn:hover {
        background: #a31d2b;
    }
    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #d63384;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        animation: spin 1s linear infinite;
        margin: 10px auto;
        display: none;
    }
    @keyframes spin {
        0% { transform: rotate(0deg);}
        100% { transform: rotate(360deg);}
    }
</style>
</head>
<body>

<div class="container">
    <h2>💖 Edit Your Profile</h2>

    <?php if (!empty($success)): ?>
        <div class="message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Profile Pic Upload -->
    <div class="profile-wrapper" id="profileWrapper" title="Click to change profile picture">
        <img src="<?= $pic ? "../" . htmlspecialchars($pic) : $defaultAvatar ?>" class="profile-pic" id="previewPic" alt="Profile Picture" />
        <div class="upload-overlay">Change</div>
    </div>

    <button class="remove-btn" id="removeBtn" style="<?= !$pic ? 'display:none;' : '' ?>">Remove Profile Picture</button>

    <input type="file" id="photoInput" accept="image/png, image/jpeg" />

    <div class="loading-spinner" id="spinner"></div>

    <!-- Profile Details Form -->
    <form method="POST" id="profileForm" novalidate>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required minlength="3" maxlength="50" value="<?= htmlspecialchars($name) ?>" />

        <label for="age">Age</label>
        <input type="number" id="age" name="age" min="13" max="120" value="<?= htmlspecialchars($age) ?>" />

        <label for="gender">Gender</label>
        <select id="gender" name="gender">
            <option value="" <?= $gender === '' ? 'selected' : '' ?>>Select Gender</option>
            <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>

        <label for="bio">Bio</label>
        <textarea id="bio" name="bio" maxlength="500"><?= htmlspecialchars($bio) ?></textarea>

        <button type="submit" class="submit-btn">Save Changes</button>
    </form>
</div>

<script>
const profileWrapper = document.getElementById('profileWrapper');
const photoInput = document.getElementById('photoInput');
const previewPic = document.getElementById('previewPic');
const removeBtn = document.getElementById('removeBtn');
const spinner = document.getElementById('spinner');

// Trigger file input when clicking the profile wrapper
profileWrapper.addEventListener('click', () => {
    photoInput.click();
});

// Upload photo via AJAX
photoInput.addEventListener('change', () => {
    if (photoInput.files.length === 0) return;
    const file = photoInput.files[0];
    if (!['image/jpeg', 'image/png'].includes(file.type)) {
        alert("❌ Only JPG or PNG images are allowed.");
        photoInput.value = "";
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert("❌ Maximum file size is 2MB.");
        photoInput.value = "";
        return;
    }

    const formData = new FormData();
    formData.append('ajax_action', 'upload_photo');
    formData.append('photo', file);

    spinner.style.display = 'block';

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        spinner.style.display = 'none';
        if (data.status === 'success') {
            previewPic.src = data.pic ? `../${data.pic}` : '<?= $defaultAvatar ?>';
            removeBtn.style.display = 'block';
            alert(data.msg);
        } else {
            alert(data.msg);
        }
        photoInput.value = "";
    })
    .catch(() => {
        spinner.style.display = 'none';
        alert("❌ Something went wrong during upload.");
        photoInput.value = "";
    });
});

// Remove profile picture via AJAX
removeBtn.addEventListener('click', () => {
    if (!confirm("Are you sure you want to remove your profile picture?")) return;

    const formData = new FormData();
    formData.append('ajax_action', 'remove_photo');

    spinner.style.display = 'block';

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        spinner.style.display = 'none';
        if (data.status === 'success') {
            previewPic.src = '<?= $defaultAvatar ?>';
            removeBtn.style.display = 'none';
            alert(data.msg);
        } else {
            alert(data.msg);
        }
    })
    .catch(() => {
        spinner.style.display = 'none';
        alert("❌ Something went wrong while removing.");
    });
});
</script>

</body>
</html>
