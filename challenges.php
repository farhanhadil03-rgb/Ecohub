<?php
require_once 'config.php';

$challenges = $pdo->prepare("
    SELECT c.*
    FROM eco_challenges c
    WHERE c.id NOT IN (
        SELECT challenge_id 
        FROM user_challenges 
        WHERE user_id = ?
    )
    ORDER BY c.created_at DESC
");
$challenges->execute([current_user_id()]);
$challenges = $challenges->fetchAll();
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>EcoHub - Challenges</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/challenges.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="challenge-page">
<h2 class="challenge-title">Eco-Challenges</h2>

<div class="challenge-grid">
<?php foreach($challenges as $c): ?>
    <div class="challenge-card">
        <h3><?= htmlspecialchars($c['title']) ?></h3>

        <div class="challenge-points">
            <?= $c['points'] ?> poin
        </div>

        <p class="challenge-desc">
            <?= htmlspecialchars($c['description']) ?>
        </p>

        <?php if(is_logged_in()): ?>
            <a href="complete_challenge.php?id=<?= $c['id'] ?>" class="btn-complete">
                Selesaikan Challenge
            </a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
</div>

<footer><p>© 2025 EcoHub</p></footer>
</body>
</html>
