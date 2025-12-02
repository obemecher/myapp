<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Университетский сервис доставки</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }
        header {
            background-color: #002d62;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
        }
        header a:hover {
            text-decoration: underline;
        }
        iframe {
            width: 100%;
            height: calc(100vh - 50px); /* Adjust the height to account for the header */
            border: none;
        }
        .left-menu,
        .middle-menu,
        .right-menu {
            display: flex;
            /*justify-content: center;
            align-items: center;*/
            height: 100%;
        }
        .left-menu {
            width: 41%;
            
        }
        .middle-menu {
            width: 30%;
        }
        .right-menu {
            width: 7%;
        }
        .account-button {
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <div class="left-menu">
            <a href="home.php" target="content-frame">Главная</a>
            <a href="5.php" target="content-frame">Сделать заказ</a>
            <a href="getorder.php" target="content-frame">Принять заказ</a>
            <a href="myget.php" target="content-frame">Мои заказы</a>
        </div>
            
        </div>
        <div class="middle-menu">
            
        </div>
        <div class="right-menu">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="signin.php" target="content-frame" class="account-button">Аккаунт</a>
            <?php else: ?>
                <a href="lk.php" target="content-frame" class="account-button">Аккаунт</a>
            <?php endif; ?>
        </div>
    </header>
    <iframe name="content-frame" src="home.php"></iframe>
</body>
</html>