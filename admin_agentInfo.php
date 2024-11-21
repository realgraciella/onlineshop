<?php
// Include configuration for PDO database connection
include 'database/db_connect.php';

// Initialize agent_id for search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the query based on the search
$query = "SELECT * FROM agents WHERE agent_fname LIKE :search OR agent_lname LIKE :search OR agent_id LIKE :search OR agent_user LIKE :search";
$stmt = $pdo->prepare($query);

// Bind the search parameter
$searchTerm = '%' . $search . '%';
$stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);

try {
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Page - Profile Details</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

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
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }
            .form-control {
                font-size: 14px;
            }
            .btn-primary {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <?php include 'admin_header.php'; ?>

    <main id="main">
        <h2>PROFILE DETAILS</h2>

        <!-- Search Bar -->
        <div class="search-bar">
            <form method="get" action="#">
                <input type="text" name="search" placeholder="Search by First Name, Last Name, Agent ID, or Username" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <form action="#" method="post">
            <section>
                <?php if ($agents): ?>
                    <table class="table">
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
                        </tr>
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
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </section>
        </form>
    </main>

    <!-- Include your JS files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
