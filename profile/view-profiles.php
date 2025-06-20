<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

$email = $_SESSION['email'];
$defaultAvatar = "https://cdn-icons-png.flaticon.com/512/1077/1077114.png"; // Default avatar URL

// Securely get is_premium flag with prepared statement
$stmt = $conn->prepare("SELECT is_premium FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$is_premium = $res->fetch_assoc()['is_premium'] ?? 0;
$stmt->close();

// Fetch all verified users
$result = $conn->query("SELECT name, age, gender, bio, profile_pic FROM users WHERE verified=1 ORDER BY RAND()");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Verified Profiles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('https://cdn.pixabay.com/photo/2017/07/31/11/21/wedding-2560975_1280.jpg') no-repeat center center/cover;
            min-height: 100vh;
            padding: 40px 20px;
            backdrop-filter: brightness(0.95);
        }

        h2 {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            background-color: rgba(214, 51, 132, 0.85);
            padding: 12px 30px;
            border-radius: 20px;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .back-home {
            display: inline-block;
            margin-bottom: 20px;
            color: #fff;
            background-color: rgba(214, 51, 132, 0.85);
            padding: 10px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: background-color 0.3s;
        }

        .back-home:hover,
        .back-home:focus {
            background-color: #d4337a;
            outline: none;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.92);
            padding: 20px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            cursor: default;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* Locked card styles */
        .card.locked {
            cursor: pointer;
        }

        .card.locked img {
            filter: brightness(0.5) blur(4px);
            transition: filter 0.3s ease;
            border-radius: 12px;
        }

        .card.locked:hover img {
            filter: brightness(0.7) blur(3px);
        }

        /* Lock overlay icon */
        .card.locked::after {
            content: "🔒";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.85);
            pointer-events: none;
            user-select: none;
            text-shadow: 0 0 6px rgba(0,0,0,0.7);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .info {
            font-size: 0.95rem;
            color: #555;
            margin: 4px 0;
        }

        .bio {
            font-style: italic;
            margin-top: 8px;
            color: #666;
        }

        .lock-note {
            font-size: 0.85rem;
            color: #b31c5c;
            margin-top: -8px;
            margin-bottom: 12px;
            cursor: pointer;
            text-decoration: underline;
        }

        .lock-note:hover,
        .lock-note:focus {
            color: #d63384;
            outline: none;
        }

        @media (max-width: 600px) {
            .card img {
                height: 170px;
            }
        }
    </style>
</head>
<body>

    <a href="../profile/index.php" class="back-home" tabindex="0">← Back to Home</a>

    <h2>🌸 All Verified Profiles</h2>

    <?php if ($result->num_rows === 0): ?>
        <p style="color:white; text-align:center; margin-top: 50px;">No verified profiles found.</p>
    <?php else: ?>
        <div class="grid">
            <?php while ($row = $result->fetch_assoc()):
                $hasPic = !empty($row['profile_pic']);
                $imgSrc = $hasPic ? "../" . htmlspecialchars($row['profile_pic']) : $defaultAvatar;
                $locked = (!$is_premium && $hasPic); // locked if not premium and has profile pic
                // We'll make whole card clickable if locked
                $cardClass = $locked ? 'card locked' : 'card';
                $upgradeUrl = '../premium/upgrade.php';
            ?>
                <div
                    class="<?= $cardClass ?>"
                    <?php if($locked): ?>
                        role="button"
                        tabindex="0"
                        onclick="window.location.href='<?= $upgradeUrl ?>'"
                        onkeypress="if(event.key === 'Enter' || event.key === ' ') window.location.href='<?= $upgradeUrl ?>'"
                        aria-label="Upgrade to Premium to see full photo and bio of <?= htmlspecialchars($row['name']) ?>"
                    <?php endif; ?>
                >
                    <img src="<?= $imgSrc ?>" alt="Profile picture of <?= htmlspecialchars($row['name']) ?>" loading="lazy" />

                    <?php if ($locked): ?>
                        <div 
                            class="lock-note"
                            tabindex="0"
                            role="button"
                            onclick="window.location.href='<?= $upgradeUrl ?>'"
                            onkeypress="if(event.key === 'Enter' || event.key === ' ') window.location.href='<?= $upgradeUrl ?>'"
                            title="Upgrade to Premium to see full photo and bio"
                        >
                            🔒 Photo for Premium Members - Click to Upgrade
                        </div>
                    <?php endif; ?>

                    <div class="name"><?= htmlspecialchars($row['name']) ?></div>
                    <div class="info">Age: <?= htmlspecialchars($row['age']) ?></div>
                    <div class="info">Gender: <?= htmlspecialchars($row['gender']) ?></div>

                    <?php if ($is_premium): ?>
                        <div class="bio"><?= htmlspecialchars($row['bio']) ?></div>
                    <?php else: ?>
                        <div
                            class="bio lock-note"
                            tabindex="0"
                            role="button"
                            onclick="window.location.href='<?= $upgradeUrl ?>'"
                            onkeypress="if(event.key === 'Enter' || event.key === ' ') window.location.href='<?= $upgradeUrl ?>'"
                            title="Upgrade to Premium to read bio"
                        >
                            🔒 Bio available for Premium Members - Click to Upgrade
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

</body>
</html>
