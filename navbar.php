<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? 'Guest';
$username = $user['full_name'] ?? null;
?>

<!--  NAVBAR  -->
<nav class="navbar-top">

    <!-- EcoHub = Tombol Home -->
    <a href="index.php" class="navbar-title">EcoHub</a>

    <div class="navbar-navigation">

        <!-- MENU UMUM -->
        <a href="map.php">Map</a>
        <a href="tutorials.php">Tutorials</a>
        <a href="barter.php">Barter</a>
        <a href="challenges.php">Challenges</a>
        <a href="articles.php">Articles</a>
        <a href="notif.php">Notif</a>
        <a href="rewards.php">Rewards</a>

        <!--  ROLE: GUEST  -->
        <?php if ($role === 'Guest'): ?>
            <a href="login.php" class="btn-login">Login</a>
            <a href="register.php" class="btn-login">Register</a>

        <!--  ROLE: ADMIN  -->
        <?php elseif ($role === 'Admin'): ?>
            <div class="dropdown">
                <button class="dropbtn">Manage ▼</button>
                <div class="dropdown-content">
                    <a href="manage_articles.php">Manage Articles</a><br>
                    <a href="manage_Tutorials.php">Manage Tutorials</a><br>
                    <a href="manage_Barter.php">Manage Barter</a><br>
                    <a href="manage_Challenges.php">Manage Challenge</a>
                    <a href="manage_rewards.php">Manage rewards</a>
                </div>
            </div>

            <a href="profile.php" class="username">
                <?= htmlspecialchars($username) ?>
            </a>
            <a href="logout.php" class="btn-logout">Logout</a>
        <!--  ROLE: USER  -->
        <?php elseif ($role === 'User'): ?>
            <a href="profile.php" class="username">
                <?= htmlspecialchars($username) ?>
            </a>
            <a href="logout.php" class="btn-logout">Logout</a>

        <?php endif; ?>

    </div>
</nav>

