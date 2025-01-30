<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database/db_connect.php'; // Database connection

// Fetch distinct months and years from sales
$monthsQuery = "SELECT DISTINCT MONTH(sale_date) AS month FROM store_sales ORDER BY month";
$yearsQuery = "SELECT DISTINCT YEAR(sale_date) AS year FROM store_sales ORDER BY year DESC";

$monthsStmt = $pdo->prepare($monthsQuery);
$monthsStmt->execute();
$months = $monthsStmt->fetchAll(PDO::FETCH_ASSOC);

$yearsStmt = $pdo->prepare($yearsQuery);
$yearsStmt->execute();
$years = $yearsStmt->fetchAll(PDO::FETCH_ASSOC);

$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : null;
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : null;

// Combined database query to optimize data fetching
$query = "
    -- Top 3 Agents with highest sales in the selected month
    SELECT agent_fname, agent_lname, SUM(total_amount) AS total_sales 
    FROM agents 
    JOIN store_sales ON agents.agent_user = store_sales.name 
    WHERE (sale_date >= CURDATE() - INTERVAL 1 MONTH OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year))
    GROUP BY agents.agent_user
    HAVING total_sales > 0
    ORDER BY total_sales DESC LIMIT 3;

    -- Active agents count
    SELECT COUNT(*) AS active_agents_count FROM agents WHERE agent_status = 'active';

    -- Weekly sales (from both sales and store_sales tables)
    SELECT 
        SUM(sale_amount) AS weekly_sales 
    FROM sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 7 DAY OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    SELECT 
        SUM(total_amount) AS weekly_store_sales 
    FROM store_sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 7 DAY OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    -- Monthly sales (from both sales and store_sales tables)
    SELECT 
        SUM(sale_amount) AS monthly_sales 
    FROM sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 1 MONTH OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    SELECT 
        SUM(total_amount) AS monthly_store_sales 
    FROM store_sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 1 MONTH OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    -- Annual sales (from both sales and store_sales tables)
    SELECT 
        SUM(sale_amount) AS annual_sales 
    FROM sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 1 YEAR OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    SELECT 
        SUM(total_amount) AS annual_store_sales 
    FROM store_sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 1 YEAR OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

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

    -- Previous weekly sales
    SELECT 
        SUM(sale_amount) AS previous_weekly_sales 
    FROM sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 14 DAY AND sale_date < CURDATE() - INTERVAL 7 DAY OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    SELECT 
        SUM(total_amount) AS previous_weekly_store_sales 
    FROM store_sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 14 DAY AND sale_date < CURDATE() - INTERVAL 7 DAY OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    -- Previous monthly sales
    SELECT 
        SUM(sale_amount) AS previous_monthly_sales 
    FROM sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 2 MONTH AND sale_date < CURDATE() - INTERVAL 1 MONTH OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    SELECT 
        SUM(total_amount) AS previous_monthly_store_sales 
    FROM store_sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 2 MONTH AND sale_date < CURDATE() - INTERVAL 1 MONTH OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    -- Previous annual sales
    SELECT 
        SUM(sale_amount) AS previous_annual_sales 
    FROM sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 2 YEAR AND sale_date < CURDATE() - INTERVAL 1 YEAR OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    SELECT 
        SUM(total_amount) AS previous_annual_store_sales 
    FROM store_sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 2 YEAR AND sale_date < CURDATE() - INTERVAL 1 YEAR OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    -- Quarterly sales (from both sales and store_sales tables)
    SELECT 
        SUM(sale_amount) AS quarterly_sales 
    FROM sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 3 MONTH OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    SELECT 
        SUM(total_amount) AS quarterly_store_sales 
    FROM store_sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 3 MONTH OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year));

    -- Most purchased products based on product_id
    SELECT product_id, product_name, SUM(quantity) AS total_quantity
    FROM store_sales
    JOIN products ON store_sales.product_id = products.product_id
    WHERE (MONTH(sale_date) = :month AND YEAR(sale_date) = :year)
    GROUP BY product_id
    ORDER BY total_quantity DESC
    LIMIT 5;

    -- Least purchased products based on product_id
    SELECT product_id, product_name, SUM(quantity) AS total_quantity
    FROM store_sales
    JOIN products ON store_sales.product_id = products.product_id
    WHERE (MONTH(sale_date) = :month AND YEAR(sale_date) = :year)
    GROUP BY product_id
    ORDER BY total_quantity ASC
    LIMIT 5;

    -- Unsettled Amount
    SELECT SUM(total_amount) AS unsettled_amount FROM orders WHERE payment_status = 'Unpaid';

    -- Top Brand for the Month
    SELECT brand_name, SUM(total_amount) AS total_sales 
    FROM store_sales 
    WHERE (sale_date >= CURDATE() - INTERVAL 1 MONTH OR (MONTH(sale_date) = :month AND YEAR(sale_date) = :year))
    GROUP BY brand_name 
    ORDER BY total_sales DESC 
    LIMIT 1;
";

// Prepare the statement
$stmt = $pdo->prepare($query);

// Bind parameters for month and year
$stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
$stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);

// Execute the query
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

// Previous weekly sales
$previousWeeklySales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$previousWeeklyStoreSales = $stmt->fetchColumn() ?: 0;

// Previous monthly sales
$previousMonthlySales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$previousMonthlyStoreSales = $stmt->fetchColumn() ?: 0;

// Previous annual sales
$previousAnnualSales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$previousAnnualStoreSales = $stmt->fetchColumn() ?: 0;

// Current sales
$currentWeeklySales = (int)($weeklySales + $weeklyStoreSales);
$currentMonthlySales = (int)($monthlySales + $monthlyStoreSales);
$currentAnnualSales = (int)($annualSales + $annualStoreSales);

// Previous sales
$previousWeeklyTotal = (int)($previousWeeklySales + $previousWeeklyStoreSales);
$previousMonthlyTotal = (int)($previousMonthlySales + $previousMonthlyStoreSales);
$previousAnnualTotal = (int)($previousAnnualSales + $previousAnnualStoreSales);

// Determine indicator classes
$weeklyIndicatorClass = $currentWeeklySales > $previousWeeklyTotal ? 'text-success' : 'text-danger';
$monthlyIndicatorClass = $currentMonthlySales > $previousMonthlyTotal ? 'text-success' : 'text-danger';
$annualIndicatorClass = $currentAnnualSales > $previousAnnualTotal ? 'text-success' : 'text-danger';

// Fetch quarterly sales
$quarterlySales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$quarterlyStoreSales = $stmt->fetchColumn() ?: 0;

// Current quarterly sales
$currentQuarterlySales = (int)($quarterlySales + $quarterlyStoreSales);

// Sales data for all periods
$salesData = [
  'weekly' => [(int)($weeklySales + $weeklyStoreSales)],
  'monthly' => [(int)($monthlySales + $monthlyStoreSales)],
  'quarterly' => [(int)($currentQuarterlySales)],
  'annual' => [(int)($annualSales + $annualStoreSales)]
];

// Most purchased products
$stmt = $pdo->prepare("
    SELECT product_id, product_name, SUM(quantity) AS total_quantity
    FROM store_sales
    WHERE (MONTH(sale_date) = :month AND YEAR(sale_date) = :year)
    GROUP BY product_id
    ORDER BY total_quantity DESC
    LIMIT 3;
");
$stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
$stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
$stmt->execute();
$mostPurchasedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Least purchased products
$stmt = $pdo->prepare("
    SELECT product_id, product_name, SUM(quantity) AS total_quantity
    FROM store_sales
    WHERE (MONTH(sale_date) = :month AND YEAR(sale_date) = :year)
    GROUP BY product_id
    ORDER BY total_quantity ASC
    LIMIT 3;
");
$stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
$stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
$stmt->execute();
$leastPurchasedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$salesData2 = [
  'weekly' => [],
  'monthly' => [],
  'quarterly' => [],
  'annual' => [],
  'all' => []
];

// Weekly sales (last 7 days)
$stmt = $pdo->prepare("SELECT DATE(sale_date) AS date, SUM(total_amount) AS total FROM store_sales WHERE sale_date >= CURDATE() - INTERVAL 7 DAY GROUP BY DATE(sale_date)");
$stmt->execute();
$salesData2['weekly'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly sales (last 30 days)
$stmt = $pdo->prepare("SELECT DATE(sale_date) AS date, SUM(total_amount) AS total FROM store_sales WHERE sale_date >= CURDATE() - INTERVAL 1 MONTH GROUP BY DATE(sale_date)");
$stmt->execute();
$salesData2['monthly'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Quarterly sales (last 3 months)
$stmt = $pdo->prepare("SELECT DATE_FORMAT(sale_date, '%Y-%m') AS month, SUM(total_amount) AS total FROM store_sales WHERE sale_date >= CURDATE() - INTERVAL 3 MONTH GROUP BY DATE_FORMAT(sale_date, '%Y-%m')");
$stmt->execute();
$salesData2['quarterly'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Annual sales (last 12 months)
$stmt = $pdo->prepare("SELECT DATE_FORMAT(sale_date, '%Y-%m') AS month, SUM(total_amount) AS total FROM store_sales WHERE sale_date >= CURDATE() - INTERVAL 1 YEAR GROUP BY DATE_FORMAT(sale_date, '%Y-%m')");
$stmt->execute();
$salesData2['annual'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Combine all data for 'All' filter
$salesData2['all'] = array_merge($salesData2['weekly'], $salesData2['monthly'], $salesData2['quarterly'], $salesData2['annual']);

$salesDataJson2 = json_encode($salesData2);

// Unsettled Amount
$stmt = $pdo->prepare("SELECT SUM(total_amount) AS unsettled_amount FROM orders WHERE payment_status = 'Unpaid'");
$stmt->execute();
$unsettledAmount = $stmt->fetchColumn() ?: 0;

// Top Brand for the Month
$stmt = $pdo->prepare("
    SELECT b.brand_name, SUM(ss.total_amount) AS total_sales 
    FROM store_sales ss
    JOIN products p ON ss.product_id = p.product_id
    JOIN brands b ON p.brand_id = b.brand_id
    WHERE (MONTH(ss.sale_date) = :month AND YEAR(ss.sale_date) = :year)
    GROUP BY b.brand_id 
    ORDER BY total_sales DESC 
    LIMIT 1;
");
$stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
$stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
$stmt->execute();
$topBrand = $stmt->fetch(PDO::FETCH_ASSOC);
function formatSales($number) {
  if ($number >= 1000) {
      return number_format($number / 1000, 1) . 'K'; // Format as thousands
  }
  return number_format($number); // Return as is for numbers less than 1000
}

function formatunsettledAmount($number) {
  if ($number >= 1000) {
      return number_format($number / 1000, 1) . 'K'; // Format as thousands
  }
  return number_format($number); // Return as is for numbers less than 1000
}

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
  <script defer src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script defer src="assets/vendor/aos/aos.js"></script>
  <script defer src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script defer src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script defer src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script defer src="assets/js/admin.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #e6e6e6;
        color: #444;
    }
    #dashboard {
        padding: 30px 20px;
        text-align: center;
        margin: 30px auto;
    }
    .metric {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgb(255, 255, 255);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        margin: 20px auto;
        transition: transform 0.2s;
    }
    .metric h5 {
        font-size: 14px; 
        margin: 0;
        color: #6c757d;
    }
    .metric p {
        font-family: 'Arial', sans-serif; 
        font-weight: bold; 
        font-size: 32px; 
        margin: 0;
    }
    .metric:hover {
        transform: translateY(-5px);
    }
    .footer {
        padding: 20px;
        text-align: center;
        background-color: #e6e6e6;
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
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        background: #ffffff;
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
    .most-purchased h5, .leaderboard h5, .least-purchased h5, .low-stock h5{
        font-family: 'Arial', sans-serif;  
        font-weight: bold;  
        font-size: 25px; 
        margin: 0;
    }
    .leaderboard, .low-stock {
        margin: 20px;
        padding: 20px;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        max-height: 200px; 
    }
    .sales-chart {
        width: 100%;
        height: 600px;
        margin: 20px auto;
        padding: 80px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
        overflow: visible;
    }
    .btn {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        transition: background-color 0.3s;
    }
    .btn:hover {
        background-color: #0056b3;
    }
    .most-purchased, .least-purchased {
    margin: 20px;
    padding: 20px;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    max-height: 130px; 
    }
    .filter-form {
    display: flex;
    gap: 10px; 
    }

    .filter-form .form-select {
        width: auto; 
    }
  </style>
</head>

<body>
  <?php include 'admin_header.php'; ?>

  <main id="main">
    <section id="dashboard">
      <h2 class="mb-4">Dashboard</h2>
      <div class="col-md-12 d-flex justify-content-end mb-3">
          <div class="filter-form d-flex">
              <form method="GET" action="" class="d-flex">
                  <select name="month" class="form-select me-2" aria-label="Month" onchange="this.form.submit()">
                      <option value="">Select Month</option>
                      <?php foreach ($months as $month): ?>
                          <option value="<?php echo $month['month']; ?>" <?php echo $selectedMonth == $month['month'] ? 'selected' : ''; ?>>
                              <?php echo date('F', mktime(0, 0, 0, $month['month'], 1)); ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
                  <select name="year" class="form-select me-2" aria-label="Year" onchange="this.form.submit()">
                      <option value="">Select Year</option>
                      <?php foreach ($years as $year): ?>
                          <option value="<?php echo $year['year']; ?>" <?php echo $selectedYear == $year['year'] ? 'selected' : ''; ?>>
                              <?php echo $year['year']; ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              </form>
          </div>
      </div>
      <div class="container">
        <div class="row">
        <div class="col-md-4">
            <div class="metric">
                <h5>Weekly Sales</h5>
                <p class="<?php echo $weeklyIndicatorClass; ?>">PHP
                    <?php echo formatSales($currentWeeklySales); ?>
                    <span class="indicator"><?php echo $currentWeeklySales > $previousWeeklyTotal ? '↑' : '↓'; ?></span>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric">
                <h5>Monthly Sales</h5>
                <p class="<?php echo $monthlyIndicatorClass; ?>"> PHP
                    <?php echo formatSales($currentMonthlySales); ?>
                    <span class="indicator"><?php echo $currentMonthlySales > $previousMonthlyTotal ? '↑' : '↓'; ?></span>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric">
                <h5>Annual Sales</h5>
                <p class="<?php echo $annualIndicatorClass; ?>">PHP
                    <?php echo formatSales($currentAnnualSales); ?>
                    <span class="indicator"><?php echo $currentAnnualSales > $previousAnnualTotal ? '↑' : '↓'; ?></span>
                </p>
            </div>
        </div>
        <div class="col-md-4">
              <div class="metric">
                    <h5>Active Agents</h5>
                    <p><?php echo (int)$activeAgentsCount; ?></p>
              </div>
        </div>
            <div class="col-md-4">
                <div class="metric">
                    <h5>Unsettled Amount</h5>
                    <p class="<?php echo $unsettledAmount > 0 ? 'text-danger' : 'text-success'; ?>">
                        PHP <?php echo formatUnsettledAmount($unsettledAmount); ?>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric">
                    <h5>Top Brand</h5>
                    <?php if ($topBrand): ?>
                        <p><?php echo htmlspecialchars($topBrand['brand_name']); ?>
                    <?php else: ?>
                        <p>No Top Brand</p>
                    <?php endif; ?>
                </div>
            </div>
      </div>

        <div class="row">
        <div class="col-md-8">
          <div class="sales-chart">
              <h3 style="font-family: Arial, sans-serif; font-weight: bold; text-align: left;">Sales Overview</h3>
              <div style="display: flex; justify-content: flex-end; ">
                  <select id="salesFilter" class="form-select" aria-label="Sales Filter" style="width: auto;">
                      <option value="all">All Sales</option>
                      <option value="weekly">Weekly</option>
                      <option value="monthly">Monthly</option>
                      <option value="quarterly">Quarterly</option>
                      <option value="annual">Annual</option>
                  </select>
              </div>
              <canvas id="salesLineChart" style="height: 100%; width: 100%;"></canvas>
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
            <div class="most-purchased">
                <h5>Most Purchased Products</h5>
                <ul class="list-group">
                    <?php if (empty($mostPurchasedProducts)): ?>
                        <li class="list-group-item">No products found.</li>
                    <?php else: ?>
                        <?php foreach ($mostPurchasedProducts as $product): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($product['product_name']); ?> - 
                                <?php echo (int)($product['total_quantity']); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="least-purchased">
                <h5>Least Purchased Products</h5>
                <ul class="list-group">
                    <?php if (empty($leastPurchasedProducts)): ?>
                        <li class="list-group-item">No products found.</li>
                    <?php else: ?>
                        <?php foreach ($leastPurchasedProducts as $product): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($product['product_name']); ?> - 
                                <?php echo (int)($product['total_quantity']); ?>
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
  // Sales Chart Setup
  const salesData = <?php echo $salesDataJson2; ?>;
  const ctx = document.getElementById('salesLineChart').getContext('2d');
  const salesLineChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Weekly', 'Monthly', 'Quarterly', 'Annual'], // Default labels
      datasets: [{
        label: 'Sales',
        data: salesData.all.map(item => item.total), // Default data for 'All'
        borderColor: '#007bff',
        backgroundColor: 'rgba(0, 255, 115, 0.29)',
        fill: true,
        tension: 0.4
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

  function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { month: 'long', day: 'numeric', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  }

  function formatAnnualDate(dateString) {
    const date = new Date(dateString);
    return date.getFullYear(); // Return only the year
  }

  function formatQuarterlyDate(dateString) {
      const date = new Date(dateString);
      const options = { month: 'long', year: 'numeric' };
      return date.toLocaleDateString('en-US', options); // Return month and year
  }

  function formatMonthlyDate(dateString) {
      const date = new Date(dateString);
      const options = { month: 'long' };
      return date.toLocaleDateString('en-US', options); // Return only the month
  }
  // Handle sales filter selection
  document.getElementById('salesFilter').addEventListener('change', function(event) {
    const selectedFilter = event.target.value;
    let filteredData = [];
    let newLabels = [];

    if (selectedFilter === 'weekly') {
        newLabels = salesData.weekly.map(item => formatDate(item.date)); // Keep the original format for weekly
        filteredData = salesData.weekly.map(item => item.total);
    } else if (selectedFilter === 'monthly') {
        newLabels = salesData.monthly.map(item => formatMonthlyDate(item.date)); // Format for monthly
        filteredData = salesData.monthly.map(item => item.total);
    } else if (selectedFilter === 'quarterly') {
        newLabels = salesData.quarterly.map(item => formatQuarterlyDate(item.month)); // Format for quarterly
        filteredData = salesData.quarterly.map(item => item.total);
    } else if (selectedFilter === 'annual') {
        newLabels = salesData.annual.map(item => formatAnnualDate(item.month)); // Format for annual
        filteredData = salesData.annual.map(item => item.total);
    } else {
        newLabels = ['Weekly', 'Monthly', 'Quarterly', 'Annual'];
        filteredData = salesData.all.map(item => item.total);
    }

    salesLineChart.data.labels = newLabels;
    salesLineChart.data.datasets[0].data = filteredData;
    salesLineChart.update();
});

</script>

</body>

</html>