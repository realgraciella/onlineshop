<?php
include 'database/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Page</title>

    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/admin.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            background-color: #f5f5f5;
        }

        main {
            margin-top: 80px;
        }

        .agent-list-form {
            width: 90%;
            max-width: 900px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .agent-list-form h2 {
            color: #333;
            text-align: center;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .agent-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .agent-row:last-child {
            border-bottom: none;
        }

        .agent-info {
            flex-grow: 2;
        }

        .agent-actions {
            display: flex;
            gap: 10px;
        }

        .agent-actions button {
            border: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #5bc0de;
        }

        .btn-delete {
            background-color: #d9534f;
        }

        .agent-status {
            color: #FFC451;
            font-weight: bold;
            margin-left: 20px;
        }
    </style>
</head>

<body>

<?php include 'admin_header.php'; ?>

<main id="main">
    <div class="agent-list-form">
        <h2>Agent List</h2>

        <?php
        $sql = "SELECT agent_id, agent_fname, agent_lname, agent_status FROM agents";
        $result = $pdo->query($sql);

        if ($result) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $agent_id = htmlspecialchars($row["agent_id"]);
                $agent_name = htmlspecialchars($row["agent_fname"] . " " . $row["agent_lname"]);
                $agent_status = htmlspecialchars($row["agent_status"]);
                ?>

                <div class="agent-row">
                    <div class="agent-info">
                        <strong><?php echo $agent_id; ?>.</strong> <?php echo $agent_name; ?>
                        <span class="agent-status">(<?php echo $agent_status; ?>)</span>
                    </div>
                    <div class="agent-actions">
                        <button class="btn-edit" onclick="promptForPin('edit', '<?php echo $agent_id; ?>')">Edit</button>
                        <button class="btn-delete" onclick="promptForPin('delete', '<?php echo $agent_id; ?>')">Delete</button>
                    </div>
                </div>

                <?php
            }
        } else {
            echo "<p style='text-align: center; color: red;'>Error fetching agent data.</p>";
        }

        $conn = null;
        ?>
    </div>
</main>

<!-- <script>
    function promptForPin(action, agentId) {
        const pin = prompt("Please enter the admin PIN to continue:");
        if (pin) {
            window.location.href = `admin_${action}_agent.php?id=${agentId}&pin=${pin}`;
        }
    }
</script> -->

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
