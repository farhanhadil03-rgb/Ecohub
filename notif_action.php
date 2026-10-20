<?php
require_once "config.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$notif_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action   = $_GET['action'] ?? '';

if ($notif_id <= 0) {
    die("Invalid notification id.");
}

$stmt = $pdo->prepare("
    SELECT n.*, u.username AS req_user, b.item_name
    FROM notifications n
    LEFT JOIN users u ON n.from_user_id = u.user_id
    LEFT JOIN barter_items b ON n.item_id = b.id
    WHERE n.id = ?
");
$stmt->execute([$notif_id]);
$notif = $stmt->fetch();

if (!$notif) {
    die("Notifikasi tidak ditemukan.");
}

$current_uid = $_SESSION['user']['user_id'];
if ((int)$notif['user_id'] !== $current_uid) {
    die("Anda tidak memiliki akses ke notifikasi ini.");
}

$item_id      = $notif['item_id'];
$owner_id     = $notif['user_id'];
$requester_id = $notif['from_user_id'];
$item_name    = $notif['item_name'] ?? "Barang";

/* ACCEPT BARter */
if ($action === "accept") {

    // 1. Update status request original
    $pdo->prepare("
        UPDATE notifications 
        SET status = 'accepted' 
        WHERE id = ?
    ")->execute([$notif_id]);

    // 2. Kirim notifikasi ke pengaju
    $msg = "Permintaan bartermu untuk '$item_name' telah DISETUJUI!";
    $pdo->prepare("
        INSERT INTO notifications (user_id, from_user_id, item_id, message, type, status)
        VALUES (?, ?, ?, ?, 'response', 'accepted')
    ")->execute([$requester_id, $owner_id, $item_id, $msg]);

    // 3. Hapus barang dari listing
    if (!empty($item_id)) {
        $pdo->prepare("DELETE FROM barter_items WHERE id = ?")->execute([$item_id]);
    }

    header("Location: notif.php?accept=1");
    exit;
}

/* REJECT BARter */
if ($action === "reject") {

    // 1. Update status request original
    $pdo->prepare("
        UPDATE notifications 
        SET status = 'rejected' 
        WHERE id = ?
    ")->execute([$notif_id]);

    // 2. Kirim notifikasi ke pengaju
    $msg = "Permintaan bartermu untuk '$item_name' telah DITOLAK.";
    $pdo->prepare("
        INSERT INTO notifications (user_id, from_user_id, item_id, message, type, status)
        VALUES (?, ?, ?, ?, 'response', 'rejected')
    ")->execute([$requester_id, $owner_id, $item_id, $msg]);

    header("Location: notif.php?reject=1");
    exit;
}

die("Aksi tidak dikenali.");
