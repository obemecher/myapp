<?php
session_start();
require 'connect.php'; // подключение к БД через connect.php

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

// Получаем подключение из connect.php
global $dbConnection;
$conn = $dbConnection;

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $_SESSION['status'] = $row['status'];
}

if (isset($_POST['select_shop'])) {
    $_SESSION['selected_shop'] = $_POST['shop_id'];
    $_SESSION['cart_items'] = [];
    $_SESSION['total_price'] = 0;
    $_SESSION['comment'] = '';
}

if (isset($_POST['submit_cart'])) {
    
    $selected_items = $_POST['selected_items'] ?: [];
    $total_price = 0;
    $cart_items = [];

    foreach ($selected_items as $item_id => $quantity) {
        if ($quantity > 0) {
            $stmt = $conn->prepare("SELECT ID, name, price FROM catalog WHERE ID = ?");
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($item = $result->fetch_assoc()) {
                $total_price += $item['price'] * $quantity;
                $cart_items[$item['ID']] = $item + ['quantity' => $quantity];
            }
        }
    }
    $_SESSION['cart_items'] = $cart_items;
    $_SESSION['total_price'] = $total_price;
}

if (isset($_POST['confirm_order'])) {
    $_SESSION['comment'] = $_POST['comment'] ?: '';
    $id_user = $_SESSION['user_id'];
    $id_shop = $_SESSION['selected_shop'];
    $total_price = $_SESSION['total_price'];
    $comment = $_SESSION['comment'];

    $stmt = $conn->prepare("INSERT INTO orders (id_user, id_shop, date_time, amount, comment) VALUES (?, ?, NOW(), ?, ?)");
    $stmt->bind_param("iids", $id_user, $id_shop, $total_price, $comment);
    $stmt->execute();
    $order_id = $conn->insert_id; 
   
    foreach ($_SESSION['cart_items'] as $item_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
        $quantity = $item['quantity'];
        $price_per_unit = $item['price'];
        $stmt->bind_param("iiid", $order_id, $item_id, $quantity, $price_per_unit);
        $stmt->execute();
    }

    // Редирект до вывода HTML
    header('Location: myget.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Каталог товаров</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
            margin: 0;
        }
        h1, h2 {
            color: #333;
        }
        select, button, input[type="number"], input[type="text"] {
            padding: 8px 16px;
            margin: 8px 0;
            border-radius: 11px;
            border: 1px solid #ccc;
            display: block; /* Добавлено, чтобы элементы формы располагались вертикально */
            width: 100%; /* Для того чтобы элементы формы занимали всю доступную ширину внутри формы */
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .shop-form {
            margin-bottom: 20px;
            margin-left: 10; /* Исправлено значение для корректного отображения */
            width: 200px; /* Установка фиксированной ширины для формы */
        }
        .product-list {
            background: white;
            padding: 10px;
            border-radius: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.5);
            max-width: 666px;
            margin-left: 4px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-button {
            margin: 0 10px;
        }
        .quantity-display {
            width: 20px;
            text-align: center;
        }
        .confirmation-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 10px; /* Уменьшаем отступ сверху */
        }
        .total-price {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }
        .shop-image {
            position: absolute;
            top: 100px;
            left: 230px; /* Изменено значение для смещения фотографии влево относительно окна выбора заведения */
            max-width: 200px;
            max-height: 60px;
            margin-left: 1px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <h1>Выберите магазин для заказа</h1>
    <div class="shop-form">
        <form action="5.php" method="post">
            <select name="shop_id">
                <?php
                $shops = $conn->query("SELECT id, nameshop FROM Shop");
                while ($shop = $shops->fetch_assoc()) {
                    $selected = $shop['id'] == ($_SESSION['selected_shop'] ?: '') ? 'selected' : '';
                    echo "<option value='{$shop['id']}' $selected>{$shop['nameshop']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="select_shop">Выбрать магазин</button>
        </form>
    </div>

    <?php
    if (isset($_SESSION['selected_shop'])) {
        switch ($_SESSION['selected_shop']) {
            case 1:
                echo "<img src='LL.jpeg' alt='Фотография' class='shop-image'>";
                break;
            case 2:
                echo "<img src='SHK.jpeg' alt='Фотография' class='shop-image'>";
                break;
            case 3:
                echo "<img src='BV.jpeg' alt='Фотография' class='shop-image'>";
                break;
            case 4:
                echo "<img src='PD.webp' alt='Фотография' class='shop-image'>";
                break;
            case 5:
                echo "<img src='CEF.webp' alt='Фотография' class='shop-image'>";
                break;
        }
        echo "<h2>Товары доступные для заказа:</h2>";
        echo "<form action='5.php' method='post' class='product-list'>";
        $shop_id = $_SESSION['selected_shop'];
        $result = $conn->query("SELECT ID, name, price FROM catalog WHERE id_shop = $shop_id");

        while ($row = $result->fetch_assoc()) {
            $quantity = isset($_SESSION['cart_items'][$row['ID']]['quantity']) ? $_SESSION['cart_items'][$row['ID']]['quantity'] : 0;
            echo "<div class='product-item'>";
            echo "{$row['name']} - {$row['price']} руб.";
            echo "<div class='quantity-control'>";
            echo "<button type='button' class='quantity-button' data-item-id='{$row['ID']}' data-action='decrease'>-</button>";
            echo "<span class='quantity-display'>$quantity</span>";
            echo "<button type='button' class='quantity-button' data-item-id='{$row['ID']}' data-action='increase'>+</button>";
            echo "</div>";
            echo "<input type='hidden' name='selected_items[{$row['ID']}]' value='$quantity'>";
            echo "</div>";
        }

        echo "<button type='submit' name='submit_cart'>Добавить в корзину</button>";
        echo "</form>";

        if (!empty($_SESSION['cart_items'])) {
            echo "<h2>Выбранные товары:</h2>";
            foreach ($_SESSION['cart_items'] as $item) {
                echo "<p>{$item['name']} - {$item['quantity']} шт. по {$item['price']} руб.</p>";
            }
            echo "<p class='total-price'>Итого: {$_SESSION['total_price']} руб.</p>";
            echo "<form action='5.php' method='post'>";
            echo "<input type='text' name='comment' placeholder='Добавьте комментарий к заказу' style='width: 90%; margin-bottom: 10px;' value='" . ($_SESSION['comment'] ?: '') . "'>";
            
            echo "<div class='confirmation-section'>";
           
            if ($_SESSION['status'] !== 1) {
                echo "<p style='margin-bottom: 10px;'>Добавьте выбор нескольких заведений</p>";
                echo "<label class='checkbox_label'><input type='checkbox' name='status_checkbox'></label>";
            }
            echo "</div>"; 

            echo "<div class='confirmation-section'>";
            echo "<p><button type='submit' name='confirm_order'>Подтвердить заказ</button></p>";
            echo "</div>"; 

            echo "</form>";
        }
    }
    ?>
    
    <script src="script.js"></script>
</body>
</html>