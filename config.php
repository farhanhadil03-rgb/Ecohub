<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOST = '127.0.0.1';
$DB_NAME = 'ecohub';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('DB connection failed: ' . $e->getMessage());
}

/* FIX: FUNGSI USER LOGIN */
function is_logged_in() {
    return isset($_SESSION['user']);
}

function current_user_id() {
    return $_SESSION['user']['user_id'] ?? null;
}


function updateUserLevel($user_id) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT points FROM users WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);
    $points = $stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT id FROM levels 
        WHERE min_points <= ?
        ORDER BY min_points DESC
        LIMIT 1
    ");
    $stmt->execute([$points]);
    $level_id = $stmt->fetchColumn();

    $pdo->prepare("
        UPDATE users SET level_id = ? WHERE user_id = ?
    ")->execute([$level_id, $user_id]);
}
