<?php
require_once "config.php";

if (!is_logged_in() || $_SESSION['user']['role'] !== 'Admin') {
    die("Access Denied!");
}

/* DELETE ARTICLE */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $del = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $del->execute([$id]);
    header("Location: manage_articles.php?msg=deleted");
    exit;
}

/* ADD ARTICLE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title !== "" && $content !== "") {

        $ins = $pdo->prepare("INSERT INTO articles (title, content) VALUES (?,?)");
        $ins->execute([$title, $content]);
        header("Location: manage_articles.php?msg=added");
        exit;
    }
}

/* GET ALL ARTICLES */
$articles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Articles - EcoHub</title>
    <link rel="stylesheet" href="assets/css/styling.css">
    <link rel="stylesheet" href="assets/css/manage_users.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="manage-users-container">
    <h2 class="page-title">Manage Articles</h2>

    <?php if (@$_GET['msg'] == 'added'): ?>
        <div class="msg-success">Artikel berhasil ditambahkan!</div>
    <?php elseif (@$_GET['msg'] == 'deleted'): ?>
        <div class="msg-success">Artikel berhasil dihapus!</div>
    <?php endif; ?>

    <!-- ADD FORM -->
    <form method="POST" class="add-form">
        <h3>Tambah Artikel Baru</h3>
        <input type="text" name="title" placeholder="Judul" required>
        <textarea name="content" placeholder="Isi artikel..." required></textarea>
        <button type="submit">Tambah Artikel</button>
    </form>

    <table class="manage-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($articles as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['title']) ?></td>
                <td><?= $a['created_at'] ?></td>
                <td>
                    <a href="manage_articles.php?delete=<?= $a['id'] ?>" class="btn-delete"
                       onclick="return confirm('Hapus artikel ini?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
