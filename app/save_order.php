<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['confirm'])) {
    header('Location: 5.php');
    exit();
}

require 'connect.php'; // подключение к БД через connect.php

try {
    // Получаем подключение из connect.php
    global $dbConnection;
    $conn = $dbConnection;

    $conn->autocommit(FALSE); // отключаем автокоммит

    $totalPrice = 0;
    foreach ($_POST['product'] as $productId => $quantity) {
        if ($quantity > 0) {
            $stmt = $conn->prepare("SELECT price FROM catalog WHERE id = ?");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $price = $result->fetch_assoc()['price'];
            $totalPrice += $price * $quantity;
        }
    }

    $stmt = $conn->prepare("INSERT INTO Orders (user_id, total_price, status) VALUES (?, ?, 'в_ожидании')");
    $stmt->bind_param("is", $_SESSION['user_id'], $totalPrice);
    $stmt->execute();
    $orderId = $conn->insert_id;

    foreach ($_POST['product'] as $productId => $quantity) {
        if ($quantity > 0) {
            $stmt = $conn->prepare("INSERT INTO Order_Details (order_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $orderId, $productId, $quantity);
            $stmt->execute();
        }
    }
    $conn->commit();

    echo "Заказ успешно сохранен!";
} catch (Exception $e) {
    $conn->rollback();
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>