<?php
// Include configuration for PDO database connection
include 'database/db_connect.php';

// Initialize agent_id for search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Initialize $agents as an empty array to prevent undefined variable warning
$agents = [];

try {
    // Prepare the query based on the search
    if ($search === '') {
        // If the search is empty, get all agents
        $query = "SELECT * FROM agents";
    } else {
        // Otherwise, search with the provided term
        $query = "SELECT * FROM agents WHERE agent_fname LIKE :search OR agent_lname LIKE :search OR agent_id LIKE :search OR agent_user LIKE :search";
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
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($agents)) {
        echo "<p>No agent found with the provided search term.</p>";
    }

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agent_id = $_POST['agent_id'];
    $agent_fname = $_POST['agent_fname'];
    $agent_mname = $_POST['agent_mname']; // Added middle name
    $agent_lname = $_POST['agent_lname'];
    $agent_sex = $_POST['agent_sex'];
    $agent_birthdate = $_POST['agent_birthdate'];
    $agent_age = $_POST['agent_age']; // Added age
    $agent_address = $_POST['agent_address'];
    $agent_contact = $_POST['agent_contact'];
    $agent_email = $_POST['agent_email'];
    $agent_status = $_POST['agent_status'];
    $credit_limit = $_POST['credit_limit']; // Added credit limit

    // Update the agent's details in the database
    $query = "UPDATE agents SET 
                agent_fname = :agent_fname, 
                agent_mname = :agent_mname, 
                agent_lname = :agent_lname, 
                agent_sex = :agent_sex,
                agent_age = :agent_age,
                agent_birthdate = :agent_birthdate, 
                agent_address = :agent_address, 
                agent_contact = :agent_contact,
                agent_email = :agent_email, 
                credit_limit = :credit_limit,  
                agent_status = :agent_status 
              WHERE agent_id = :agent_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':agent_id', $agent_id, PDO::PARAM_INT);
    $stmt->bindParam(':agent_fname', $agent_fname, PDO::PARAM_STR);
    $stmt->bindParam(':agent_mname', $agent_mname, PDO::PARAM_STR); 
    $stmt->bindParam(':agent_lname', $agent_lname, PDO::PARAM_STR);
    $stmt->bindParam(':agent_sex', $agent_sex, PDO::PARAM_STR);
    $stmt->bindParam(':agent_age', $agent_age, PDO::PARAM_INT); 
    $stmt->bindParam(':agent_birthdate', $agent_birthdate, PDO::PARAM_STR);
    $stmt->bindParam(':agent_address', $agent_address, PDO::PARAM_STR);
    $stmt->bindParam(':agent_contact', $agent_contact, PDO::PARAM_STR);
    $stmt->bindParam(':agent_email', $agent_email, PDO::PARAM_STR);
    $stmt->bindParam(':credit_limit', $credit_limit, PDO::PARAM_STR); // Bind credit limit
    $stmt->bindParam(':agent_status', $agent_status, PDO::PARAM_STR);

    session_start(); // Start the session

    if ($stmt->execute()) {
        // Set a success message in the session
        $_SESSION['update_success'] = "Agent $agent_fname information successfully updated!";
        header("Location: admin_agentInfo.php"); 
        exit();
    } else {
        // Set an error message in the session
        $_SESSION['update_error'] = "Error updating agent.";
        header("Location: admin_agentInfo.php"); 
        exit();
    }
}

?>

<!DOCTYPE html>
< lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Page - Profile Details</title>

    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
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
    <link href="assets/css/admin.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>



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
    left:  0;
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
    <?php include 'admin_header.php'; ?>

    <main id="main">
        <h2>PROFILE DETAILS</h2>

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
                <input type="text" name="search" placeholder="Search by First Name, Last Name, Agent ID, or Username" value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <form action="#" method="post">
            <section>
                <?php if ($agents): ?>
                    <table class="table" id="agentInfo">
                        <thead>
                            <tr>
                                <th>Agent ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Sex</th>
                                <th>Birthday</th>
                                <th>Address</th>
                                <th>Mobile Number</th>
                                <th>Email Address</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Credit Limit</th>
                                <th>ID Picture</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agents as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['agent_id']); ?></td>
                                <td><?= htmlspecialchars($row['agent_fname']); ?></td>
                                <td><?= htmlspecialchars($row['agent_lname']); ?></td>
                                <td><?= htmlspecialchars($row['agent_sex']); ?></td>
                                <td><?= htmlspecialchars($row['agent_birthdate']); ?></td>
                                <td><?= htmlspecialchars($row['agent_address']); ?></td>
                                <td><?= htmlspecialchars($row['agent_contact']); ?></td>
                                <td><?= htmlspecialchars($row['agent_email']); ?></td>
                                <td><?= htmlspecialchars($row['role']); ?></td>
                                <td><?= htmlspecialchars($row['agent_status']); ?></td>
                                <td><?= htmlspecialchars($row['credit_limit']); ?></td>
                                <td>
                                    <a href="javascript:void(0);" 
                                    onclick="showModal(
                                        'data:image/jpeg;base64,<?= base64_encode($row['id_front_image']); ?>',
                                        'data:image/jpeg;base64,<?= base64_encode($row['id_back_image']); ?>'
                                    )">View</a>
                                </td>

                                <td>
                                    <a href="javascript:void(0);" class="edit-button" onclick="openEditModal(<?= htmlspecialchars($row['agent_id']); ?>)">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                <?php endif; ?>
            </section>
        </form>
    </main>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalFrontImage" src="" alt="Agent ID Front Image" style="width: 200px; height: 200px; margin-bottom: 10px;">
            <img id="modalBackImage" src="" alt="Agent ID Back Image" style="width: 200px; height: 200px">
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Edit Agent Information</h3>
            <form id="editAgentForm" method="post" action="edit_agent_action.php">
                <input type="hidden" name="agent_id" id="editAgentId">

                <label for="editFname">First Name:</label>
                <input type="text" id="editFname" name="agent_fname" placeholder="Enter First Name" required>

                <label for="editLname">Last Name:</label>
                <input type="text" id="editLname" name="agent_lname" placeholder="Enter Last Name" required>

                <label for="editSex">Sex:</label>
                <select id="editSex" name="agent_sex">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <label for="editBirthdate">Birthday:</label>
                <input type="date" id="editBirthdate" name="agent_birthdate" required>

                <label for="editAddress">Address:</label>
                <input type="text" id="editAddress" name="agent_address" placeholder="Enter Address" required>

                <label for="editContact">Contact Number:</label>
                <input type="text" id="editContact" name="agent_contact" placeholder="Enter Contact Number" required>

                <label for="editEmail">Email Address:</label>
                <input type="email" id="editEmail" name="agent_email" placeholder="Enter Email Address" required>

                <label for="editCreditLimit">Credit Limit:</label>
                <input type="number" id="editCreditLimit" name="credit_limit" placeholder="Enter Credit Limit" required>

                <label for="editStatus">Status:</label>
                <select id="editStatus" name="agent_status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="smodal">
        <div class="modal-content">
            <span class="close" onclick="closeSuccessModal()">&times;</span>
            <h3 id="successMessage"></h3>
            <button onclick="closeSuccessModal()">OK</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>


    <script>

        $(document).ready(function() {
            $('#agentInfo').DataTable();
        });

        // Function to show modal for ID image
        function showModal(frontImagePath, backImagePath) {
            var modal = document.getElementById('myModal');
            var modalFrontImage = document.getElementById('modalFrontImage');
            var modalBackImage = document.getElementById('modalBackImage');
            
            modal.style.display = 'block';
            modalFrontImage.src = frontImagePath;
            modalBackImage.src = backImagePath;
        }


        // Function to close the modal
        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }

        // Function to open the edit modal and fetch agent data
        function openEditModal(agentId) {
            // Create an AJAX request to fetch agent data based on agentId
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_agent_data.php?agent_id=' + agentId, true);

            // When the response is received, populate the form fields
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var agentData = JSON.parse(xhr.responseText);
                    
                    // Populate the modal form fields with the fetched data
                    document.getElementById('editAgentId').value = agentData.agent_id;
                    document.getElementById('editFname').value = agentData.agent_fname;
                    document.getElementById('editLname').value = agentData.agent_lname;
                    document.getElementById('editSex').value = agentData.agent_sex;
                    document.getElementById('editBirthdate').value = agentData.agent_birthdate;
                    document.getElementById('editAddress').value = agentData.agent_address;
                    document.getElementById('editContact').value = agentData.agent_contact;
                    document.getElementById('editEmail').value = agentData.agent_email;
                    document.getElementById('editCreditLimit').value = agentData.credit_limit; // Populate credit limit
                    document.getElementById('editStatus').value = agentData.agent_status;

                    // Show the edit modal
                    document.getElementById('editModal').style.display = 'block';
                } else {
                    alert('Failed to fetch agent details.');
                }
            };

            xhr .send();
        }


        // Function to close the edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Function to close the alert modal
        function closeAlertModal() {
            document.getElementById('alertModal').style.display = 'none';
        }

        // Check if there is a success or error message and show the modal
        window.onload = function() {
            <?php if (isset($_SESSION['update_success'])): ?>
                document.getElementById('alertMessage').innerText = "<?= $_SESSION['update_success']; ?>";
                document.getElementById('alertModal').style.display = 'block';
                <?php unset($_SESSION['update_success']); ?>
            <?php elseif (isset($_SESSION['update_error'])): ?>
                document.getElementById('alertMessage').innerText = "<?= $_SESSION['update_error']; ?>";
                document.getElementById('alertModal').style.display = 'block';
                <?php unset($_SESSION['update_error']); ?>
            <?php endif; ?>
        }; 
    </script>
</body>

</html>