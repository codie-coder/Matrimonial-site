<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/db.php';

// Input validation and sanitization
$minAge = filter_input(INPUT_GET, 'min_age', FILTER_VALIDATE_INT, [
    'options' => ['default' => 18, 'min_range' => 18, 'max_range' => 100]
]);
$maxAge = filter_input(INPUT_GET, 'max_age', FILTER_VALIDATE_INT, [
    'options' => ['default' => 100, 'min_range' => 18, 'max_range' => 100]
]);
$gender = filter_input(INPUT_GET, 'gender', FILTER_SANITIZE_STRING) ?? '';
$keyword = filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_STRING) ?? '';

if ($minAge > $maxAge) {
    // Swap if minAge is greater than maxAge
    [$minAge, $maxAge] = [$maxAge, $minAge];
}

// Pagination variables
$resultsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $resultsPerPage;

// Base query part
$queryBase = "FROM users WHERE verified=1 AND age BETWEEN ? AND ?";
$params = [$minAge, $maxAge];
$types = "ii";

if (!empty($gender)) {
    $queryBase .= " AND gender = ?";
    $types .= "s";
    $params[] = $gender;
}
if (!empty($keyword)) {
    $queryBase .= " AND bio LIKE ?";
    $types .= "s";
    $params[] = "%$keyword%";
}

// Get total results count for pagination
$countQuery = "SELECT COUNT(*) as total " . $queryBase;
$stmt = $conn->prepare($countQuery);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$countResult = $stmt->get_result()->fetch_assoc();
$totalResults = $countResult['total'];
$stmt->close();

$totalPages = ceil($totalResults / $resultsPerPage);

// Query with LIMIT and OFFSET for current page
$dataQuery = "SELECT name, age, gender, bio " . $queryBase . " LIMIT ? OFFSET ?";
$paramsWithLimit = $params;
$typesWithLimit = $types . "ii";
$paramsWithLimit[] = $resultsPerPage;
$paramsWithLimit[] = $offset;

$stmt = $conn->prepare($dataQuery);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param($typesWithLimit, ...$paramsWithLimit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Your Match</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&family=Playfair+Display&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2016/03/09/09/17/roses-1246491_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(0, 0, 0, 0.4);
            background-blend-mode: overlay;
            min-height: 100vh;
            padding: 60px 20px;
        }

        .container {
            max-width: 750px;
            margin: auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        h2 {
            font-family: 'Playfair Display', serif;
            text-align: center;
            color: #c0397c;
            margin-bottom: 25px;
            font-size: 2rem;
        }

        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: 500;
            color: #444;
        }

        input[type="number"],
        input[type="text"],
        select {
            width: 100%;
            padding: 10px 12px;
            font-size: 0.95rem;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        input[type="submit"],
        input[type="reset"] {
            background: #d63384;
            color: white;
            border: none;
            padding: 12px 20px;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1rem;
            width: 48%;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            background: #b31c6d;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .results {
            margin-top: 30px;
        }

        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .card strong {
            display: inline-block;
            width: 90px;
            color: #7a1e52;
        }

        .icon {
            margin-right: 5px;
        }

        .divider {
            border-top: 1px solid #eee;
            margin: 15px 0;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 10px;
            flex-wrap: wrap;
        }
        .pagination a,
        .pagination span {
            padding: 8px 14px;
            background: #d63384;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            user-select: none;
            transition: background 0.3s ease;
        }
        .pagination a:hover {
            background: #b31c6d;
        }
        .pagination .current-page {
            background: #7a1e52;
            cursor: default;
        }
        .pagination .disabled {
            background: #ccc;
            cursor: not-allowed;
            color: #888;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 1.6rem;
            }

            input[type="submit"],
            input[type="reset"] {
                width: 100%;
                margin-bottom: 12px;
            }

            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>💍 Find Your Perfect Match</h2>

    <form method="GET" id="searchForm">
        <label>Age Range:</label>
        <input type="number" name="min_age" value="<?= $minAge ?>" min="18" max="100" required> to 
        <input type="number" name="max_age" value="<?= $maxAge ?>" min="18" max="100" required>

        <label>Gender:</label>
        <select name="gender">
            <option value="" <?= $gender === '' ? 'selected' : '' ?>>Any</option>
            <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>

        <label>Keyword in Bio:</label>
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>">

        <div class="buttons">
            <input type="submit" value="Search Matches 💌">
            <input type="reset" value="Reset Filters" onclick="window.location.href='<?= basename($_SERVER['PHP_SELF']) ?>'">
        </div>
    </form>

    <?php if (!empty($_GET)): ?>
    <p style="color:#7a1e52; font-style: italic; margin-bottom: 15px; text-align:center;">
        Showing results for: Age <?= $minAge ?> to <?= $maxAge ?>,
        Gender: <?= $gender ?: 'Any' ?>,
        Keyword: <?= htmlspecialchars($keyword) ?: 'None' ?>
    </p>
    <?php endif; ?>

    <div class="results">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div><span class="icon">👤</span><strong>Name:</strong> <?= htmlspecialchars($row['name']) ?></div>
                    <div><span class="icon">🎂</span><strong>Age:</strong> <?= htmlspecialchars($row['age']) ?></div>
                    <div><span class="icon">⚧️</span><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></div>
                    <div class="divider"></div>
                    <div><span class="icon">💬</span><strong>Bio:</strong> <?= htmlspecialchars($row['bio']) ?></div>
                </div>
            <?php endwhile; ?>

            <!-- Pagination -->
            <div class="pagination" role="navigation" aria-label="Pagination Navigation">
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" aria-label="Previous page">&laquo; Prev</a>
                <?php else: ?>
                    <span class="disabled" aria-disabled="true">&laquo; Prev</span>
                <?php endif; ?>

                <?php
                // Show pagination range (e.g., 1 ... 4 5 6 ... 10)
                $range = 2; // pages before and after current
                $start = max(1, $page - $range);
                $end = min($totalPages, $page + $range);

                if ($start > 1) {
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a>';
                    if ($start > 2) echo '<span>...</span>';
                }

                for ($i = $start; $i <= $end; $i++) {
                    if ($i == $page) {
                        echo '<span class="current-page" aria-current="page">' . $i . '</span>';
                    } else {
                        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '">' . $i . '</a>';
                    }
                }

                if ($end < $totalPages) {
                    if ($end < $totalPages - 1) echo '<span>...</span>';
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '">' . $totalPages . '</a>';
                }
                ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" aria-label="Next page">Next &raquo;</a>
                <?php else: ?>
                    <span class="disabled" aria-disabled="true">Next &raquo;</span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p style="text-align:center; color:#b31c6d;">No matches found based on your search criteria 💔</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
