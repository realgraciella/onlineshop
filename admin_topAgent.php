<?php
include 'database/db_connect.php';

// Queries for leaderboard
$queries = [
    'weekly' => "SELECT agents.agent_fname, agents.agent_lname, SUM(sales.sale_amount) AS total_sales 
                 FROM sales 
                 JOIN agents ON sales.agent_id = agents.agent_id 
                 WHERE sales.sale_date >= CURDATE() - INTERVAL 1 WEEK 
                 GROUP BY agents.agent_id 
                 ORDER BY total_sales DESC LIMIT 5",
    'monthly' => "SELECT agents.agent_fname, agents.agent_lname, SUM(sales.sale_amount) AS total_sales 
                  FROM sales 
                  JOIN agents ON sales.agent_id = agents.agent_id 
                  WHERE sales.sale_date >= CURDATE() - INTERVAL 1 MONTH 
                  GROUP BY agents.agent_id 
                  ORDER BY total_sales DESC LIMIT 5",
    'annually' => "SELECT agents.agent_fname, agents.agent_lname, SUM(sales.sale_amount) AS total_sales 
                   FROM sales 
                   JOIN agents ON sales.agent_id = agents.agent_id 
                   WHERE sales.sale_date >= CURDATE() - INTERVAL 1 YEAR 
                   GROUP BY agents.agent_id 
                   ORDER BY total_sales DESC LIMIT 5"
];

// Prepare and execute queries
$leaderboards = [];
foreach ($queries as $period => $query) {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $leaderboards[$period] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AGENTS LEADERBOARD</title>
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
    <style>

/* Container Styling */
.top-agent-container {
    width: 80%;
    margin: 60px auto;
    padding-top: 20px;
}

/* Heading Styling */
h1 {
    font-family: 'Open Sans', sans-serif;
    font-size: 35px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 40px;
    color: #333;
}

h2 {
    font-size: 30px;
    color: #4CAF50;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 40px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px;
    text-align: left;
    font-size: 1.1rem;
}

th {
    background-color: #008a00;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
}

td {
    background-color: #f9f9f9;
    color: #333;
    border-bottom: 1px solid #ddd;
}

tr:nth-child(even) td {
    background-color: #f2f2f2;
}

tr:hover td {
    background-color: #f1f1f1;
}

/* Rank Styling */
td, th {
    font-size: 1.1rem;
}

th {
    font-size: 1.1rem;
}

/* Leaderboard Table Styling */
table {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 50px;
}

table thead {
    background-color: #4CAF50;
}

table tbody tr:hover {
    background-color: #e9ecef;
}

/* Responsive Design */
@media (max-width: 768px) {
    .top-agent-container {
        width: 90%;
        padding: 15px;
    }

    h1 {
        font-size: 2.5rem;
    }

    h2 {
        font-size: 1.5rem;
    }

    table th, table td {
        padding: 8px;
    }
}
    </style>
</head>

<body>
<?php include 'admin_header.php'; ?>
  <div class="top-agent-container">
    <h1>Agent Sales Leaderboard</h1>
    
    <!-- Weekly Leaderboard -->
    <h2>Top Sales Agents This Week</h2>
    <table>
      <thead>
        <tr>
          <th>Rank</th>
          <th>Agent Name</th>
          <th>Total Sales</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($leaderboards['weekly'] as $rank => $agent): ?>
        <tr>
          <td><?php echo $rank + 1; ?></td>
          <td><?php echo $agent['agent_fname'] . ' ' . $agent['agent_lname']; ?></td>
          <td><?php echo number_format($agent['total_sales'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Monthly Leaderboard -->
    <h2>Top Sales Agents This Month</h2>
    <table>
      <thead>
        <tr>
          <th>Rank</th>
          <th>Agent Name</th>
          <th>Total Sales</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($leaderboards['monthly'] as $rank => $agent): ?>
        <tr>
          <td><?php echo $rank + 1; ?></td>
          <td><?php echo $agent['agent_fname'] . ' ' . $agent['agent_lname']; ?></td>
          <td><?php echo number_format($agent['total_sales'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Annual Leaderboard -->
    <h2>Top Sales Agents This Year</h2>
    <table>
      <thead>
        <tr>
          <th>Rank</th>
          <th>Agent Name</th>
          <th>Total Sales</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($leaderboards['annually'] as $rank => $agent): ?>
        <tr>
          <td><?php echo $rank + 1; ?></td>
          <td><?php echo $agent['agent_fname'] . ' ' . $agent['agent_lname']; ?></td>
          <td><?php echo number_format($agent['total_sales'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>

</html>
