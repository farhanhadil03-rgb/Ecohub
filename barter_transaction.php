<?php
require_once "config.php";

if (!is_logged_in()) {
    die("Harus login untuk barter.");
}

$item_id = $_GET['item_id'] ?? $_GET['id'] ?? 0;
$item_id = (int)$item_id;

if ($item_id <= 0) {
    die("ID barang tidak valid.");
}

// Ambil data listing
$stmt = $pdo->prepare("
    SELECT b.*, u.username 
    FROM barter_items b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.id = ?
");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item) {
    die("Barang tidak ditemukan");
}

// Jika submit barter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $offer = trim($_POST['offer']);
    $from_user = $_SESSION['user']['user_id'];
    $owner = $item['user_id'];

    $msg = "Ingin barter barang <b>{$item['item_name']}</b> dengan menawarkan: $offer";

    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, from_user_id, item_id, message, type)
        VALUES (?, ?, ?, ?, 'request')
    ");
    $stmt->execute([$owner, $from_user, $item_id, $msg]);

    header("Location: barter.php?success=1");
    exit;
}
?>


<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Ajukan Barter</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/notif.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="transaction-page">

    <div class="transaction-card">

        <h2>Ajukan Barter Untuk</h2>

        <h3 class="transaction-item"><?= htmlspecialchars($item['item_name']) ?></h3>

        <form method="POST">
            <textarea name="offer" placeholder="Barang apa yang kamu tawarkan?" required></textarea>
            <button type="submit" class="btn-submit-offer">Kirim Permintaan</button>
        </form>

        <a href="barter.php" class="btn-back-trans">← Kembali</a>

    </div>

</div>

<footer><p>© 2025 EcoHub</p></footer>

</body>
</html>
