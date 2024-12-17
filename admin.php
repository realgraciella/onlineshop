<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database/db_connect.php'; // Database connection


// Combined database query to optimize data fetching
$query = "
    -- Top 3 Agents with highest sales in the last month
    SELECT agent_fname, agent_lname, SUM(sale_amount) AS total_sales 
    FROM agents 
    JOIN sales ON agents.agent_id = sales.agent_id 
    WHERE sale_date >= CURDATE() - INTERVAL 1 MONTH
    GROUP BY agents.agent_id 
    HAVING total_sales > 0
    ORDER BY total_sales DESC LIMIT 3;

    -- Active agents count
    SELECT COUNT(*) AS active_agents_count FROM agents WHERE agent_status = 'active';

    -- Weekly sales (from both sales and store_sales tables)
    SELECT 
        SUM(sale_amount) AS weekly_sales 
    FROM sales 
    WHERE sale_date >= CURDATE() - INTERVAL 7 DAY;

    SELECT 
        SUM(total_amount) AS weekly_store_sales 
    FROM store_sales 
    WHERE sale_date >= CURDATE() - INTERVAL 7 DAY;

    -- Monthly sales (from both sales and store_sales tables)
    SELECT 
        SUM(sale_amount) AS monthly_sales 
    FROM sales 
    WHERE sale_date >= CURDATE() - INTERVAL 1 MONTH;

    SELECT 
        SUM(total_amount) AS monthly_store_sales 
    FROM store_sales 
    WHERE sale_date >= CURDATE() - INTERVAL 1 MONTH;

    -- Annual sales (from both sales and store_sales tables)
    SELECT 
        SUM(sale_amount) AS annual_sales 
    FROM sales 
    WHERE sale_date >= CURDATE() - INTERVAL 1 YEAR;

    SELECT 
        SUM(total_amount) AS annual_store_sales 
    FROM store_sales 
    WHERE sale_date >= CURDATE() - INTERVAL 1 YEAR;

    -- Low stock count
    SELECT product_name, stock_level FROM products WHERE stock_level < 10;

    -- Recent purchases
    SELECT rcprod_name, purchase_date FROM purchases ORDER BY purchase_date DESC LIMIT 5;

    -- Total feedback count (product and system feedback)
    SELECT 
        (SELECT COUNT(*) FROM product_feedback) + 
        (SELECT COUNT(*) FROM system_feedback) AS total_feedbacks_count;

    -- Total inquiries count (client and agent inquiries)
    SELECT 
        (SELECT COUNT(*) FROM client_inquiries) + 
        (SELECT COUNT(*) FROM agent_inquiries) AS total_inquiries_count;
";

$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetching results for each query result set
$topAgents = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $topAgents[] = $row;
}
$stmt->nextRowset();
$activeAgentsCount = $stmt->fetchColumn();
$stmt->nextRowset();
$weeklySales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$weeklyStoreSales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$monthlySales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$monthlyStoreSales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$annualSales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$annualStoreSales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$lowStockProducts = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lowStockProducts[] = $row;
}
$stmt->nextRowset();
$recentPurchases = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $recentPurchases[] = $row;
}
$stmt->nextRowset();
$totalFeedbacksCount = $stmt->fetchColumn();
$stmt->nextRowset();
$totalInquiriesCount = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>

  <!-- Favicons -->
  <link href="assets/img/logo/2.png" rel="icon">

  <!-- Minified CSS -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/admin.css" rel="stylesheet"> <!-- Ensure minified CSS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Deferred JS loading -->
  <script defer src="assets/vendor/pure counter/purecounter_vanilla.js"></script>
  <script defer src="assets/vendor/aos/aos.js"></script>
  <script defer src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script defer src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script defer src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script defer src="assets/js/admin.js"></script>

  <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f6f9;
        color: #444;
    }
    #dashboard {
        padding: 40px 20px;
        text-align: center;
        margin: 50px auto;
    }
    .sales-chart {
        width: 100%;
        max-width: 1000px;
        margin: 20px auto;
    }
    .metric {
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
    }
    .footer {
        padding: 20px;
        text-align: center;
        background-color: #f8f9fa;
        position: fixed;
        width: 100%;
        bottom: 0;
    }
    .footer p {
        margin: 0;
        font-size: 14px;
    }
    .card {
        margin: 20px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .card-body {
        padding: 20px;
    }
    .card-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .card-text {
        font-size: 18px;
        margin-bottom: 20px;
    }
    .leaderboard, .low-stock, .recent-purchases {
        margin: 20px;
        padding: 20px;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        max-height: 200px; /* Set a max height for scrollable area */
    }
  </style>
</head>

<body>
  <?php include 'admin_header.php'; ?>

  <main id="main">
    <section id="dashboard">
      <h2>DASHBOARD</h2>
      <div class="container">
        <div class="row">
          <div class="col-md-3">
            <div class="metric">
              <h5>Weekly Sales</h5>
              <p><?php echo (int)($weeklySales + $weeklyStoreSales); ?></p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="metric">
              <h5>Monthly Sales</h5>
              <p><?php echo (int)($monthlySales + $monthlyStoreSales); ?></p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="metric">
              <h5>Annual Sales</h5>
              <p><?php echo (int)($annualSales + $annualStoreSales); ?></p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="metric">
              <h5>Active Agents</h5>
              <p><?php echo (int)$activeAgentsCount; ?></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-8">
            <div class="sales-chart">
              <h3>Sales Overview</h3>
              <select id="salesFilter" class="form-select" aria-label="Sales Filter">
                  <option value="weekly">Weekly</option>
                  <option value="monthly">Monthly</option>
                  <option value="annual">Annual</option>
              </select>
              <canvas id="salesLineChart"></canvas>
            </div>
          </div>
          <div class="col-md-4">
            <div class="leaderboard">
              <h5>Top 3 Agents</h5>
              <ul class="list-group">
                <?php foreach ($topAgents as $agent): ?>
                  <li class="list-group-item">
                    <?php echo htmlspecialchars($agent['agent_fname'] . ' ' . $agent['agent_lname']); ?> - 
                    <?php echo (int)$agent['total_sales']; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
            <div class="low-stock">
              <h5>Low Stock Products</h5>
              <ul class="list-group">
                <?php foreach ($lowStockProducts as $product): ?>
                  <li class="list-group-item">
                    <?php echo htmlspecialchars($product['product_name']); ?> - 
                    <?php echo (int)$product['stock_level']; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
            <div class="recent-purchases">
                <h5>Recent Purchases</h5>
                <ul class="list-group">
                    <?php if (empty($recentPurchases)): ?>
                        <li class="list-group-item">No recent purchases found.</li>
                    <?php else: ?>
                        <?php foreach ($recentPurchases as $purchase): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($purchase['rcprod_name']); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2024 DMshop. All rights reserved.</p>
  </footer>

  <script>
    const salesData = {
        weekly: [<?php echo (int)($weeklySales + $weeklyStoreSales); ?>],
        monthly: [<?php echo (int)($monthlySales + $monthlyStoreSales); ?>],
        annual: [<?php echo (int)($annualSales + $annualStoreSales); ?>]
    };

    const ctx = document.getElementById('salesLineChart').getContext('2d');
    let salesLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sales'],
            datasets: [{
                label: 'Sales',
                data: salesData.weekly,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    document.getElementById('salesFilter').addEventListener('change', function() {
        const selectedValue = this.value;
        salesLineChart.data.datasets[0].data = salesData[selectedValue];
        salesLineChart.update();
    });
  </script>
</body>

</html>