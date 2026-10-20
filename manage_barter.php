<?php
require_once "config.php";

if (!is_logged_in() || $_SESSION['user']['role'] !== 'Admin') {
    die("Access Denied!");
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM barter_items WHERE id=?");
    $stmt->execute([$id]);
    header("Location: manage_barter.php?msg=deleted");
    exit;
}

$list = $pdo->query("SELECT b.*, u.username FROM barter_items b
    JOIN users u ON u.user_id = b.user_id
    ORDER BY id DESC")->fetchAll();
?>

<html>
<head>
<title>Manage Barter</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/manage_users.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="manage-users-container">
<h2 class="page-title">Manage Barter</h2>

<?php if (@$_GET['msg']=='deleted'): ?>
    <div class="msg-success">Listing berhasil dihapus.</div>
<?php endif; ?>

<table class="manage-table">
<thead>
<tr>
    <th>ID</th>
    <th>Nama Barang</th>
    <th>User</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php foreach($list as $b): ?>
<tr>
    <td><?= $b['id'] ?></td>
    <td><?= htmlspecialchars($b['item_name']) ?></td>
    <td><?= htmlspecialchars($b['username']) ?></td>
    <td><a class="btn-delete"
           href="manage_barter.php?delete=<?= $b['id'] ?>"
           onclick="return confirm('Hapus listing ini?')">Delete</a></td>
</tr>
<?php endforeach; ?>
</tbody>

</table>
</div>

</body>
</html>
