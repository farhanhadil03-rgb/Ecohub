<?php
require_once "config.php";

if (!is_logged_in() || $_SESSION['user']['role'] !== 'Admin') {
    die("Access Denied!");
}

/* DELETE TUTORIAL */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // ambil gambar dulu (biar bisa dihapus filenya)
    $img = $pdo->prepare("SELECT image FROM tutorials WHERE id=?");
    $img->execute([$id]);
    $old = $img->fetch();

    if ($old && !empty($old['image'])) {
        @unlink("uploads/tutorials/" . $old['image']);
    }

    $del = $pdo->prepare("DELETE FROM tutorials WHERE id=?");
    $del->execute([$id]);

    header("Location: manage_tutorials.php?msg=deleted");
    exit;
}

/* ADD NEW TUTORIAL */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $video       = trim($_POST['video_url']);
    $image       = null;

    // === UPLOAD GAMBAR ===
    if (!empty($_FILES['image']['name'])) {

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Format gambar tidak didukung!");
        }

        $dir = "uploads/tutorials/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $image = uniqid("tutorial_") . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $image);
    }

    $ins = $pdo->prepare("
        INSERT INTO tutorials (title, description, video_url, image)
        VALUES (?,?,?,?)
    ");
    $ins->execute([$title, $description, $video, $image]);

    header("Location: manage_tutorials.php?msg=added");
    exit;
}

/* GET ALL TUTORIALS */
$tuts = $pdo->query("SELECT * FROM tutorials ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manage Tutorials</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/manage_users.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="manage-users-container">

<h2 class="page-title">Manage Tutorials</h2>

<?php if (@$_GET['msg'] === 'added'): ?>
    <div class="msg-success">Tutorial berhasil ditambahkan!</div>
<?php elseif (@$_GET['msg'] === 'deleted'): ?>
    <div class="msg-success">Tutorial berhasil dihapus!</div>
<?php endif; ?>

<!-- FORM ADD -->
<form class="add-form" method="POST" enctype="multipart/form-data">
    <h3>Tambah Tutorial Baru</h3>

    <input type="text" name="title" placeholder="Judul Tutorial" required>

    <input type="file" name="image" accept="image/*">

    <input type="text" name="video_url" placeholder="YouTube Embed URL">

    <textarea name="description" placeholder="Isi Tutorial..." required></textarea>

    <button type="submit">Tambah Tutorial</button>
</form>

<table class="manage-table">
<thead>
<tr>
    <th>ID</th>
    <th>Thumbnail</th>
    <th>Judul</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>

<?php foreach ($tuts as $t): ?>
<tr>
    <td><?= $t['id'] ?></td>
    <td>
        <?php if ($t['image']): ?>
            <img src="uploads/tutorials/<?= $t['image'] ?>" width="80">
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($t['title']) ?></td>
    <td>
        <a href="manage_tutorials.php?delete=<?= $t['id'] ?>"
           class="btn-delete"
           onclick="return confirm('Hapus tutorial ini?')">
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
