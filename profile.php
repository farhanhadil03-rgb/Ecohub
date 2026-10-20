<?php
session_start();

//  Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>My Profile - EcoHub</title>
<link rel="stylesheet" href="assets/css/styling.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="profile-card">
    <h2>My Profile</h2>

    <table class="profile-table">
        <tr>
            <td><strong>User ID</strong></td>
            <td><?= htmlspecialchars($user['user_id']) ?></td>
        </tr>
        <tr>
            <td><strong>Full Name</strong></td>
            <td><?= htmlspecialchars($user['full_name']) ?></td>
        </tr>
        <tr>
            <td><strong>Username</strong></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
        </tr>
        <tr>
            <td><strong>Role</strong></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
        </tr>
    </table>

    <a href="profile_edit.php" class="btn-profile">Edit Profile</a>
    <a href="level.php" class="btn-profile">Level</a>


    <a href="index.php" class="back-link">← Back</a>
</div>

<footer>© 2025 EcoHub</footer>

</body>
</html>
