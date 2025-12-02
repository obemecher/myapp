<?php
session_start();
require 'connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

// Получаем подключение из connect.php
global $dbConnection;
$conn = $dbConnection;

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $orderId = $_GET['delete'];
    $checkOrder = $conn->prepare("SELECT id_user FROM orders WHERE id = ?");
    $checkOrder->bind_param("i", $orderId);
    $checkOrder->execute();
    $result = $checkOrder->get_result();
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        if ($order['id_user'] == $_SESSION['user_id']) {
            $conn->query("DELETE FROM orders WHERE id = $orderId");
            $conn->query("DELETE FROM order_details WHERE order_id = $orderId");
        }
    }
    header("Location: getorder.php");
    exit();
}

$sql = "SELECT o.id, o.id_user, u.contacts, u.familia, u.status, o.id_shop, s.nameshop, s.adres, o.amount, o.date_time, o.statusor
        FROM orders o
        JOIN users u ON o.id_user = u.id
        JOIN Shop s ON o.id_shop = s.id
        WHERE o.date_time >= NOW() - INTERVAL 2 HOUR";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Все заказы</title>
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
        .action-link {
            color: #2196F3;
            text-decoration: none;
        }
        .delete-link {
            color: red;
            text-decoration: none;
        }
        .action-link:hover, .delete-link:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        h1 {
            color: #333;
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm("Вы уверены, что хотите удалить заказ?");
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Принять заказ</h1>
        <h2 class="section-title"></h2>
        <?php displayOrders($result, $_SESSION['user_id'], false); ?>
        
    </div>
    
</body>
</html>

<?php
function displayOrders($result, $currentUserId, $isOwnOrders) {
    echo "<table><tr><th>Фамилия заказчика</th><th>Заведение</th><th>Сумма заказа</th><th>Дата и время заказа</th><th>Статус</th><th>Действия</th></tr>";
    $result->data_seek(0); 
    while ($row = $result->fetch_assoc()) {
        if (($isOwnOrders && $row['id_user'] == $currentUserId) || (!$isOwnOrders && $row['id_user'] != $currentUserId)) {
            //$verifiedIcon = $row['status'] == 1 ? "<span style='color: green;'>&#10003;</span>" : "";
            $statusText = ($row['statusor'] === null) ? "Актуален" : 
            ($row['statusor'] == 3 ? "Завершен" : 
            ($row['statusor'] == 4 ? "Отменен" : "В исполнении"));
          $formattedDate = formatDate($row['date_time']); 

            echo "<tr><td>{$row['familia']}</td><td>{$row['nameshop']} ({$row['adres']})</td><td>{$row['amount']} руб.</td><td>$formattedDate</td><td>{$statusText}</td>";
            if ($row['id_user'] == $currentUserId) {
                echo "<td><a href='details.php?order_id={$row['id']}' class='action-link'>Посмотреть</a></td>";
            } else {
                if ($statusText == "Актуален") {
                    echo "<td><a href='myorders.php?accept={$row['id']}' class='action-link'>Подробнее</a></td>";
                } else {
                    echo "<td></td>"; 
                }
            }
            echo "</tr>";
        }
    }
    echo "</table>";
}

function formatDate($datetime) {
    $timestamp = strtotime($datetime);
    if (date('Y-m-d') == date('Y-m-d', $timestamp)) {
        return "Сегодня в " . date('H:i', $timestamp);
    } else {
        return date('d.m.Y H:i', $timestamp);
    }
}

?>