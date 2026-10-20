<?php
require_once 'config.php';

$id = $_GET['id'] ?? 0;

// Ambil data tutorial berdasarkan ID
$stmt = $pdo->prepare('SELECT * FROM tutorials WHERE id=? LIMIT 1');
$stmt->execute([$id]);
$tut = $stmt->fetch();

if(!$tut) {
    die('Tutorial tidak ditemukan.');
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" description="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($tut['title']) ?> - EcoHub</title>

<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/tutorial.css">

</head>
<body>

<?php include 'navbar.php'; ?>

<div class="tutorial-view-container">

    <div class="tutorial-view-card">

        <!-- Judul -->
        <h1 class="tutorial-view-title"><?= htmlspecialchars($tut['title']) ?></h1>

        <!-- Gambar jika ada -->
        <?php if (!empty($tut['image_url'])): ?>
            <img src="uploads/<?= htmlspecialchars($tut['image_url']) ?>" class="tutorial-view-img">
        <?php endif; ?>

        <!-- Deskripsi / isi -->
        <div class="tutorial-view-content">
            <?= nl2br(htmlspecialchars($tut['description'])) ?>
        </div>

        <!-- Video jika ada -->
        <?php if (!empty($tut['video_url'])): ?>
            <div class="tutorial-view-video">
                <iframe 
                    src="<?= htmlspecialchars($tut['video_url']) ?>" 
                    frameborder="0" 
                    allowfullscreen>
                </iframe>
            </div>
        <?php endif; ?>

        <!-- Tombol kembali -->
        <a href="tutorials.php" class="btn-back">← Kembali</a>

    </div>

</div>

<footer>
<p>© 2025 EcoHub</p>
</footer>

</body>
</html>
