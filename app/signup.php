<?php
session_start();

require 'connect.php'; // подключение к БД через connect.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $familia = $_POST['familia'];
    $imya = $_POST['imya'];
    $otch = $_POST['otch'];
    $contacts = $_POST['contacts'];

    try {
        // Получаем подключение из connect.php
        global $dbConnection;
        $conn = $dbConnection;

        $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Пользователь с таким логином уже существует.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (login, password, familia, imya, otch, contacts) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $login, $password, $familia, $imya, $otch, $contacts);
            $stmt->execute();

            // Редирект до вывода HTML
            header("Location: signin.php");
            exit();
        }
    } catch (Exception $e) {
        $error = "Ошибка подключения к базе данных: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #2196F3;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Регистрация</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="text" name="familia" placeholder="Фамилия" required>
            <input type="text" name="imya" placeholder="Имя" required>
            <input type="text" name="otch" placeholder="Отчество" required>
            <input type="text" name="contacts" placeholder="Контакты" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <a href="signin.php">Уже есть аккаунт? Войти</a>
    </div>
</body>
</html>