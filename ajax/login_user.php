<?php
session_start();
require_once("../settings/connect_datebase.php");



$login = $_POST['login'];
$password = $_POST['password'];

// Получаем количество входов пользователя
$countAttempt = 0;
$sql = "SELECT `attempt` FROM `users` WHERE `login` = '$login'";
$QueryAttempt = $mysqli->query($sql);
if($QueryAttempt->num_rows > 0) {
    $ReadAttempt = $QueryAttempt->fetch_assoc();
    $countAttempt = $ReadAttempt['attempt'];
}

// Проверяем заблокирован ли пользователь
if($countAttempt >= 5) {
    echo md5(md5(-1));
    exit;
}

// ищем пользователя
$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login` = '" .$login."'  AND `password` = '" .$password. "';" );
$id = -1;
while($user_read = $query_user->fetch_row()) {
    $id = $user_read[0];
}

if($id != -1) {
    $_SESSION['user'] = $id;
    // Обнуляем количество попыток
    $countAttempt = 0;
} else {
    // Записываем неудачную попытку
    $countAttempt += 1;
}

$sql = "UPDATE `users` SET `attempt` = $countAttempt WHERE `login` = '$login'";
$mysqli->query($sql);

echo md5(md5($id));
?>