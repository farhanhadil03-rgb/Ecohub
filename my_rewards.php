<?php
require_once "config.php";
if (!is_logged_in()) exit;

$user_id = current_user_id();

$stmt = $pdo->prepare("
    SELECT rc.claim_code, r.name, r.image
    FROM reward_claims rc
    JOIN rewards r ON rc.reward_id = r.id
    WHERE rc.user_id = ?
    ORDER BY rc.created_at DESC
");
$stmt->execute([$user_id]);
$claims = $stmt->fetchAll();
?>

<!doctype html>
<html>
<head>
<title>Hadiah Saya</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/my_rewards.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="my-rewards">

<h2>🎉 Selamat! Hadiah Kamu</h2>

<?php foreach ($claims as $c): ?>
<div class="my-reward-card">
    <?php if ($c['image']): ?>
        <img src="uploads/rewards/<?= $c['image'] ?>">
    <?php endif; ?>
    <h3><?= $c['name'] ?></h3>
</div>
<?php endforeach; ?>

</div>
</body>
</html>
