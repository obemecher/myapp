<?php
session_start();

require 'connect.php'; // подключение к БД через connect.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $userPassword = $_POST['password'];

    try {
        // Получаем подключение из connect.php
        global $dbConnection;
        $conn = $dbConnection;

        $stmt = $conn->prepare("SELECT * FROM users WHERE login = ? AND password = ?");
        $stmt->bind_param("ss", $login, $userPassword);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: lk.php");
            exit();
        } else {
            echo "Неверный логин или пароль.";
        }
    } catch (Exception $e) {
        echo "Ошибка подключения к базе данных: " . $e->getMessage();
    }
} else {
    echo "Ошибка при отправке формы.";
}
?>