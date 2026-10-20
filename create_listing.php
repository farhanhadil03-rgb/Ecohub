<?php
require_once 'config.php';
if (!is_logged_in()) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $item_name   = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $image = null;

    //  Upload Image 
    if (!empty($_FILES['image']['name'])) {

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Format gambar tidak didukung!");
        }

        $dir = "uploads/barter/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $image = uniqid("barter_") . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $image);
    }

    //  Insert ke DB 
    $stmt = $pdo->prepare("
        INSERT INTO barter_items (user_id, item_name, description, image)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $_SESSION['user']['user_id'],
        $item_name,
        $description,
        $image
    ]);

    header("Location: barter.php");
    exit;
}
?>


<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Listing - EcoHub</title>

<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/barter.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="listing-create-page">

    <h2 class="listing-create-title">Tambah Listing Baru</h2>

    <form method="post" enctype="multipart/form-data" class="listing-form">

        <input type="text" name="item_name" placeholder="Nama Barang" required>

        <textarea name="description" placeholder="Deskripsi Barang" required></textarea>

        <label>Upload Foto (optional):</label>
        <input type="file" name="image">

        <button type="submit" class="btn-submit-listing">Tambah Listing</button>
    </form>

    <a href="barter.php" class="btn-back-listing">← Kembali ke daftar barter</a>

</div>

<footer><p>© 2025 EcoHub</p></footer>

</body>
</html>
