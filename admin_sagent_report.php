<?php
include 'database/db_connect.php';

// Fetch sales agent performance with username
$query = "
    SELECT 
        s.username,
        SUM(s.sale_amount) AS total_sales,
        SUM(s.quantity) AS total_quantity,
        COUNT(DISTINCT s.sale_id) AS total_transactions,
        p.product_name,
        SUM(s.quantity) AS total_quantity_sold
    FROM sales s
    JOIN products p ON s.product_id = p.product_id
    WHERE s.agent_id LIKE 'AGT-%'
    GROUP BY s.agent_id, p.product_name
    ORDER BY s.agent_id
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Agent Performance</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <script defer src="assets/js/admin.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.11/jspdf.plugin.autotable.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            margin-top: 80px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .searchBar{
            width: 200px;
        }

        .download-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
    <h1>Sales Agent Performance</h1>

    <!-- Search Bar -->
    <input type="text" id="searchBar" placeholder="Search by Username" onkeyup="filterTable()">

    <!-- Download Button -->
    <button class="btn btn-primary download-btn" onclick="downloadPDF()">Download PDF</button>

    <table id="salesTable" border="1">
        <thead>
            <tr>
                <th>Username</th>
                <th>Total Sales</th>
                <th>Total Quantity Sold</th>
                <th>Total Transactions</th>
                <th>Most Purchased Product</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $performance = [];
            foreach ($sales_data as $row) {
                $agent_id = $row['agent_id'];
                if (!isset($performance[$agent_id])) {
                    $performance[$agent_id] = [
                        'username' => $row['username'],
                        'total_sales' => 0,
                        'total_quantity' => 0,
                        'total_transactions' => 0,
                        'most_purchased_product' => $row['product_name'],
                        'most_purchased_quantity' => 0
                    ];
                }
                $performance[$agent_id]['total_sales'] += $row['total_sales'];
                $performance[$agent_id]['total_quantity'] += $row['total_quantity'];
                $performance[$agent_id]['total_transactions'] += $row['total_transactions'];

                // Check for most purchased product
                if ($row['total_quantity_sold'] > $performance[$agent_id]['most_purchased_quantity']) {
                    $performance[$agent_id]['most_purchased_product'] = $row['product_name'];
                    $performance[$agent_id]['most_purchased_quantity'] = $row['total_quantity_sold'];
                }
            }

            foreach ($performance as $agent_id => $data) {
                echo "<tr>
                        <td>{$data['username']}</td>
                        <td>{$data['total_sales']}</td>
                        <td>{$data['total_quantity']}</td>
                        <td>{$data['total_transactions']}</td>
                        <td>{$data['most_purchased_product']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        function filterTable() {
            const input = document.getElementById('searchBar');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('salesTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0]; // Username column
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
                }
            }
        }

        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const table = document.getElementById('salesTable');
            const rows = Array.from(table.rows).map(row => Array.from(row.cells).map(cell => cell.innerText));

            doc.autoTable({
                head: [rows[0]], // Header
                body: rows.slice(1) // Data
            });

            doc.save('sales_agent_performance.pdf');
        }
    </script>
</body>
</html>