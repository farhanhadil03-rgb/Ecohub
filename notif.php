<?php
require_once "config.php";

if (!is_logged_in()) {
    die("Harus login.");
}

$user_id = $_SESSION['user']['user_id'];

// Ambil semua notifikasi barter untuk user
$stmt = $pdo->prepare("
    SELECT n.*, 
           u.username AS from_user_name,
           b.item_name
    FROM notifications n
    LEFT JOIN users u ON n.from_user_id = u.user_id
    LEFT JOIN barter_items b ON n.item_id = b.id
    WHERE n.user_id = ?
    ORDER BY n.created_at DESC
");
$stmt->execute([$user_id]);
$notifs = $stmt->fetchAll();

?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Notifikasi Barter</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/notif.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="notif-page">

<h2 class="notif-title">Notifikasi Barter</h2>

<?php if(isset($_GET['accept'])): ?>
    <div class="notif-success">Barter berhasil disetujui!</div>
<?php endif; ?>

<?php if(isset($_GET['reject'])): ?>
    <div class="notif-error">Kamu menolak barter.</div>
<?php endif; ?>

<?php foreach($notifs as $n): ?>

    <div class="notif-card">

        <p><b><?= htmlspecialchars($n['from_user_name']) ?></b></p>

        <h3>
            <?= htmlspecialchars($n['item_name'] ?? "[Barang sudah dihapus]") ?>
        </h3>

        <p><?= nl2br(htmlspecialchars($n['message'])) ?></p>


        <!-- HANYA REQUEST + PENDING yang boleh ada tombol -->
        <?php if ($n['type'] === 'request' && $n['status'] === 'pending'): ?>

            <div class="notif-actions">
                <a href="notif_action.php?id=<?= $n['id'] ?>&action=accept" class="btn-accept">Setuju</a>
                <a href="notif_action.php?id=<?= $n['id'] ?>&action=reject" class="btn-reject">Tolak</a>
            </div>

        <?php else: ?>
            <!-- Status setelah keputusan -->
            <?php if ($n['status'] === 'accepted'): ?>
                <div class="notif-badge accepted">✔ Barter diterima</div>
            <?php elseif ($n['status'] === 'rejected'): ?>
                <div class="notif-badge rejected">✖ Barter ditolak</div>
            <?php endif; ?>
        <?php endif; ?>

    </div>

<?php endforeach; ?>

</div>

</body>
</html>
