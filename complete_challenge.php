<?php
require_once 'config.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$challenge_id = $_GET['id'] ?? $_POST['challenge_id'] ?? null;

$status = null;


// AMBIL DATA CHALLENGE

$stmt = $pdo->prepare("SELECT * FROM eco_challenges WHERE id = ?");
$stmt->execute([$challenge_id]);
$challenge = $stmt->fetch();

if (!$challenge) {
    die("Challenge tidak ditemukan.");
}


// SUBMIT FORM

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $message = trim($_POST['message'] ?? '');
    $photo = null;

    
    // VALIDASI UPLOAD FOTO
    
    if (!empty($_FILES['photo']['name'])) {

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Format foto tidak didukung.");
        }

        // pastikan folder ada
        $uploadDir = "uploads/challenges/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $photo = uniqid("challenge_") . "." . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photo);
    }

    
    // CEK SUDAH PERNAH?
    
    $check = $pdo->prepare("
        SELECT id FROM user_challenges 
        WHERE user_id = ? AND challenge_id = ?
    ");
    $check->execute([$user_id, $challenge_id]);

    if ($check->fetch()) {
        $status = "done";
    } else {

        // Tambah poin
        $pdo->prepare("
            UPDATE users 
            SET points = points + ? 
            WHERE user_id = ?
        ")->execute([$challenge['points'], $user_id]);

        // Simpan challenge
        $pdo->prepare("
            INSERT INTO user_challenges 
            (user_id, challenge_id, message, photo)
            VALUES (?, ?, ?, ?)
        ")->execute([$user_id, $challenge_id, $message, $photo]);

        $status = "success";
    }
}
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Selesaikan Challenge</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/challenges.css">
<link rel="stylesheet" href="assets/css/complete_challenges.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="completion-page">
    <div class="challenge-card">
        <div class="completion-card">

        <?php if ($status === null): ?>

            <!-- FORM -->
            <h2 class="completion-title">
                <?= htmlspecialchars($challenge['title']) ?>
            </h2>

            <div class="completion-points">
                +<?= $challenge['points'] ?> poin
            </div>

            <form method="post" enctype="multipart/form-data" class="completion-form">

                <input type="hidden" name="challenge_id" value="<?= $challenge_id ?>">

                <div class="form-group">
                    <label>Kesan & Pesan</label>
                    <textarea name="message" required placeholder="Ceritakan pengalamanmu..."></textarea>
                </div>

                <div class="form-group">
                    <label>Upload Foto Bukti (opsional)</label>
                    <input type="file" name="photo" accept="image/*">
                </div>

                <button type="submit" class="btn-complete">
                    Kirim & Selesaikan
                </button>

            </form>

            <a href="challenges.php" class="back-link">← Kembali</a>

        <?php elseif ($status === "success"): ?>

            <!-- SUCCESS -->
            <div class="success-box">
                <h2>🎉 Challenge Berhasil!</h2>
                <p>
                    Kamu mendapatkan <b><?= $challenge['points'] ?> poin</b>
                </p>
                <a href="challenges.php" class="btn-complete">Kembali ke Challenges</a>
            </div>

        <?php else: ?>

            <!-- SUDAH -->
            <div class="success-box">
                <h2>✔ Sudah Diselesaikan</h2>
                <p>Kamu sudah menyelesaikan challenge ini sebelumnya.</p>
                <a href="challenges.php" class="btn-complete">Kembali</a>
            </div>

        <?php endif; ?>

        </div>
    </div>
</div>

<footer>
<p>© 2025 EcoHub</p>
</footer>

</body>
</html>
