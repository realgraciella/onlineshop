<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database/db_connect.php'; // Include database connection

// Fetch data for each section
try {
    // Top Agent (Agent with most sales)
    $query = "SELECT agent_fname, agent_lname, SUM(quantity * price) AS total_sales 
              FROM agents 
              JOIN sales ON agents.agent_id = sales.agent_id 
              GROUP BY agents.agent_id 
              ORDER BY total_sales DESC LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $topAgent = $stmt->fetch(PDO::FETCH_ASSOC);
    $topAgentName = $topAgent ? $topAgent['agent_fname'] . ' ' . $topAgent['agent_lname'] : 'No data';

    // Active Agents (Total active agents)
    $query = "SELECT COUNT(*) AS active_agents_count FROM agents WHERE agent_status = 'active'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $activeAgents = $stmt->fetch(PDO::FETCH_ASSOC);
    $activeAgentsCount = $activeAgents['active_agents_count'];

    // Sales (Sales summary for the week)
    $query = "SELECT SUM(sale_amount) AS weekly_sales FROM sales WHERE sale_date >= CURDATE() - INTERVAL 7 DAY";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $sales = $stmt->fetch(PDO::FETCH_ASSOC);
    $weeklySales = $sales ? $sales['weekly_sales'] : 0;

    // Inventory (Low stock products)
    $query = "SELECT product_name, stock_level FROM products WHERE stock_level < 10";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $lowStockCount = count($lowStockProducts);

    // Combined Feedbacks (Number of product and system feedbacks)
    $queryProductFeedback = "SELECT COUNT(*) AS product_feedback_count FROM product_feedback";
    $stmtProduct = $pdo->prepare($queryProductFeedback);
    $stmtProduct->execute();
    $productFeedbacks = $stmtProduct->fetch(PDO::FETCH_ASSOC);
    $productFeedbacksCount = $productFeedbacks['product_feedback_count'];

    $querySystemFeedback = "SELECT COUNT(*) AS system_feedback_count FROM system_feedback";
    $stmtSystem = $pdo->prepare($querySystemFeedback);
    $stmtSystem->execute();
    $serviceFeedbacks = $stmtSystem->fetch(PDO::FETCH_ASSOC);
    $serviceFeedbacksCount = $serviceFeedbacks['system_feedback_count'];

    // Total Combined Feedbacks
    $totalFeedbacksCount = $productFeedbacksCount + $serviceFeedbacksCount;

    // Inquiries (Number of inquiries)
    // Count inquiries from clients
    $queryClientInquiries = "SELECT COUNT(*) AS client_inquiry_count FROM client_inquiries";
    $stmtClient = $pdo->prepare($queryClientInquiries);
    $stmtClient->execute();
    $clientInquiries = $stmtClient->fetch(PDO::FETCH_ASSOC);
    $clientInquiriesCount = $clientInquiries['client_inquiry_count'];

    // Count inquiries from agents
    $queryAgentInquiries = "SELECT COUNT(*) AS agent_inquiry_count FROM agent_inquiries";
    $stmtAgent = $pdo->prepare($queryAgentInquiries);
    $stmtAgent->execute();
    $agentInquiries = $stmtAgent->fetch(PDO::FETCH_ASSOC);
    $agentInquiriesCount = $agentInquiries['agent_inquiry_count'];

    // Total Inquiries
    $totalInquiriesCount = $clientInquiriesCount + $agentInquiriesCount;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Dashboard</title>

  <!-- Favicons -->
  <link href="assets/img/logo/2.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/admin.css" rel="stylesheet">
</head>

<style>
  body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f9;
      color: #444;
  }

  #dashboard {
      padding: 40px 20px;
      text-align: center;
  }

  #dashboard h2 {
      font-size: 2em;
      font-weight: 600;
      color: #333;
      margin-top: 50px;
      margin-bottom: 30px;
  }

  .grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
  }

  .grid-item {
      background-color: #ffffff;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
  }

  .grid-item h3 {
      font-size: 1.2em;
      font-weight: 600;
      color: #444;
      margin-bottom: 10px;
  }

  .grid-item p {
      font-size: 1em;
      color: #777;
      margin: 5px 0;
  }

  .grid-item:hover {
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
  }

  .grid-item button {
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-weight: 500;
      color: #ffffff;
      transition: background-color 0.3s ease;
      align-self: flex-end;
  }

</style>

<body>
  <?php include 'admin_header.php'; ?>

  <main id="main">
    <section id="dashboard">
      <h2>DASHBOARD</h2>
      <div class="grid-container">

          <!-- Top Agent -->
          <div class="grid-item top-agent">
              <h3>Top Agent</h3>
              <p><?php echo $topAgentName; ?> with the highest sales</p>
              <a href="admin_topAgent.php">
                <button>View</button>
              </a>
          </div>

          <!-- Active Agents -->
          <div class="grid-item active-agent">
              <h3>Active Agents</h3>
              <p><?php echo $activeAgentsCount; ?> active agents</p>
              <a href="admin_viewAgent.php">
                <button>View</button>
              </a>
          </div>

          <!-- Sales -->
          <div class="grid-item sales">
              <h3>Sales</h3>
              <p><?php echo $weeklySales; ?> total sales this week</p>
              <a href="admin_sales.php">
                <button>View</button>
              </a>
          </div>

          <!-- Inventory -->
          <div class="grid-item inventory">
              <h3>Inventory</h3>
              <p><?php echo $lowStockCount; ?> products with low stock</p>
              <a href="admin_inventory.php">
                <button>View</button>
              </a>
          </div>

          <!-- Combined Feedbacks -->
          <div class="grid-item feedbacks">
              <h3>Combined Feedbacks</h3>
              <p><?php echo $totalFeedbacksCount; ?> total feedbacks (Product + Service)</p>
              <a href="admin_viewFeedbacks.php">
                <button>View</button>
              </a>
          </div>

          <!-- Inquiries -->
          <div class="grid-item inquiries">
              <h3>Inquiries</h3>
              <p><?php echo $totalInquiriesCount; ?> total inquiries (Clients + Agents)</p>
              <a href="admin_viewInquiries.php">
                <button>View</button>
              </a>
          </div>

      </div>
    </section>
  </main>

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/admin.js"></script>

</body>
</html>
