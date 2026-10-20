<?php
require_once 'config.php';
$articles = $pdo->query('SELECT id, title, content, created_at FROM articles ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoHub - Articles</title>

<link rel="stylesheet" href="assets/css/styling.css">

</head>
<body>

<?php include 'navbar.php'; ?>

<h2 class="page-title">Artikel Edukasi Lingkungan</h2>

<main class="articles-container">

    <div class="articles-grid">
        <?php foreach ($articles as $a): ?>
            <article class="article-card">
                <h3><?= htmlspecialchars($a['title']) ?></h3>

                <p>
                    <?= nl2br(htmlspecialchars(substr($a['content'], 0, 150))) ?>...
                </p>

                <a class="btn-read" href="article_view.php?id=<?= $a['id'] ?>">
                    Baca Selengkapnya
                </a>

                <span class="article-date">
                    <?= date("d M Y", strtotime($a['created_at'])) ?>
                </span>
            </article>
        <?php endforeach; ?>
    </div>

</main>

<footer class="footer">
    <p>© 2025 EcoHub</p>
</footer>

</body>
</html>
