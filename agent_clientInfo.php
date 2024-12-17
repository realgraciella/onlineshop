<?php
// Include configuration for PDO database connection
include 'database/db_connect.php';

// Initialize search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Initialize $clients as an empty array to prevent undefined variable warning
$clients = [];

try {
    // Prepare the query based on the search
    if ($search === '') {
        // If the search is empty, get all clients
        $query = "SELECT * FROM clients";
    } else {
        // Otherwise, search with the provided term
        $query = "SELECT * FROM clients WHERE client_fname LIKE :search OR client_lname LIKE :search OR client_id LIKE :search OR client_user LIKE :search";
    }

    $stmt = $pdo->prepare($query);

    // Bind the search parameter if needed
    if ($search !== '') {
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    }

    // Execute the query
    $stmt->execute();

    // Fetch all results
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($clients)) {
        echo "<p>No client found with the provided search term.</p>";
    }

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_POST['client_id'];
    $client_fname = $_POST['client_fname'];
    $client_mname = $_POST['client_mname'];
    $client_lname = $_POST['client_lname'];
    $client_sex = $_POST['client_sex'];
    $client_birthdate = $_POST['client_birthdate'];
    $client_age = $_POST['client_age'];
    $client_address = $_POST['client_address'];
    $client_contact = $_POST['client_contact'];
    $client_email = $_POST['client_email'];

    // Update the client's details in the database
    $query = "UPDATE clients SET 
                client_fname = :client_fname, 
                client_mname = :client_mname, 
                client_lname = :client_lname, 
                client_sex = :client_sex,
                client_age = :client_age,
                client_birthdate = :client_birthdate, 
                client_address = :client_address, 
                client_contact = :client_contact,
                client_email = :client_email 
              WHERE client_id = :client_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $stmt->bindParam(':client_fname', $client_fname, PDO::PARAM_STR);
    $stmt->bindParam(':client_mname', $client_mname, PDO::PARAM_STR); 
    $stmt->bindParam(':client_lname', $client_lname, PDO::PARAM_STR);
    $stmt->bindParam(':client_sex', $client_sex, PDO::PARAM_STR);
    $stmt->bindParam(':client_age', $client_age, PDO::PARAM_INT); 
    $stmt->bindParam(':client_birthdate', $client_birthdate, PDO::PARAM_STR);
    $stmt->bindParam(':client_address', $client_address, PDO::PARAM_STR);
    $stmt->bindParam(':client_contact', $client_contact, PDO::PARAM_STR);
    $stmt->bindParam(':client_email', $client_email, PDO::PARAM_STR);

    session_start(); // Start the session

    if ($stmt->execute()) {
        // Set a success message in the session
        $_SESSION['update_success'] = "Client $client_fname information successfully updated!";
        header("Location: admin_clientInfo.php"); 
        exit();
    } else {
        // Set an error message in the session
        $_SESSION['update_error'] = "Error updating client.";
        header("Location: admin_clientInfo.php"); 
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Page - Client Details</title>

    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway: 300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        h2 {
            color: #333;
            font-size: 2rem;
            margin-top: 65px;
            margin-bottom: 20px;
            text-align: center;
        }

        .search-bar {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .search-bar input {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 300px;
        }

        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            border: 1px solid;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #008a00;
            color: white;
        }

        .table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        .table th {
            background-color: #46923c;
            color: white;
            font-weight: bold;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .edit-button {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .edit-button:hover {
            background-color: #008a00;
            color: #fff;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal h3 {
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.5rem;
            color: #333;
        }

        .modal label {
            font-weight: bold;
            margin-top: 10px;
        }

        .modal input, .modal select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .modal button:hover {
            background-color: #45a049;
        }

        .smodal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0, 0, 0); /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
        }

        .smodal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 60%; /* Could be more or less, depending on screen size */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include 'agent_header.php'; ?>

    <main id="main">
        <h2>CLIENT DETAILS</h2>

        <!-- Check and display success or error message if set -->
        <?php if (isset($_SESSION['update_success'])): ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['update_success']; ?>
            </div>
            <?php unset($_SESSION['update_success']); // Clear the message after displaying ?>
        <?php elseif (isset($_SESSION['update_error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= $_SESSION['update_error']; ?>
            </div>
            <?php unset($_SESSION['update_error']); // Clear the message after displaying ?>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="search-bar">
            <form method="get" action="#">
                <input type="text" name="search" placeholder="Search by First Name, Last Name, Client ID" value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <form action="#" method="post">
            <section>
                <?php if ($clients): ?>
                    <table class="table">
                        <tr>
                            <th>Client ID</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Birthday</th>
                            <th>Address</th>
                            <th>Mobile Number</th>
                            <th>Email Address</th>
                            <th>Edit</th>
                        </tr>
                        <?php foreach ($clients as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['client_id']); ?></td>
                            <td><?= htmlspecialchars($row['client_fname']); ?></td>
                            <td><?= htmlspecialchars($row['client_mname']); ?></td>
                            <td><?= htmlspecialchars($row['client_lname']); ?></td>
                            <td><?= htmlspecialchars($row['client_sex']); ?></td>
                            <td><?= htmlspecialchars($row['client_age']); ?></td>
                            <td><?= htmlspecialchars($row['client_birthdate']); ?></td>
                            <td><?= htmlspecialchars($row['client_address']); ?></td>
                            <td><?= htmlspecialchars($row['client_contact']); ?></td>
                            <td><?= htmlspecialchars($row['client_email']); ?></td>
                            <td>
                                <a href="javascript:void(0);" class="edit-button" onclick="openEditModal(<?= htmlspecialchars($row['client_id']); ?>)">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </section>
        </form>
    </main>

    <!-- Edit Modal -->
    <div ```html
    id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Edit Client Information</h3>
            <form id="editClientForm" method="post" action="edit_client_action.php">
                <input type="hidden" name="client_id" id="editClientId">

                <label for="editFname">First Name:</label>
                <input type="text" id="editFname" name="client_fname" placeholder="Enter First Name" required>

                <label for="editMname">Middle Name:</label>
                <input type="text" id="editMname" name="client_mname" placeholder="Enter Middle Name">

                <label for="editLname">Last Name:</label>
                <input type="text" id="editLname" name="client_lname" placeholder="Enter Last Name" required>

                <label for="editSex">Sex:</label>
                <select id="editSex" name="client_sex">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <label for="editAge">Age:</label>
                <input type="number" id="editAge" name="client_age" placeholder="Enter Age" required>

                <label for="editBirthdate">Birthday:</label>
                <input type="date" id="editBirthdate" name="client_birthdate" required>

                <label for="editAddress">Address:</label>
                <input type="text" id="editAddress" name="client_address" placeholder="Enter Address" required>

                <label for="editContact">Contact Number:</label>
                <input type="text" id="editContact" name="client_contact" placeholder="Enter Contact Number" required>

                <label for="editEmail">Email Address:</label>
                <input type="email" id="editEmail" name="client_email" placeholder="Enter Email Address" required>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // Function to open the edit modal and fetch client data
        function openEditModal(clientId) {
            // Create an AJAX request to fetch client data based on clientId
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_client_data.php?client_id=' + clientId, true);

            // When the response is received, populate the form fields
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var clientData = JSON.parse(xhr.responseText);
                    
                    // Populate the modal form fields with the fetched data
                    document.getElementById('editClientId').value = clientData.client_id;
                    document.getElementById('editFname').value = clientData.client_fname;
                    document.getElementById('editMname').value = clientData.client_mname;
                    document.getElementById('editLname').value = clientData.client_lname;
                    document.getElementById('editSex').value = clientData.client_sex;
                    document.getElementById('editAge').value = clientData.client_age;
                    document.getElementById('editBirthdate').value = clientData.client_birthdate;
                    document.getElementById('editAddress').value = clientData.client_address;
                    document.getElementById('editContact').value = clientData.client_contact;
                    document.getElementById('editEmail').value = clientData.client_email;

                    // Show the edit modal
                    document.getElementById('editModal').style.display = 'block';
                } else {
                    alert('Failed to fetch client details.');
                }
            };

            xhr.send();
        }

        // Function to close the edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</body>

</html>