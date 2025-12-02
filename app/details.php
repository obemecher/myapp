<?php
session_start();
require 'connect.php'; 

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('Location: signin.php');
    exit();
}

$order_id = $_GET['order_id'];

// Получаем подключение из connect.php
global $dbConnection;
$conn = $dbConnection;

$userInfoQuery = $conn->prepare("SELECT id, familia, contacts FROM users WHERE id = ?");
$userInfoQuery->bind_param("i", $_SESSION['user_id']);
$userInfoQuery->execute();
$userInfo = $userInfoQuery->get_result()->fetch_assoc();
$currentUserCurerInfo = $userInfo['familia'] . ', ' . $userInfo['contacts'];
$currentUserExInfo = $userInfo['id'];

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'deliver' && $currentUserCurerInfo === $_POST['curer']) {
        $updateQuery = $conn->prepare("UPDATE orders SET statusor = 2 WHERE id = ?");
        $updateQuery->bind_param("i", $order_id);
        $updateQuery->execute();
    } 
    elseif ($_POST['action'] === 'cancel' && $currentUserCurerInfo === $_POST['curer']) {
        $updateQuery = $conn->prepare("UPDATE orders SET statusor = NULL WHERE id = ?");
        $updateQuery->bind_param("i", $order_id);
        $updateQuery->execute();
        header("Location: getorder.php");
        exit();
    } 
    elseif ($_POST['action'] === 'receive' && $currentUserExInfo == $_POST['id_user']) {
        $updateQuery = $conn->prepare("UPDATE orders SET statusor = 3 WHERE id = ?");
        $updateQuery->bind_param("i", $order_id);
        $updateQuery->execute();
        $_SESSION['message'] = "Вы отметили заказ как завершенный.";
        header("Location: details.php?order_id=$order_id");
        exit();
    }

    elseif ($_POST['action'] === 'cancel_order' && $currentUserExInfo == $_POST['id_user'] && !in_array($order_details['statusor'], [2, 3])) {
        $updateQuery = $conn->prepare("UPDATE orders SET statusor = 4 WHERE id = ?");
        $updateQuery->bind_param("i", $order_id);
        $updateQuery->execute();
        header("Location: 5.php");
        exit();
    }

}

$order_query = "SELECT o.id, o.date_time, o.amount, o.comment, o.curer, o.statusor, u.id as user_id, u.familia, u.status, u.imya, u.otch, u.contacts, s.nameshop, s.adres
                FROM orders o
                JOIN users u ON o.id_user = u.id
                JOIN Shop s ON o.id_shop = s.id
                WHERE o.id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_details = $stmt->get_result()->fetch_assoc();

$products_query = "SELECT c.name, c.price, od.quantity
                   FROM order_details od
                   JOIN catalog c ON od.product_id = c.ID
                   WHERE od.order_id = ?";
$stmt = $conn->prepare($products_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$product_details = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Подробности заказа</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        select, button, input[type="number"], input[type="text"] {
            padding: 8px 16px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Подробности заказа</h1>
        <p>Заказ от <?= date('d.m.Y H:i', strtotime($order_details['date_time'])) ?></p>
        <p>Контакты заказчика: <?= htmlspecialchars($order_details['contacts']) ?></p>
        <p>Комментарий: <?= htmlspecialchars($order_details['comment']) ?></p>
        <p>Исполнитель: <?= $order_details['curer'] ? htmlspecialchars($order_details['curer']) : "ещё не отозвался" ?></p>
        <p>Магазин: <?= htmlspecialchars($order_details['nameshop']) ?> (<?= htmlspecialchars($order_details['adres']) ?>)</p>
        <p>Общая сумма заказа: <?= $order_details['amount'] ?> руб.</p>
        <h2>Товары в заказе</h2>
        <table>
            <tr><th>Товар</th><th>Количество</th><th>Цена за единицу</th><th>Сумма</th></tr>
            <?php while ($row = $product_details->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['price'] ?> руб.</td>
                    <td><?= $row['price'] * $row['quantity'] ?> руб.</td>
                </tr>
            <?php endwhile; ?>
        </table>
        <?php
        if (!empty($_SESSION['message'])) {
            echo '<p>' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        if ($currentUserCurerInfo === $order_details['curer'] && $order_details['statusor'] == 1) {
            echo '<form method="post" onsubmit="return confirm(\'Вы подтверждаете что передали заказ?\');"><input type="hidden" name="action" value="deliver"><input type="hidden" name="curer" value="' . $currentUserCurerInfo . '"><button type="submit">Я передал заказ</button></form>';
            echo '<form method="post" onsubmit="return confirm(\'Вы действительно хотите отменить выполнение заказа?\');"><input type="hidden" name="action" value="cancel"><input type="hidden" name="curer" value="' . $currentUserCurerInfo . '"><button type="submit" class="button">Отменить выполнение</button></form>';
        
        } elseif ($currentUserCurerInfo === $order_details['curer'] && $order_details['statusor'] == 2) {
            echo '<p>Вы отметили передачу заказа.</p>'; 
        }
        
        if ($order_details['user_id'] == $_SESSION['user_id'] && $order_details['statusor'] == 2) {
            echo '<p>Курьер отметил что передал Вам заказ.</p>'; 
            echo '<form method="post" onsubmit="return confirm(\'Вы уверены, что хотите подтвердить получение заказа?\');">
                <input type="hidden" name="action" value="receive">
                <input type="hidden" name="id_user" value="' . $order_details['user_id'] . '">
                <button type="submit">Я получил заказ</button></form>';
        }
        if ($order_details['user_id'] == $_SESSION['user_id'] && !in_array($order_details['statusor'], [2, 3])) {
            echo '<form method="post" onsubmit="return confirm(\'Вы уверены, что хотите отменить заказ?\');">
                    <input type="hidden" name="action" value="cancel_order">
                    <input type="hidden" name="id_user" value="' . $order_details['user_id'] . '">
                    <button type="submit" class="button">Отменить заказ</button>
                </form>';
        }
        ?>
    </div>
</body>
</html>