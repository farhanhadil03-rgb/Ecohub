<?php
require_once 'config.php';
$tutorials = $pdo->query('SELECT * FROM tutorials ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoHub - Tutorials</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/tutorial.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="tutorial-page">

    <h2 class="tutorial-title">Tutorial</h2>

    <div class="tutorial-grid">

        <?php if (empty($tutorials)): ?>
            <p>Tidak ada tutorial tersedia.</p>
        <?php else: ?>

            <?php foreach ($tutorials as $t): ?>
                <div class="tutorial-card">

                    <?php if ($t['image']): ?>
                        <img src="uploads/tutorials/<?= $t['image'] ?>" class="tutorial-img">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($t['title']) ?></h3>

                    <p><?= htmlspecialchars(substr($t['description'], 0, 120)) ?>...</p>

                    <a href="tutorial_view.php?id=<?= $t['id'] ?>" class="btn-view">
                        Lihat Tutorial
                    </a>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>
    </div>

</div>

<footer>
<p>© 2025 EcoHub</p>
</footer>

</body>
</html>
