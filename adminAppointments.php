<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle status update
    $appointmentId = $_POST['appointmentId'];
    $newStatus = $_POST['newStatus'];

    // Update the status in the database
    $updateSql = "UPDATE appointments SET status = :newStatus WHERE app_id = :appointmentId";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bindParam(':newStatus', $newStatus);
    $updateStmt->bindParam(':appointmentId', $appointmentId);

    try {
        $updateStmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
        exit();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating status: ' . $e->getMessage()]);
        exit();
    }
} else {
    // Fetch data from the 'appointments' table
    $sql = "SELECT app_id, customerName, service, totalPrice, appointmentDate, appointmentTime, prefferredStaff, status FROM appointments";
    $stmt = $conn->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <!-- Template Main CSS File -->
  <link href="assets/css/customer.css" rel="stylesheet">

  <style>
        table {
        border-collapse: collapse;
        border-color: #FFC451;
        justify-content: center;
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

      .cancelled {
        background-color: red;
      }

      .btn {
        display: inline-block;
        padding: 6px 12px;
        background-color: green;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn:hover {
        background-color: green;
        color: #000;
    }

    .generate-receipt-btn-container,
    .print-receipt-btn-container {
        display: inline-block;
        margin-right: 10px;
    }
  </style>
</head>

<body>

  <?php
    include 'admin_header.php';
  ?>

  <main id="main">

    <section>
    <div class="container">
        <h2 style="text-align: center; margin-top: 50px; margin-bottom: 50px;">APPOINTMENTS REQUEST</h2>
        <div class="lamesa">
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
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $row['customerName']; ?></td>
                <td><?php echo $row['service']; ?></td>
                <td><?php echo $row['totalPrice']; ?></td>
                <td><?php echo $row['appointmentDate']; ?></td>
                <td><?php echo $row['appointmentTime']; ?></td>
                <td><?php echo $row['prefferredStaff']; ?></td>
                <td class="status">
                    <form onsubmit="return updateStatus(this)">
                        <input type="hidden" name="appointmentId" value="<?php echo $row['app_id']; ?>">
                        <select name="newStatus">
                            <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo ($row['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="cancelled" <?php echo ($row['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <input type="submit" value="Update" style="background-color: green; color: white; border: 1px solid white;">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

            <!-- ... (unchanged) ... -->
        </div>
    </div>
</section>

  </main><!-- End #main -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
      function updateStatus(form) {
        var appointmentId = form.appointmentId.value;
        var newStatus = form.newStatus.value;

        $.ajax({
            type: 'POST',
            url: 'adminAppointments.php',
            data: { appointmentId: appointmentId, newStatus: newStatus },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('Status updated successfully');
                    location.reload();
                } else {
                    alert('Error updating status: ' + response.message);
                }
            },
            error: function() {
                alert('Error updating status');
            }
        });

        return false;
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