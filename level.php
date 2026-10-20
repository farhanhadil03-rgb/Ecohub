<?php
require_once "config.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = current_user_id();

/*AMBIL POINT USER (FIX) */
$stmt = $pdo->prepare("
    SELECT points 
    FROM users 
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$userRow = $stmt->fetch(PDO::FETCH_ASSOC);

$user_points = (int)($userRow['points'] ?? 0);

/* AMBIL LEVEL BERDASARKAN POINT */
$levels = $pdo->query("
    SELECT * FROM levels ORDER BY min_points ASC
")->fetchAll(PDO::FETCH_ASSOC);

$currentLevel = $levels[0];
$nextLevel = null;

foreach ($levels as $i => $lvl) {
    if ($user_points >= $lvl['min_points']) {
        $currentLevel = $lvl;
        $nextLevel = $levels[$i + 1] ?? null;
    }
}

/* HITUNG PROGRESS */
if ($nextLevel) {
    $progress = ($user_points - $currentLevel['min_points']) /
                ($nextLevel['min_points'] - $currentLevel['min_points']) * 100;
    $progress = max(0, min(100, round($progress)));
} else {
    $progress = 100;
}
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Level Saya - EcoHub</title>

<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/level.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="level-page">

    <div class="level-card">
        <h2>🌱 Level EcoHub</h2>

        <div class="current-level">
            <span class="level-name">
                <?= htmlspecialchars($currentLevel['name']) ?>
            </span>
            <span class="level-points">
                <?= $user_points ?> poin
            </span>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $progress ?>%"></div>
        </div>

        <?php if ($nextLevel): ?>
            <p class="next-level">
                <?= $nextLevel['min_points'] - $user_points ?>
                poin lagi ke <b><?= htmlspecialchars($nextLevel['name']) ?></b>
            </p>
        <?php else: ?>
            <p class="max-level">🏆 Level tertinggi tercapai!</p>
        <?php endif; ?>
    </div>

    <div class="level-list">
        <h3>Daftar Level</h3>

        <?php foreach ($levels as $lvl): ?>
            <div class="level-item <?= $user_points >= $lvl['min_points'] ? 'active' : '' ?>">
                <span><?= htmlspecialchars($lvl['name']) ?></span>
                <span><?= $lvl['min_points'] ?> poin</span>
            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
