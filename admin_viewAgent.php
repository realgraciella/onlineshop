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
            color:rgb(11, 134, 38);
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
    <button class="btn btn-primary" onclick="printAgentList()">Print Agent List</button>
    <button class="btn btn-success" id="downloadBtn">Download Agent List as PDF</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Contact</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Updated SQL query to join agents and users tables
            $sql = "SELECT agent_id, agent_user, agent_fname, agent_mname, agent_lname, agent_contact, agent_status 
                    FROM agents";

            $result = $pdo->query($sql);
            if ($result) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $agent_id = htmlspecialchars($row["agent_id"]);
                    $username = htmlspecialchars($row["agent_user"]);
                    $agent_name = htmlspecialchars($row["agent_fname"] . " " . $row["agent_mname"] . " " . $row["agent_lname"]);
                    $agent_contact = htmlspecialchars($row["agent_contact"]);
                    $agent_status = htmlspecialchars($row["agent_status"]);
                    ?>

                    <tr>
                        <td><?php echo $agent_id; ?></td>
                        <td><?php echo $agent_name; ?></td>
                        <td><?php echo $username; ?></td>
                        <td><?php echo $agent_contact; ?></td>
                        <td><span class="agent-status"><?php echo $agent_status; ?></span></td>
                        <!-- Removed the actions column -->
                    </tr>

                    <?php
                }
            } else {
                echo "<tr><td colspan='5' style='text-align: center; color: red;'>No agents found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function printAgentList() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print Agent List</title>');
        printWindow.document.write('<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">');
        printWindow.document.write('<link rel="stylesheet" href="assets/css/admin.css">');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h2>Agent List</h2>');
        
        var agentListContent = document.querySelector('.agent-list-form').innerHTML;
        printWindow.document.write(agentListContent);
        
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        
        printWindow.onload = function() {
            printWindow.print();
            printWindow.close();
        };
    }

    document.getElementById('downloadBtn').addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const logoUrl = 'assets/img/logo/4.2.png'; // Update with the correct path to your logo image

    const img = new Image();
    img.src = logoUrl;

    img.onload = function () {
        const pageWidth = doc.internal.pageSize.width;
        const imgWidth = 50;
        const imgHeight = (img.height / img.width) * imgWidth;
        const imgX = (pageWidth - imgWidth) / 2;
        const imgY = 10;

        // Add logo and title
        doc.addImage(img, 'PNG', imgX, imgY, imgWidth, imgHeight);
        doc.setFontSize(16);
        doc.text("Agent List", pageWidth / 2, imgY + imgHeight + 10, { align: 'center' });

        // Get table data
        const tableHeaders = ["ID", "Name", "Username", "Contact", "Status"];
        const rows = [];

        document.querySelectorAll('table tbody tr').forEach(row => {
            const rowData = [];
            row.querySelectorAll('td').forEach(cell => {
                rowData.push(cell.innerText);
            });
            rows.push(rowData);
        });

        // Add table to PDF
        doc.autoTable({
            head: [tableHeaders],
            body: rows,
            startY: imgY + imgHeight + 20, // Adjust start position below the logo and title
            theme: 'grid',
            headStyles: { fillColor: [22, 160, 133] }, // Customize header color
        });

        doc.save('agent_list.pdf');
    };
});


</script>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>


</body>
</html>