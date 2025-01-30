<?php
session_start();
include 'database/db_connect.php';

$current_user = $_SESSION['username'];

// Fetch agent data
$agent_query = "SELECT * FROM agents WHERE agent_user = :agent_user";
$stmt = $pdo->prepare($agent_query);
$stmt->execute(['agent_user' => $current_user]);
$agent = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sales data with formatted month and year
$sales_query = "
    SELECT 
        DATE_FORMAT(ss.sale_date, '%M %Y') as sale_month, 
        SUM(ss.total_amount) as total_sales 
    FROM store_sales ss
    INNER JOIN agents a ON ss.name = a.agent_user
    WHERE a.agent_user = :agent_user 
    GROUP BY sale_month
";
$stmt = $pdo->prepare($sales_query);
$stmt->execute(['agent_user' => $current_user]);
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unsettled amounts
$unsettled_query = "
    SELECT SUM(total_amount) as unsettled_amount 
    FROM to_pay_products 
    WHERE username = :username AND payment_status = 'unsettled'
";
$stmt = $pdo->prepare($unsettled_query);
$stmt->execute(['username' => $current_user]);
$unsettled = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch current credit limit
$credit_limit = $agent['credit_limit'];

// Fetch near due date bills with formatted due date
$near_due_query = "
    SELECT *, DATE_FORMAT(due_date, '%M %d, %Y') as formatted_due_date 
    FROM to_pay_products 
    WHERE username = :username AND due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
";
$stmt = $pdo->prepare($near_due_query);
$stmt->execute(['username' => $current_user]);
$near_due_bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch overdue bills with formatted due date
$overdue_query = "
    SELECT *, DATE_FORMAT(due_date, '%M %d, %Y') as formatted_due_date 
    FROM to_pay_products 
    WHERE username = :username AND due_date < CURDATE()
";
$stmt = $pdo->prepare($overdue_query);
$stmt->execute(['username' => $current_user]);
$overdue_bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rankings of sales agents for the current month
$ranking_query = "
    SELECT a.agent_user, a.agent_fname, SUM(ss.total_amount) as total_sales
    FROM store_sales ss
    INNER JOIN agents a ON ss.name = a.agent_user
    WHERE MONTH(ss.sale_date) = MONTH(CURDATE()) AND YEAR(ss.sale_date) = YEAR(CURDATE())
    GROUP BY a.agent_user
    ORDER BY total_sales DESC
";
$stmt = $pdo->prepare($ranking_query);
$stmt->execute();
$rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determine the current user's rank
$current_user_rank = null;
foreach ($rankings as $index => $row) {
    if ($row['agent_user'] === $current_user) {
        $current_user_rank = $index + 1; // Rank is 1-based
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Agent Dashboard</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/agent.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dash-container {
            margin-top: 80px;
        }
        .card {
            margin-bottom: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 40vh;
            width: 80vw;
        }
    </style>
</head>
<body>
<?php include 'agent_header.php'; ?>

<div class="dash-container">
    <h1 class="text-center">Welcome, <?php echo htmlspecialchars($agent['agent_fname']); ?></h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Personal Sales</h2>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales_data as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['sale_month']); ?></td>
                                    <td><?php echo number_format($row['total_sales'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Financial Overview</h2>
                </div>
                <div class="card-body">
                    <p><strong>Unsettled Amount:</strong> <?php echo number_format($unsettled['unsettled_amount'], 2); ?></p>
                    <p><strong>Current Credit Limit:</strong> <?php echo number_format($credit_limit, 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Near Due Date Bills</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($near_due_bills as $row): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($row['product_id']); ?> - Due: <?php echo htmlspecialchars($row['formatted_due_date']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Overdue Bills</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($overdue_bills as $row): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($row['product_id']); ?> - Due: <?php echo htmlspecialchars($row['formatted_due_date']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Sales Agent Rankings</h2>
            </div>
            <div class="card-body">
                <p><strong>Your Rank:</strong> <?php echo $current_user_rank ? $current_user_rank : 'Not ranked this month'; ?></p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Agent Name</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rankings as $index => $row): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($row['agent_fname']); ?></td>
                                <td><?php echo number_format($row['total_sales'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <div class="chart-container">
        <h2>Sales Line Chart</h2>
        <canvas id="salesChart"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = {
        labels: [<?php foreach ($sales_data as $row) { echo '"' . $row['sale_month'] . '",'; } ?>],
        datasets: [{
            label: 'Personal Sales',
            data: [<?php foreach ($sales_data as $row) { echo $row['total_sales'] . ','; } ?>],
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: false
        }]
    };

    const config = {
        type: 'line',
        data: salesData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Sales'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Months'
                    }
                }
            }
        }
    };

    const salesChart = new Chart(ctx, config);
</script>
</body>
</html>