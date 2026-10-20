<?php
require_once "config.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['user_id'];

$errors = [];

// Ambil data user dari database
$stmt = $pdo->prepare("SELECT full_name, username, email FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$data = $stmt->fetch();

if (!$data) {
    die("User tidak ditemukan.");
}

$currentName = $data['full_name'];
$currentUsername = $data['username'];
$currentEmail = $data['email'];

/*  VALIDASI  */
function is_alpha($s) {
    return preg_match("/^[A-Za-z]+$/", $s);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newName = trim($_POST['fullname'] ?? '');
    $newUsername = trim($_POST['username'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');

    // Validasi full name
    if ($newName === "") {
        $errors['fullname'] = "Nama tidak boleh kosong.";
    }

    // Validasi username
    if ($newUsername === "" || !is_alpha($newUsername)) {
        $errors['username'] = "Username hanya boleh huruf tanpa spasi.";
    }

    // Validasi email
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format email tidak valid.";
    }

    // Cek email sudah ada
    if ($newEmail !== $currentEmail) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$newEmail, $user_id]);
        if ($stmt->fetch()) {
            $errors['email'] = "Email sudah digunakan.";
        }
    }

    // Jika tidak ada error → Update
    if (empty($errors)) {

        $update = $pdo->prepare("
            UPDATE users 
            SET full_name = ?, username = ?, email = ?
            WHERE user_id = ?
        ");

        $update->execute([$newName, $newUsername, $newEmail, $user_id]);

        // Update SESSION
        $_SESSION['user']['full_name'] = $newName;
        $_SESSION['user']['username'] = $newUsername;
        $_SESSION['user']['email'] = $newEmail;

        header("Location: profile.php?updated=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Profile - EcoHub</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/profile_edit.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="edit-card">

    <h2>Edit Profile</h2>

    <form method="POST" class="edit-form">

        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($currentName) ?>">
        <div class="err"><?= $errors['fullname'] ?? '' ?></div>

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($currentUsername) ?>">
        <div class="err"><?= $errors['username'] ?? '' ?></div>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($currentEmail) ?>">
        <div class="err"><?= $errors['email'] ?? '' ?></div>

        <button type="submit" class="btn-save">Simpan Perubahan</button>
    </form>

    <a href="profile.php" class="back-link">← Kembali</a>
</div>


<footer>
<p>© 2025 EcoHub</p>
</footer>

</body>
</html>
