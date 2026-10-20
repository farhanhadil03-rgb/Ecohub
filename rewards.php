<?php
require_once "config.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = current_user_id();

/* AMBIL POINT USER (AMAN) */
$stmt = $pdo->prepare("SELECT points FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_points = (int)$stmt->fetchColumn();

/* AMBIL LEVEL BERDASARKAN POINT */
$levels = $pdo->query("
    SELECT * FROM levels ORDER BY min_points ASC
")->fetchAll(PDO::FETCH_ASSOC);

$currentLevel = $levels[0] ?? ['name' => 'Eco Starter'];

foreach ($levels as $lvl) {
    if ($user_points >= $lvl['min_points']) {
        $currentLevel = $lvl;
    }
}

/* AMBIL REWARDS */
$rewards = $pdo->query("
    SELECT * FROM rewards 
    WHERE stock > 0
    ORDER BY points_required ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rewards EcoHub</title>

<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/rewards.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="rewards-page">

    <!-- USER INFO -->
    <div class="user-info">
        <h2>🎁 Tukar Hadiah EcoHub</h2>
        <p>Level: <b><?= htmlspecialchars($currentLevel['name']) ?></b></p>
        <p>Poin kamu: <b><?= $user_points ?></b></p>
    </div>

    <!-- GRID HADIAH -->
    <div class="reward-grid">

        <?php if (empty($rewards)): ?>
            <p class="empty-text">Belum ada hadiah tersedia.</p>
        <?php endif; ?>

        <?php foreach ($rewards as $r): ?>
        <div class="reward-card">

            <!-- GAMBAR HADIAH -->
            <?php if (!empty($r['image'])): ?>
                <img 
                    src="uploads/rewards/<?= htmlspecialchars($r['image']) ?>" 
                    alt="<?= htmlspecialchars($r['name']) ?>" 
                    class="reward-img"
                >
            <?php else: ?>
                <div class="reward-no-img">No Image</div>
            <?php endif; ?>

            <h3><?= htmlspecialchars($r['name']) ?></h3>

            <p class="reward-desc">
                <?= htmlspecialchars($r['description']) ?>
            </p>

            <div class="reward-points">
                🎯 <?= $r['points_required'] ?> poin
            </div>

            <?php if ($user_points >= $r['points_required']): ?>
                <a 
                    href="claim_reward.php?id=<?= $r['id'] ?>" 
                    class="btn-claim"
                    onclick="return confirm('Tukar poin dengan hadiah ini?')"
                >
                    Tukar Sekarang
                </a>
            <?php else: ?>
                <div class="not-enough">
                    Poin belum cukup
                </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>

    </div>

</div>

<footer>
<p>© 2025 EcoHub</p>
</footer>

</body>
</html>
