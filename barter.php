<?php
require_once 'config.php';

$listings = $pdo->query("
    SELECT b.id, b.item_name, b.description, b.image, u.username
    FROM barter_items b
    JOIN users u ON b.user_id = u.user_id
    ORDER BY b.id DESC
")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoHub - Barter</title>

<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/barter.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="barter-page">

    <h2 class="barter-title">Barter Barang Bekas</h2>

    <?php if(is_logged_in()): ?>
        <a href="create_listing.php" class="btn-add-listing">+ Tambah Listing Baru</a>
    <?php endif; ?>

    <div class="barter-grid">
        <?php foreach($listings as $l): ?>
            <div class="barter-card">

                <?php if (!empty($l['image'])): ?>
                    <img src="uploads/barter/<?= htmlspecialchars($l['image']) ?>"
                        class="barter-img"
                        alt="<?= htmlspecialchars($l['item_name']) ?>">
                <?php endif; ?>

                <h3 class="barter-item-title">
                    <?= htmlspecialchars($l['item_name']) ?>
                </h3>

                <p class="barter-desc">
                    <?= htmlspecialchars($l['description']) ?>
                </p>

                <p class="barter-user">
                    Diposting oleh: <b><?= htmlspecialchars($l['username']) ?></b>
                </p>

                <a href="barter_transaction.php?item_id=<?= $l['id'] ?>" class="btn-barter">
                    Ajukan Barter
                </a>

            </div>
        <?php endforeach; ?>

    </div>

</div>

<footer><p>© 2025 EcoHub</p></footer>

</body>
</html>
