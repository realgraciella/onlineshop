<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database/db_connect.php'; // Database connection

// Combined database query to optimize data fetching
$query = "
    SELECT agent_fname, agent_lname, SUM(quantity * price) AS total_sales 
    FROM agents 
    JOIN sales ON agents.agent_id = sales.agent_id 
    GROUP BY agents.agent_id 
    ORDER BY total_sales DESC LIMIT 1;

    SELECT COUNT(*) AS active_agents_count FROM agents WHERE agent_status = 'active';

    SELECT SUM(sale_amount) AS weekly_sales FROM sales WHERE sale_date >= CURDATE() - INTERVAL 7 DAY;

    SELECT COUNT(*) AS low_stock_count FROM products WHERE stock_level < 10;

    SELECT 
        (SELECT COUNT(*) FROM product_feedback) + 
        (SELECT COUNT(*) FROM system_feedback) AS total_feedbacks_count;

    SELECT 
        (SELECT COUNT(*) FROM client_inquiries) + 
        (SELECT COUNT(*) FROM agent_inquiries) AS total_inquiries_count;
";

$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetching results for each query result set
$topAgent = $stmt->fetch(PDO::FETCH_ASSOC);
$topAgentName = $topAgent ? $topAgent['agent_fname'] . ' ' . $topAgent['agent_lname'] : 'No data';
$stmt->nextRowset();
$activeAgentsCount = $stmt->fetchColumn();
$stmt->nextRowset();
$weeklySales = $stmt->fetchColumn() ?: 0;
$stmt->nextRowset();
$lowStockCount = $stmt->fetchColumn();
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

  <!-- Deferred JS loading -->
  <script defer src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
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
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    .grid-item {
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .grid-item:hover {
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }
    .grid-item button {
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 500;
        color: #fff;
        background-color: #4CAF50;
        transition: background-color 0.3s ease;
        align-self: flex-end;
    }
    .grid-item button:hover {
            background-color: #008a00;
    }
  </style>
</head>

<body>
  <?php include 'admin_header.php'; ?>

  <main id="main">
    <section id="dashboard">
      <h2>DASHBOARD</h2>
      <div class="grid-container">
          <div class="grid-item top-agent">
              <h3>Top Agent</h3>
              <p><?php echo htmlspecialchars($topAgentName); ?> with the highest sales</p>
              <a href="admin_topAgent.php"><button>View</button></a>
          </div>
          <div class="grid-item active-agent">
              <h3>Active Agents</h3>
              <p><?php echo (int)$activeAgentsCount; ?> active agents</p>
              <a href="admin_viewAgent.php"><button>View</button></a>
          </div>
          <div class="grid-item sales">
              <h3>Sales</h3>
              <p><?php echo (int)$weeklySales; ?> total sales this week</p>
              <a href="admin_sales.php"><button>View</button></a>
          </div>
          <div class="grid-item inventory">
              <h3>Inventory</h3>
              <p><?php echo (int)$lowStockCount; ?> products with low stock</p>
              <a href="admin_inventory.php"><button>View</button></a>
          </div>
          <div class="grid-item feedbacks">
              <h3>Feedbacks</h3>
              <p><?php echo (int)$totalFeedbacksCount; ?> total feedbacks</p>
              <a href="admin_viewFeedbacks.php"><button>View</button></a>
          </div>
          <div class="grid-item inquiries">
              <h3>Inquiries</h3>
              <p><?php echo (int)$totalInquiriesCount; ?> total inquiries</p>
              <a href="admin_viewInquiries.php"><button>View</button></a>
          </div>
      </div>
    </section>
  </main>
</body>
</html>
