<?php
require_once "config.php";

$errors = [];

/*  AUTO LOGIN DARI COOKIE */
if (isset($_COOKIE['remember_user'])) {
    $user_id = $_COOKIE['remember_user'];

    $stmt = $pdo->prepare("SELECT user_id, full_name, username, email, role FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    }
}

/* PROSES LOGIN */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($username === '') $errors['username'] = "Username harus diisi";
    if ($password === '') $errors['password'] = "Password harus diisi";

    if (empty($errors)) {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors['general'] = "Username atau password salah.";
        } else {

            $_SESSION['user'] = [
                'user_id'   => $user['user_id'],
                'full_name' => $user['full_name'],
                'username'  => $user['username'],
                'email'     => $user['email'],
                'role'      => $user['role']
            ];

            //  REMEMBER ME (7 hari)
            if ($remember) {
                setcookie("remember_user", $user['user_id'], time() + (7 * 24 * 60 * 60), "/");
            }

            $_SESSION['login_success'] = true;
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - EcoHub</title>
<link rel="stylesheet" href="assets/css/styling.css">
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="page">
    <section class="card">
        <h1 class="card-title">Login EcoHub</h1>

        <?php if (!empty($errors['general'])): ?>
            <div class="error"><?= $errors['general']; ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <div class="error"><?= $errors['username'] ?? '' ?></div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
                <div class="error"><?= $errors['password'] ?? '' ?></div>
            </div>

            <div class="form-group remember-me">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </div>

            <button type="submit" class="login-btn">Login</button>

            <div class="register-text">
                Belum punya akun?
                <a href="register.php">Register</a>
            </div>

        </form>
    </section>
</main>

<footer>© 2025 EcoHub</footer>

</body>
</html>
