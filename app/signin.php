<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница входа</title>
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
            padding: 20px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            width: 200; /* Увеличена ширина контейнера */
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 24px;
        }
        label {
            display: block;
            text-align: left;
            margin-top: 10px;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 16px); /* Уменьшение ширины полей ввода */
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            color: white;
            background-color: #4CAF50; /* Green for success */
        }
        .message.error {
            background-color: #f44336; /* Red for error */
        }
        a {
            color: #2196F3;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>
    <div class="container">
        <h1>Вход</h1>
        <form action="login.php" method="post">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" required>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Войти</button>
        </form>
        <a href="signup.php">Зарегистрироваться</a>
    </div>
</body>
</html>
