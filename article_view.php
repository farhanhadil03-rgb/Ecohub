<?php
require_once 'config.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('SELECT * FROM articles WHERE id=?');
$stmt->execute([$id]);
$art = $stmt->fetch();
if(!$art) die('Artikel tidak ditemukan.');
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($art['title']) ?> - EcoHub</title>
<link rel="stylesheet" href="assets/css/styling.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<h2><?= htmlspecialchars($art['title']) ?></h2>
<p><?= nl2br(htmlspecialchars($art['content'])) ?></p>
<p><a href="articles.php">Kembali ke daftar artikel</a></p>


<footer>
<p>© 2025 EcoHub</p>
</footer>
</body>
</html>
