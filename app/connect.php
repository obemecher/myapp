<?php
function connectToDatabase() {
    $servername = '172.18.0.1'; // IP твоего Mint-хоста
    $username = 'root';           // логин
    $password = 'secret';          // пароль
    $dbname = 'MainData';             // база
   // $port = 3306;

    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    return $conn;
}

$dbConnection = connectToDatabase();
?>
