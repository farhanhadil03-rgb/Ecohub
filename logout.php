<?php
session_start();

// Hapus session array
$_SESSION = [];

// Hapus cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 🔥 HAPUS Remember Me cookie
setcookie("remember_user", "", time() - 3600, "/");

// Destroy session
session_destroy();

// Redirect ke login
header("Location: login.php");
exit;
