<?php
require 'connect.php'; // подключение к БД через connect.php

// Получаем подключение из connect.php
global $dbConnection;
$conn = $dbConnection;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Location: main.php');
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .loading {
            font-size: 24px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="loading">
        <p>Waiting for database connection...</p>
    </div>
</body>
</html>