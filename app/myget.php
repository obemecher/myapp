<?php
session_start();
require 'connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

global $dbConnection;
$conn = $dbConnection;

// --- Проверка ошибок подключения
if (!$conn) {
    die("Ошибка: подключение к базе не установлено");
}

// --- Заказы, где пользователь — клиент
$customerOrdersSql = "
    SELECT o.id, o.id_user, u.contacts, u.familia, u.status, o.id_shop, s.nameshop, s.adres, o.amount, o.date_time, o.statusor
    FROM orders o
    JOIN users u ON o.id_user = u.id
    JOIN Shop s ON o.id_shop = s.id
    WHERE o.id_user = ? AND o.date_time >= NOW() - INTERVAL 2 HOUR
";

$customerOrdersStmt = $conn->prepare($customerOrdersSql);
if (!$customerOrdersStmt) {
    die("Ошибка SQL (customerOrders): " . $conn->error);
}
$customerOrdersStmt->bind_param("i", $_SESSION['user_id']);
$customerOrdersStmt->execute();
$customerOrdersResult = $customerOrdersStmt->get_result();

// --- Данные пользователя (курьера)
$userInfoQuery = $conn->prepare("SELECT familia, contacts FROM users WHERE id = ?");
if (!$userInfoQuery) {
    die("Ошибка SQL (userInfoQuery): " . $conn->error);
}
$userInfoQuery->bind_param("i", $_SESSION['user_id']);
$userInfoQuery->execute();
$userInfo = $userInfoQuery->get_result()->fetch_assoc();
$curerInfo = $userInfo['familia'] . ', ' . $userInfo['contacts'];

// --- Заказы, где пользователь — курьер
$courierOrdersSql = "
    SELECT o.id, o.id_user, u.contacts, u.familia, u.status, o.id_shop, s.nameshop, s.adres, o.amount, o.date_time, o.statusor
    FROM orders o
    JOIN users u ON o.id_user = u.id
    JOIN Shop s ON o.id_shop = s.id
    WHERE o.curer = ? AND o.date_time >= NOW() - INTERVAL 2 HOUR
";
$courierOrdersStmt = $conn->prepare($courierOrdersSql);
if (!$courierOrdersStmt) {
    die("Ошибка SQL (courierOrders): " . $conn->error);
}
$courierOrdersStmt->bind_param("s", $curerInfo);
$courierOrdersStmt->execute();
$courierOrdersResult = $courierOrdersStmt->get_result();

function formatDate($datetime) {
    $timestamp = strtotime($datetime);
    return (date('Y-m-d') == date('Y-m-d', $timestamp))
        ? "Сегодня в " . date('H:i', $timestamp)
        : date('d.m.Y H:i', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Все заказы</title>
<style>
body {font-family: Arial; background-color: #f2f2f2; padding: 20px;}
table {width: 100%; border-collapse: collapse; margin-top: 20px;}
th, td {border: 1px solid #ddd; padding: 8px;}
th {background-color: #4CAF50; color: white;}
</style>
</head>
<body>
<div class="container">
    <?php if ($customerOrdersResult->num_rows > 0): ?>
        <h1>Я заказчик</h1>
        <?php displayOrders($customerOrdersResult); ?>
    <?php endif; ?>

    <?php if ($courierOrdersResult->num_rows > 0): ?>
        <h1>Я курьер</h1>
        <?php displayOrders($courierOrdersResult); ?>
    <?php endif; ?>
</div>
</body>
</html>

<?php
function displayOrders($result) {
    echo "<table><tr><th>Заведение</th><th>Сумма</th><th>Дата</th><th>Статус</th><th>Действие</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $formattedDate = formatDate($row['date_time']);
        switch ($row['statusor']) {
            case 3: $statusText = "Завершен"; $detailLink = ""; break;
            case 4: $statusText = "Отменен"; $detailLink = ""; break;
            case null: $statusText = "Курьер не найден"; 
                       $detailLink = "<a href='details.php?order_id={$row['id']}'>Подробнее</a>"; break;
            default: $statusText = "В исполнении";
                     $detailLink = "<a href='details.php?order_id={$row['id']}'>Подробнее</a>"; break;
        }
        echo "<tr><td>{$row['nameshop']} ({$row['adres']})</td>
              <td>{$row['amount']} руб.</td>
              <td>$formattedDate</td>
              <td>$statusText</td>
              <td>$detailLink</td></tr>";
    }
    echo "</table>";
}
?>
