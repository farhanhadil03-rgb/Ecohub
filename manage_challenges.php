<?php
require_once "config.php";

if (!is_logged_in() || $_SESSION['user']['role'] !== 'Admin') {
    die("Access Denied!");
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM eco_challenges WHERE id=?")->execute([$id]);
    header("Location: manage_challenges.php?msg=deleted");
    exit;
}

if ($_SERVER['REQUEST_METHOD']==='POST'){
    $title = $_POST['title'];
    $points = intval($_POST['points']);

    $pdo->prepare("INSERT INTO eco_challenges (title, points) VALUES (?,?)")
        ->execute([$title, $points]);

    header("Location: manage_challenges.php?msg=added");
    exit;
}

$chs = $pdo->query("SELECT * FROM eco_challenges ORDER BY created_at DESC")->fetchAll();
?>

<html>
<head>
<title>Manage Challenges</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/manage_users.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="manage-users-container">

<h2 class="page-title">Manage Challenges</h2>

<?php if (@$_GET['msg']=='added'): ?>
    <div class="msg-success">Challenge ditambahkan!</div>
<?php elseif (@$_GET['msg']=='deleted'): ?>
    <div class="msg-success">Challenge dihapus!</div>
<?php endif; ?>

<form method="POST" class="add-form">
    <h3>Tambah Challenge</h3>
    <input type="text" name="title" placeholder="Judul Challenge" required>
    <input type="number" name="points" placeholder="Poin" required>
    <button type="submit">Tambah</button>
</form>

<table class="manage-table">
<thead>
<tr>
    <th>ID</th>
    <th>Judul</th>
    <th>Poin</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php foreach($chs as $c): ?>
<tr>
    <td><?= $c['id'] ?></td>
    <td><?= htmlspecialchars($c['title']) ?></td>
    <td><?= $c['points'] ?></td>
    <td>
        <a href="manage_challenges.php?delete=<?= $c['id'] ?>"
           class="btn-delete"
           onclick="return confirm('Hapus challenge?')">
           Delete
        </a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>

</body>
</html>
