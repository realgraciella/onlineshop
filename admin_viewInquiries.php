<?php
include 'database/db_connect.php';

// Fetch client inquiries
$queryClient = "SELECT * FROM client_inquiries ORDER BY date_created DESC";
$stmtClient = $pdo->prepare($queryClient);
$stmtClient->execute();
$clientInquiries = $stmtClient->fetchAll(PDO::FETCH_ASSOC);

// Fetch agent inquiries
$queryAgent = "SELECT agent_inquiries.*, agents.agent_fname, agents.agent_lname 
               FROM agent_inquiries 
               JOIN agents ON agent_inquiries.agent_id = agents.agent_id 
               ORDER BY agent_inquiries.date_created DESC";
$stmtAgent = $pdo->prepare($queryAgent);
$stmtAgent->execute();
$agentInquiries = $stmtAgent->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inquiries</title>
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

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .inquiry-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        h2 {
            color: #007bff;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            background-color: #fafafa;
        }

        table th {
            background-color: #007bff;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #e9ecef;
        }

        table td {
            word-wrap: break-word;
        }
    </style>

</head>
<body>
    <div class="inquiry-container">
        <h1>Inquiries</h1>

        <!-- Client Inquiries Section -->
        <h2>Client Inquiries</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientInquiries as $inquiry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                        <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></td>
                        <td><?php echo htmlspecialchars($inquiry['date_created']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Agent Inquiries Section -->
        <h2>Agent Inquiries</h2>
        <table>
            <thead>
                <tr>
                    <th>Agent Name</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agentInquiries as $inquiry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inquiry['agent_fname']) . ' ' . htmlspecialchars($inquiry['agent_lname']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></td>
                        <td><?php echo htmlspecialchars($inquiry['date_created']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
