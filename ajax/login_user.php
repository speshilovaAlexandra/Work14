<?php
session_start();
require_once("../settings/connect_datebase.php");

$NowDate = date("Y-m-d H:i:s");
$sql = "SELECT * FROM `acces_ip` WHERE `Ip` = '$user_ip';";
$QueryAccess = $mysqli->query($sql);
if($QueryAccess->num_rows > 0) {
    $ReadAccess = $QueryAccess->fetch_assoc();
    $EndDate = $ReadAccess["endDate"];
    $StartDate = $ReadAccess["startDate"];
    
    if($StartDate == $EndDate) {
        echo "Пользователь заблокирован";
        exit();
    } else {
        $sql = "UPDATE `acces_ip` SET `startDate` = '$EndDate', `endDate` = '$NowDate' WHERE `Ip` = '$user_ip'";
        $mysqli->query($sql);
    }
} else {
    $sql = "INSERT INTO `acces_ip` (`Ip`, `startDate`, `endDate`) VALUES ('$user_ip', NULL, '$NowDate')";
    $mysqli->query($sql);
}

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