<?php
include 'config.php';

try {
    // SQL query to retrieve records from 'inquiries' table
    $sql = "SELECT * FROM feedbacks";
    $result = $conn->query($sql);

    // Check if there are records
    if ($result->rowCount() > 0) {
        // Fetch data and store it in $inquiryData
        $inquiryData = $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $inquiryData = []; // Set an empty array if no records found
    }

    // SQL query to retrieve replies from 'inquiries_reply' table
    $replySql = "SELECT DISTINCT reply FROM inquiries_reply";
    $replyResult = $conn->query($replySql);

    // Check if there are replies
    if ($replyResult->rowCount() > 0) {
        // Fetch data and store it in $replies
        $replies = $replyResult->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $replies = []; // Set an empty array if no replies found
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedReply = $_POST['reply'];
        $selectedCell = $_POST['feedback_id']; // Assuming inquiry_id is the identifier

        // Debugging: Output the received values
        echo "Selected Reply: " . $selectedReply . "<br>";
        echo "Selected Cell: " . $selectedCell . "<br>";

        // Perform the update query
        try {
            $updateSql = "UPDATE feedbacks SET reply = :reply WHERE feedback_id = :cell";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':reply', $selectedReply);
            $updateStmt->bindParam(':cell', $selectedCell);
            $updateStmt->execute();

            echo "Reply updated successfully!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} finally {
    // Close the database connection
    $conn = null;
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
    .admin-container {
        display: flex;
        gap: 20px;
        padding: 20px;
    }

    table {
        border-collapse: collapse;
        width: 70%;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border: 2px solid #FFC451;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border: 1px solid #FFC451; /* Border for each cell */
    }

    th {
        background-color: #FFC451;
        color: white;
    }

    .admin-form {
        width: 30%;
        padding: 20px;
        border-radius: 10px;
        background-color: #fff;
        border: 2px solid #FFC451; /* Updated border color */
    }

    select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
    }

    textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
    }

    button {
        background-color: #FFC451;
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
  </style>
</head>

<body>

  <?php
    include 'admin_header.php';
  ?>

  

  <main id="main">

    <section>
        <h2 style="text-align: center; margin-top: 50px; margin-bottom: 50px;">FEEDBACKS</h2>
        <div class="admin-container">
            <table>
                <thead>
                    <tr>
                        <th>Feedback ID</th>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Feedbacks</th>
                        <th>Replies</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiryData as $inquiryRow) : ?>
                        <tr>
                            <td><?php echo $inquiryRow['feedback_id']; ?></td>
                            <td><?php echo $inquiryRow['cust_id']; ?></td>
                            <td><?php echo $inquiryRow['name']; ?></td>
                            <td><?php echo $inquiryRow['email']; ?></td>
                            <td><?php echo $inquiryRow['feedback']; ?></td>
                            <td><?php echo $inquiryRow['reply']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="admin-form">
                <form>
                    <label for="inquiryId">Inquiry ID:</label>
                    <input type="text" id="inquiryId" name="inquiryId" required>

                    <label for="replies">Select a Reply:</label>
                    <select id="replies" name="replies">
                        <option value="" selected disabled>Select</option>
                        <?php foreach ($replies as $reply) : ?>
                            <option value="<?php echo $reply['reply']; ?>"><?php echo $reply['reply']; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <div class="custom-reply">
                        <label for="customReply">Custom Reply:</label>
                        <textarea id="customReply" name="customReply" rows="4"></textarea>
                    </div>

                    <button type="button" onclick="addReply(this)">Add Reply</button>
                </form>
            </div>

        </div>
    </section>

  </main><!-- End #main -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get all the table cells
            var cells = document.querySelectorAll('.admin-container table tbody tr td');

            // Add click event listener to each cell
            cells.forEach(function (cell) {
                cell.addEventListener('click', function () {
                    // Get the Inquiry ID value from the clicked row
                    var inquiryId = cell.parentNode.querySelector('td:first-child').textContent;

                    // Update the input field with the selected Inquiry ID
                    document.getElementById('inquiryId').value = inquiryId;
                });
            });
        });

        function addReply() {
            // Get the selected reply and inquiry ID
            var selectedReply = "";
            var selectedInquiryId = document.getElementById('inquiryId').value;

            // Check if a reply is selected from the dropdown
            var dropdownReply = document.getElementById('replies').value;
            var customReply = document.getElementById('customReply').value;

            if (dropdownReply !== "") {
                selectedReply = dropdownReply;
            } else if (customReply !== "") {
                selectedReply = customReply;
            } else {
                // Display a message if neither dropdown nor custom reply is selected
                alert("Please select a reply or enter a custom reply before adding.");
                return;
            }

            // Prepare the data for the POST request
            var data = new URLSearchParams();
            data.append('reply', selectedReply);
            data.append('feedback_id', selectedInquiryId);

            // Make the fetch request
            fetch('admin_view_feedbacks.php', {
                method: 'POST',
                body: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(responseText => {
                // Display a success message
                alert("Reply added successfully!");

                // Reload the page to refresh the table
                location.reload();
            })
            .catch(error => {
                // Log and display an error message
                console.error('Error adding reply:', error);
                alert("Error adding reply. Please check the console for details and try again.");
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