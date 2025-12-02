<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

$conn = $dbConnection;

// Безопасный prepare
function safePrepare($conn, $sql) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Ошибка SQL: " . $conn->error . "<br>Запрос: " . $sql);
    }
    return $stmt;
}

$order_id = isset($_GET['accept']) ? intval($_GET['accept']) : 0;

if (isset($_POST['change_status'], $_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = ($_POST['change_status'] == 'complete') ? 1 : NULL;

    $userStmt = safePrepare($conn, "SELECT familia, contacts FROM users WHERE id = ?");
    $userStmt->bind_param("i", $_SESSION['user_id']);
    $userStmt->execute();
    $user = $userStmt->get_result()->fetch_assoc();
    $curerInfo = $user['familia'] . ', ' . $user['contacts'];

    $updateStmt = safePrepare($conn, "UPDATE orders SET statusor = ?, curer = ? WHERE id = ?");
    $updateStmt->bind_param("isi", $new_status, $curerInfo, $order_id);
    $updateStmt->execute();
    header("Location: myorders.php?accept=" . $order_id);
    exit();
}

$orderStmt = safePrepare($conn, "SELECT o.id, o.date_time, o.amount, o.comment, o.statusor, o.curer,
                                        u.familia, u.imya, u.otch, u.contacts,
                                        s.nameshop, s.adres
                                 FROM orders o
                                 JOIN users u ON o.id_user = u.id
                                 JOIN Shop s ON o.id_shop = s.id
                                 WHERE o.id = ?");
$orderStmt->bind_param("i", $order_id);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

$prodStmt = safePrepare($conn, "SELECT c.name, c.price, od.quantity
                                FROM order_details od
                                JOIN catalog c ON od.product_id = c.ID
                                WHERE od.order_id = ?");
$prodStmt->bind_param("i", $order_id);
$prodStmt->execute();
$products = $prodStmt->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подробности заказа</title>
    <style>
        body { font-family: Arial; background-color: #f2f2f2; margin: 0; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .button { padding: 8px 16px; margin-top: 10px; cursor: pointer;
                  background-color: #4CAF50; color: white; border: none; border-radius: 4px; }
        .button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>Подробности заказа</h1>
    <p>Заказ №<?= $order['id'] ?> от <?= date('d.m.Y H:i', strtotime($order['date_time'])) ?></p>
    <p>Заказчик: <?= htmlspecialchars($order['familia'] . " " . $order['imya'] . " " . $order['otch']) ?></p>
    <p>Статус: <?= $order['statusor'] == 1 ? 'Выполнен' : 'Актуален' ?></p>
    <p>Исполнитель: <?= htmlspecialchars($order['curer']) ?></p>
    <p>Магазин: <?= htmlspecialchars($order['nameshop']) ?> (<?= htmlspecialchars($order['adres']) ?>)</p>
    <p>Комментарий: <?= htmlspecialchars($order['comment']) ?></p>
    <p>Сумма: <?= $order['amount'] ?> руб.</p>

    <h2>Товары</h2>
    <table>
        <tr><th>Товар</th><th>Количество</th><th>Цена</th><th>Сумма</th></tr>
        <?php while ($p = $products->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= $p['quantity'] ?></td>
                <td><?= $p['price'] ?> руб.</td>
                <td><?= $p['price'] * $p['quantity'] ?> руб.</td>
            </tr>
        <?php endwhile; ?>
    </table>

    <form method="post" onsubmit="return confirm('Подтвердить действие?');">
        <input type="hidden" name="order_id" value="<?= $order_id ?>">
        <?php if ($order['statusor'] != 1): ?>
            <button class="button" type="submit" name="change_status" value="complete">Выполнить</button>
        <?php else: ?>
            <button class="button" type="submit" name="change_status" value="uncomplete">Отменить выполнение</button>
        <?php endif; ?>
    </form>
</body>
</html>
