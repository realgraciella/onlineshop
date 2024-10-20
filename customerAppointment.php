<?php
// Include your database connection configuration
include 'config.php';

try {
    $getLastCustIdQuery = "SELECT cust_id FROM logins ORDER BY cust_id DESC LIMIT 1";
    $lastCustIdStmt = $conn->query($getLastCustIdQuery);
    $lastCustId = $lastCustIdStmt->fetchColumn();
} catch (PDOException $e) {
    echo "Error fetching last cust_id: " . $e->getMessage();
}

// Fetch data from the 'appointments' table for the last cust_id
$sql = "SELECT customerName, service, totalPrice, appointmentDate, appointmentTime, prefferredStaff, status FROM appointments WHERE cust_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$lastCustId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Client Page</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="bootstrap-3/css/bootstrap.min.css">


  <!-- Template Main CSS File -->
  <link href="assets/css/customer.css" rel="stylesheet">

  <style>
body {
  background-color: #f9f9f9;
  font-family: 'Arial', sans-serif;
}

table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 10px;
  text-align: center; /* Center text horizontally */
  vertical-align: middle; /* Center text vertically */
  border: 1px solid #ffc451; /* Add border to cells with color #FFC451 */
}

th {
  background-color: #ffc451; /* Header background color */
  color: #fff;
  font-weight: 700;
}

td.status {
  text-transform: uppercase;
  font-weight: 600;
}

td.status.pending {
  color: orange;
}

td.status.approved {
  color: green;
}

td.status.cancelled {
  color: red;
}

td.actions {
  text-align: center;
}

.btn {
  display: inline-block;
  padding: 6px 12px;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn.edit {
  background-color: #b5ead7; /* Edit button background color */
  color: #fff;
}

.btn.edit:hover {
  background-color: #6ba092; /* Hover color for edit button */
  color: #000; /* Black text on hover */
}

.btn.delete {
  background-color: #ff7f7f; /* Delete button background color */
  color: #fff;
}

.btn.delete:hover {
  background-color: #cc6666; /* Hover color for delete button */
  color: #000; /* Black text on hover */
}

.view-bill-btn-container {
  text-align: right;
  margin-top: 20px;
}

.btn.view-bill-btn {
  display: inline-block;
  padding: 6px 12px;
  background-color: green; /* View Bill button background color */
  color: #fff; /* White text color */
  text-decoration: none;
  border-radius: 4px;
  transition: color 0.3s ease, background-color 0.3s ease;
}

.btn.view-bill-btn:hover {
  background-color: green; /* Hover background color */
  color: #000; /* Hover text color (white) */
}

  </style>

</head>

<body>

  <?php
    include 'cust_header.php';
  ?>

  

  <main id="main">
    <section>
        <div class="container">
        <h2 style="text-align: center; margin-top: 40px; margin-bottom: 10px;">APPOINTMENT TABLE</h2>
            <table id="appointmentTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Services</th>
                        <th>Total Price</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Preferred Staff</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        <?php foreach ($rows as $row) : ?>
            <tr>
                <td><?php echo $row['customerName']; ?></td>
                <td><?php echo $row['service']; ?></td>
                <td><?php echo $row['totalPrice']; ?></td>
                <td><?php echo $row['appointmentDate']; ?></td>
                <td><?php echo $row['appointmentTime']; ?></td>
                <td><?php echo $row['prefferredStaff']; ?></td>
                <td class="status <?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></td>
                <td class="actions">
                    <a href="#" class="btn edit">Edit</a>
                    <a href="#" class="btn delete">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
            </table>

            <div class="view-bill-btn-container">
                <a href="viewBill.php" class="btn view-bill-btn">View Bill</a>
            </div>
        </div>
    </section>

  </main><!-- End #main -->

  <script type="text/javascript">
         window.onload = function() {
          const table = document.getElementById("appointmentTable");
          const rows = table.getElementsByTagName("tr");

          for (let i = 1; i < rows.length; i++) {
            const statusCell = rows[i].querySelector(".status");
            const statusText = statusCell.innerText.toLowerCase();

            statusCell.classList.add(statusText);
          }
        };
  </script>

  <?php
    include 'footer.php';
  ?>

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
  <script src="assets/js/main.js"></script>
  <script src="assets/js/customer.js"></script>

</body>

</html>