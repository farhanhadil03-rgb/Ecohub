<?php
require_once "config.php";
if (!is_logged_in()) exit;

$user_id = current_user_id();
$reward_id = (int)($_GET['id'] ?? 0);

$r = $pdo->prepare("SELECT * FROM rewards WHERE id=?");
$r->execute([$reward_id]);
$reward = $r->fetch();

if (!$reward || $reward['stock'] <= 0) die("Hadiah habis");

$points = $pdo->prepare("SELECT points FROM users WHERE user_id=?");
$points->execute([$user_id]);
$user_points = $points->fetchColumn();

if ($user_points < $reward['points_required']) die("Poin tidak cukup");

$code = strtoupper(substr(md5(uniqid()), 0, 8));

$pdo->beginTransaction();

$pdo->prepare("UPDATE users SET points = points - ? WHERE user_id=?")
    ->execute([$reward['points_required'], $user_id]);

$pdo->prepare("UPDATE rewards SET stock = stock - 1 WHERE id=?")
    ->execute([$reward_id]);

$pdo->prepare("
    INSERT INTO reward_claims (user_id, reward_id, claim_code)
    VALUES (?,?,?)
")->execute([$user_id, $reward_id, $code]);

$pdo->commit();

header("Location: my_rewards.php");
exit;
