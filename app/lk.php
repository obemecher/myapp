<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

require 'connect.php'; // подключение к БД через connect.php

try {
    // Получаем подключение из connect.php
    global $dbConnection;
    $conn = $dbConnection;

    $stmt = $conn->prepare("SELECT imya, familia, otch, contacts, login, status FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("Пользователь не найден.");
    }
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            width: 400px;
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 24px;
        }
        p {
            color: #666;
            font-size: 16px;
        }
        .status {
            color: #4CAF50; /* Зеленый цвет для верифицированного статуса */
            font-weight: bold;
        }
        
        .status.not-verified {
            color: #f44336; /* Красный цвет для неподтвержденного статуса */
        }
        button {
            background-color: grey;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #9c0909;
        }
        .delete-button {
            background-color: #f44336;
            margin-top: 5px;
        }
        .delete-button:hover {
            background-color: #d32f2f;
        }
        .small-text {
            color: #999;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Добро пожаловать, <?php echo htmlspecialchars($user['imya']); ?>!</h1>
        <p>Контакты: <?php echo htmlspecialchars($user['contacts']); ?></p>
        <p>Логин: <?php echo htmlspecialchars($user['login']); ?></p>
        
        </span></p>
        <form action="logout.php" method="post">
            <button type="submit" name="logout">Выйти</button>
        </form>
        
    </div>
</body>
</html>