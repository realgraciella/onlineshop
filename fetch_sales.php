<?php
include 'database/db_connect.php';
$period = $_GET['period'];

$query = "";
switch ($period) {
    case 'weekly':
        $query = "SELECT sale_date, SUM(sale_amount) as total FROM sales WHERE sale_date >= CURDATE() - INTERVAL 7 DAY GROUP BY sale_date";
        break;
    case 'monthly':
        $query = "SELECT sale_date, SUM(sale_amount) as total FROM sales WHERE sale_date >= CURDATE() - INTERVAL 1 MONTH GROUP BY sale_date";
        break;
    case 'annual':
        $query = "SELECT MONTH(sale_date) as month, SUM(sale_amount) as total FROM sales WHERE sale_date >= CURDATE() - INTERVAL 1 YEAR GROUP BY month";
        break;
}

$stmt = $pdo->query($query);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format data for Chart.js
$labels = array_column($results, 'sale_date');
$sales = array_column($results, 'total');
echo json_encode(['labels' => $labels, 'sales' => $sales]);
?>
