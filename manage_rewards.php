<?php
require_once "config.php";

if (!is_logged_in() || $_SESSION['user']['role'] !== 'Admin') {
    die("Akses ditolak");
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM rewards WHERE id=?")->execute([$id]);
    header("Location: manage_rewards.php");
    exit;
}

/* ADD */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = $_POST['name'];
    $desc   = $_POST['description'];
    $points = $_POST['points_required'];
    $stock  = $_POST['stock'];

    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/rewards/" . $image);
    }

    $pdo->prepare("
        INSERT INTO rewards (name, description, points_required, stock, image)
        VALUES (?,?,?,?,?)
    ")->execute([$name, $desc, $points, $stock, $image]);

    header("Location: manage_rewards.php");
    exit;
}

$rewards = $pdo->query("SELECT * FROM rewards ORDER BY points_required ASC")->fetchAll();
?>

<!doctype html>
<html>
<head>
<title>Manage Rewards</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/manage_rewards.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="manage-rewards-page">

    <h2 class="page-title">Manage Rewards</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="msg-success">
            <?= $_GET['msg'] === 'added' ? 'Hadiah berhasil ditambahkan!' : 'Hadiah berhasil dihapus!' ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <div class="reward-form-card">
        <h3>Tambah Hadiah Baru</h3>

        <form method="post" enctype="multipart/form-data" class="reward-form">
            <input type="text" name="name" placeholder="Nama Hadiah" required>
            <input type="number" name="points_required" placeholder="Poin Dibutuhkan" required>

            <textarea name="description" placeholder="Deskripsi Hadiah"></textarea>

            <input type="number" name="stock" placeholder="Stok" required>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Tambah Hadiah</button>
        </form>
    </div>

    <!-- TABLE -->
    <table class="reward-table">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Poin</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rewards as $r): ?>
            <tr>
                <td>
                    <?php if ($r['image']): ?>
                        <img src="uploads/rewards/<?= $r['image'] ?>" class="reward-thumb">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= $r['points_required'] ?></td>
                <td><?= $r['stock'] ?></td>
                <td>
                    <a href="?delete=<?= $r['id'] ?>" 
                       class="btn-delete"
                       onclick="return confirm('Hapus hadiah ini?')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
<footer>
<p>© 2025 EcoHub</p>
</footer>
</body>
</html>
