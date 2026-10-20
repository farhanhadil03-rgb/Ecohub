<?php
require_once "config.php";

/* FUNCTION VALIDASI */
function is_alpha_space($s){
    return preg_match("/^[A-Za-z ]+$/", $s);
}

function is_alpha($s){
    return preg_match("/^[A-Za-z]+$/", $s);
}

function strong_password($s){
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $s);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    //  Validasi Fullname
    if ($fullname === '' || !is_alpha_space($fullname))
        $errors['fullname'] = "Full name hanya boleh huruf dan spasi.";

    //  Validasi Username
    if ($username === '' || !is_alpha($username))
        $errors['username'] = "Username hanya boleh huruf tanpa spasi.";

    //  Validasi Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = "Format email tidak valid.";

    //  Validasi Password
    if (!strong_password($password))
        $errors['password'] = "Password minimal 8 karakter, huruf besar, kecil & angka.";

    //  Cek Username atau Email Sudah Ada
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $exists = $stmt->fetch();

    if ($exists) {
        if ($exists['username'] === $username)
            $errors['username'] = "Username sudah digunakan.";
        if ($exists['email'] === $email)
            $errors['email'] = "Email sudah terdaftar.";
    }

    //  SIMPAN KE DATABASE (PAKAI password_hash)
    if (empty($errors)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $insert = $pdo->prepare("
            INSERT INTO users (full_name, username, email, password_hash, role)
            VALUES (?, ?, ?, ?, 'User')
        ");

        if ($insert->execute([$fullname, $username, $email, $hash])) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            $errors['general'] = "Gagal mendaftar akun.";
        }


    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Register - EcoHub</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/login.css">
<link rel="stylesheet" href="assets/css/register.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="page">
    <section class="card register-card">
        <h2 class="card-title register-title">Register EcoHub</h2>
        <?php if (!empty($errors['general'])): ?>
            <div class="error"><?= $errors['general'] ?></div>
        <?php endif; ?>

        <form method="POST" class="register-form">


            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>">
                <div class="error"><?= $errors['fullname'] ?? '' ?></div>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>">
                <div class="error"><?= $errors['username'] ?? '' ?></div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
                <div class="error"><?= $errors['email'] ?? '' ?></div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
                <div class="error"><?= $errors['password'] ?? '' ?></div>
            </div>

            <button type="submit" class="submit">Register</button>

        </form>
    </section>
</main>

<footer>© 2025 EcoHub</footer>

</body>
</html>
