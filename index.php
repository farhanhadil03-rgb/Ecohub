<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$latest_articles = $pdo->query(
    "SELECT id, title, content, created_at 
     FROM articles 
     ORDER BY created_at DESC 
     LIMIT 5"
)->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoHub - Home</title>
<link rel="stylesheet" href="assets/css/styling.css">
<script src="assets/js/app.js" defer></script>
</head>
<body>


<?php include 'navbar.php'; ?>

<h2 class="page-title">Home</h2>

<main class="articles-container">

    <h2 class="section-title">Artikel Terbaru</h2>

    <div class="articles-grid">
        <?php foreach($latest_articles as $a): ?>
            <article class="article-card">
                <h3><?= htmlspecialchars($a['title']) ?></h3>

                <p><?= nl2br(htmlspecialchars(substr($a['content'], 0, 150))) ?>...</p>

                <a class="btn-read" href="articles.php?id=<?= $a['id'] ?>">
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
