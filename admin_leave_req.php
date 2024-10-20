<?php
// Include your database connection configuration
include 'config.php';

// Fetch data from the database
$sql = "SELECT leave_id, name, date, time, reason, status FROM staff_leave_req";
$stmt = $conn->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle the status update in the same file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leaveId = $_POST['leaveId'];
    $newStatus = $_POST['newStatus'];

    // Update the status in the database using placeholders (?)
    $updateSql = "UPDATE staff_leave_req SET status = ? WHERE leave_id = ?";
    $updateStmt = $conn->prepare($updateSql);

    try {
        $updateStmt->execute([$newStatus, $leaveId]);
        echo 'success';
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }

    // If the request was for updating the status, exit to avoid rendering the HTML below
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin Page</title>
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

  <!-- Template Main CSS File -->
  <link href="assets/css/staff.css" rel="stylesheet">

    <style>
      table {
        border-collapse: collapse;
        border-color: #FFC451;
      }

      th, td {
        border: 1px solid #FFC451;
        padding: 8px;
      }

      th {
        background-color: #FFC451;
        color: white;
      }

      .button {
        background-color: #FFC451;
        border: none;
        color: white;
        padding: 5px 10px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 12px;
        margin: 2px;
        cursor: pointer;
      }

      .pending {
        background-color: #FFC451;
      }

      .approved {
        background-color: #fff;
      }

      .rejected {
        background-color: red;
      }
  </style>
</head>

<body>

  <?php
    include 'admin_header.php';
  ?>

  <main id="main">

    <h2 style="text-align: center; margin-top: 90px; margin-bottom: 10px;">LEAVES REQUEST</h2>
    <section>
        <table>
          <thead>
              <tr>
                  <th>Staff Name</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Reason</th>
                  <th>Status</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($rows as $row): ?>
                  <tr id="leave_<?php echo $row['leave_id']; ?>">
                      <td><?php echo $row['name']; ?></td>
                      <td><?php echo $row['date']; ?></td>
                      <td><?php echo $row['time']; ?></td>
                      <td><?php echo $row['reason']; ?></td>
                      <td>
                          <select data-leave-id="<?php echo $row['leave_id']; ?>" onchange="setStatus(this)">
                              <option value="pending"  <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                              <option value="approved"  <?php echo ($row['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                              <option value="rejected"  <?php echo ($row['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                          </select>
                      </td>
                  </tr>
              <?php endforeach; ?>
                </tbody>
      </table>
    </section>


  </main><!-- End #main -->

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script>
    function setStatus(select) {
      var status = select.value;
      var row = select.parentNode.parentNode;

      console.log('Selected Status:', status);
      console.log('Row:', row);

      if (status === "pending") {
          row.className = "";
      } else if (status === "approved") {
          row.className = "approved";
      } else if (status === "rejected") {
          row.className = "rejected";
      }
  }


  function setStatus(select) {
      var leaveId = $(select).data('leave-id');
      var newStatus = $(select).val();

      // Send an asynchronous request to update the database
      $.ajax({
          type: 'POST',
          url: 'admin_leave_req.php', // Use the same file as the request handler
          data: { leaveId: leaveId, newStatus: newStatus },
          success: function(response) {
              if (response === 'success') {
                  alert('Status updated successfully');
              } else {
                  alert('Error updating status');
              }
          },
          error: function() {
              alert('Error updating status');
          }
      });
  }
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