<?php
require_once 'admin/db.php';
$password = password_hash('password', PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE ms_users SET password = ? WHERE username = 'admin'");
$stmt->execute([$password]);
echo "Password updated successfully!";
?>
