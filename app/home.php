<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная - Сервис доставки для студентов СПбГЭУ</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .header {
            width: 40%; /* Задаем ширину, аналогичную content для консистентности дизайна */
            padding: 10px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Уменьшаем интенсивность тени для более тонкого вида */
            position: fixed;
            top: 30px; /* Добавляем отступ сверху для визуального разделения от края экрана */
            left: 50%; /* Центрируем блок по горизонтали */
            transform: translateX(-50%); /* Смещаем блок на 50% его ширины назад, для точного позиционирования по центру */
            text-align: center;
            border-radius: 20px; /* Добавляем скругление углов */
        }

        .content {
            width: 80%;
            display: flex;
            justify-content: space-between;
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
            left: 12%; /* Maintain a 5% margin from left */
            right: 23%; /* Maintain a 5% margin from right */
            align-items: center;
        }
        .text-part {
            flex-grow: 1;
            margin: 0 60px; /* Spacing between text blocks */
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 2s, opacity 2s;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 2s ease 1s forwards;
        }
        .button {
            padding: 15px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            position: fixed;
            bottom: 20%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            animation: fadeIn 2s ease 2s forwards;
        }
        .button:hover {
            background-color: #45a049;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Университетский сервис доставки еды</h1>
    </div>
    <div class="content">
        <div class="text-part first">В нашем университете СПбГЭУ нет больших перемен, и студенты часто сталкиваются с необходимостью заказа еды, особенно в периоды занятости или опозданий на учебу.</div>
        <div class="text-part second">Наш сервис представляет собой удобное веб-приложение для доставки еды между студентами. Это проект позволяет студентам сделать заказ, если они находятся в университете, а другим студентам — принять и доставить этот заказ.</div>
    </div>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="signin.php" class="button">Начать пользоваться</a>
    <?php endif; ?>
</body>
</html>
