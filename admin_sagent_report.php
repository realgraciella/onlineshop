<?php
include 'database/db_connect.php'; // Ensure this file is included to establish the PDO connection

// Fetch sales agent performance with username and full name
$query = "
    SELECT 
        a.agent_fname,
        a.agent_mname,
        a.agent_lname,
        SUM(s.total_amount) AS total_sales,
        SUM(s.quantity) AS total_quantity,
        COUNT(DISTINCT s.sale_id) AS total_transactions,
        p.product_name,
        SUM(s.quantity) AS total_quantity_sold
    FROM store_sales s
    JOIN products p ON s.product_id = p.product_id
    JOIN agents a ON s.name = a.agent_user
    WHERE s.name LIKE 'AGT-%'
    GROUP BY a.agent_user, p.product_name
    ORDER BY a.agent_user
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching sales data: " . $e->getMessage();
}

// Fetch ranking of agents based on personal sales with full name
$ranking_query = "
    SELECT 
        a.agent_fname,
        a.agent_mname,
        a.agent_lname,
        SUM(total_sales) AS total_sales
    FROM (
        SELECT 
            o.username,
            SUM(o.total_amount) AS total_sales
        FROM orders o
        WHERE o.payment_status = 'completed'
        GROUP BY o.username
        
        UNION ALL
        
        SELECT 
            s.name AS username,
            SUM(s.total_amount) AS total_sales
        FROM store_sales s
        WHERE s.name LIKE 'AGT-%'
        GROUP BY s.name
    ) AS combined_sales
    JOIN agents a ON combined_sales.username = a.agent_user
    GROUP BY a.agent_user
    ORDER BY total_sales DESC
";

try {
    $ranking_stmt = $pdo->prepare($ranking_query);
    $ranking_stmt->execute();
    $ranking_data = $ranking_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching ranking data: " . $e->getMessage();
}

// Fetch agents with unsettled payments with full name
$unsettled_query = "
    SELECT 
        a.agent_fname,
        a.agent_mname,
        a.agent_lname,
        SUM(o.total_amount) AS total_due
    FROM orders o
    JOIN agents a ON o.username = a.agent_user
    WHERE o.payment_status = 'Unpaid'
    GROUP BY a.agent_user
";

try {
    $unsettled_stmt = $pdo->prepare($unsettled_query);
    $unsettled_stmt->execute();
    $unsettled_data = $unsettled_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching unsettled payments: " . $e->getMessage();
}

// Fetch agents with no overdue records for a month or no records at all with full name
$no_overdue_query = "
    SELECT 
        a.agent_fname,
        a.agent_mname,
        a.agent_lname
    FROM orders o
    JOIN agents a ON o.username = a.agent_user
    WHERE o.due_date > NOW() - INTERVAL 1 MONTH
    GROUP BY a.agent_user
    HAVING COUNT(o.order_id) = 0
";

try {
    $no_overdue_stmt = $pdo->prepare($no_overdue_query);
    $no_overdue_stmt->execute();
    $no_overdue_data = $no_overdue_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching no overdue records: " . $e->getMessage();
}

// Fetch agents with overdue records with full name
$overdue_query = "
    SELECT 
        a.agent_fname,
        a.agent_mname,
        a.agent_lname,
        COUNT(o.order_id) AS overdue_count
    FROM orders o
    JOIN agents a ON o.username = a.agent_user
    WHERE o.due_date < NOW() AND o.payment_status != 'completed'
    GROUP BY a.agent_user
";

try {
    $overdue_stmt = $pdo->prepare($overdue_query);
    $overdue_stmt->execute();
    $overdue_data = $overdue_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching overdue records: " . $e->getMessage();
}

// Fetch agents excelling in specific brand sales
$brand_sales_query = "
    SELECT 
        a.agent_fname,
        a.agent_mname,
        a.agent_lname,
        b.brand_name,
        SUM(s.total_amount) AS total_sales
    FROM store_sales s
    JOIN products p ON s.product_id = p.product_id
    JOIN brands b ON p.brand_id = b.brand_id
    JOIN agents a ON s.name = a.agent_user
    WHERE s.name LIKE 'AGT-%'
    GROUP BY a.agent_user, b.brand_id
    ORDER BY total_sales DESC
";

try {
    $brand_sales_stmt = $pdo->prepare($brand_sales_query);
    $brand_sales_stmt->execute();
    $brand_sales_data = $brand_sales_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching brand sales data: " . $e->getMessage();
}

// Fetch credit limit recommendations based on performance
$credit_limit_query = "
    SELECT 
        a.agent_fname,
        a.agent_mname,
        a.agent_lname,
        SUM(s.total_amount) AS total_sales,
        COUNT(DISTINCT o.order_id) AS total_orders,
        SUM(CASE WHEN o.payment_status = 'Unpaid' THEN o.total_amount ELSE 0 END) AS total_due,
        COUNT(CASE WHEN o.due_date < NOW() THEN 1 END) AS overdue_count
    FROM agents a
    LEFT JOIN orders o ON a.agent_user = o.username
    LEFT JOIN store_sales s ON a.agent_user = s.name
    WHERE o.due_date > NOW() - INTERVAL 1 MONTH OR o.order_id IS NULL
    GROUP BY a.agent_user
    HAVING total_due = 0 AND overdue_count = 0 AND total_sales > 1000
";

try {
    $credit_limit_stmt = $pdo->prepare($credit_limit_query);
    $credit_limit_stmt->execute();
    $credit_limit_data = $credit_limit_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching credit limit recommendations: " . $e->getMessage();
}
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
            color: #343a40;
        }

        h1 {
            margin-top: 40px;
            font-size: 2.5rem;
            color: #007bff;
            text-align: center;
        }

        h2 {
            margin-top: 30px;
            font-size: 1.75rem;
            color: #495057;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .searchBar {
            width: 250px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .sa-container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
    <div class="sa-container">
        <h1>Sales Agent Performance</h1>

        <!-- Search Bar -->
        <input type="text" id="searchBar" class="searchBar" placeholder="Search by Username" onkeyup="filterTable()">

        <table id="salesTable" border="1">
            <thead>
                <tr>
                    <th>Full Name</th>
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
                    $full_name = trim("{$row['agent_fname']} {$row['agent_mname']} {$row['agent_lname']}");
                    if (!isset($performance[$full_name])) {
                        $performance[$full_name] = [
                            'full_name' => $full_name,
                            'total_sales' => 0,
                            'total_quantity' => 0,
                            'total_transactions' => 0,
                            'most_purchased_product' => $row['product_name'],
                            'most_purchased_quantity' => 0
                        ];
                    }
                    $performance[$full_name]['total_sales'] += $row['total_sales'];
                    $performance[$full_name]['total_quantity'] += $row['total_quantity'];
                    $performance[$full_name]['total_transactions'] += $row['total_transactions'];

                    // Check for most purchased product
                    if ($row['total_quantity_sold'] > $performance[$full_name]['most_purchased_quantity']) {
                        $performance[$full_name]['most_purchased_product'] = $row['product_name'];
                        $performance[$full_name]['most_purchased_quantity'] = $row['total_quantity_sold'];
                    }
                }

                foreach ($performance as $full_name => $data) {
                    echo "<tr>
                            <td>{$data['full_name']}</td>
                            <td>{$data['total_sales']}</td>
                            <td>{$data['total_quantity']}</td>
                            <td>{$data['total_transactions']}</td>
                            <td>{$data['most_purchased_product']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Ranking of Agents -->
        <h2>Ranking of Agents Based on Personal Sales</h2>
        <table id="rankingTable" border="1">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ranking_data as $row): ?>
                    <tr>
                        <td><?= trim("{$row['agent_fname']} {$row['agent_mname']} {$row['agent_lname']}") ?></td>
                        <td><?= $row['total_sales'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Agents Excelling in Specific Brand Sales -->
        <h2>Agents Excelling in Specific Brand Sales</h2>
        <table id="brandSalesTable" border="1">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Brand Name</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brand_sales_data as $row): ?>
                    <tr>
                        <td><?= trim("{$row['agent_fname']} {$row['agent_mname']} {$row['agent_lname']}") ?></td>
                        <td><?= $row['brand_name'] ?></td>
                        <td><?= $row['total_sales'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Credit Limit Recommendations -->
        <h2>Credit Limit Recommendations</h2>
        <table id="creditLimitTable" border="1">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Total Sales</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($credit_limit_data as $row): ?>
                    <tr>
                        <td><?= trim("{$row['agent_fname']} {$row['agent_mname']} {$row['agent_lname']}") ?></td>
                        <td><?= $row['total_sales'] ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="showUpgradeModal('<?= $row['agent_fname'] ?> <?= $row['agent_mname'] ?> <?= $row['agent_lname'] ?>')">Upgrade Credit Limit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal for Credit Limit Upgrade Confirmation -->
        <div class="modal" id="upgradeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upgrade Credit Limit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to upgrade the credit limit by $500 for <span id="agentName"></span>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="approveBtn">Approve</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showUpgradeModal(agentName) {
                document.getElementById('agentName').innerText = agentName;
                $('#upgradeModal').modal('show');
            }

            document.getElementById('approveBtn').addEventListener('click', function() {
                // Logic to handle the credit limit upgrade
                // This could involve an AJAX call to update the database
                alert('Credit limit upgraded by $500 for ' + document.getElementById('agentName').innerText);
                $('#upgradeModal').modal('hide');
            });

            function filterTable() {
                const input = document.getElementById('searchBar');
                const filter = input.value.toLowerCase();
                const table = document.getElementById('salesTable');
                const tr = table.getElementsByTagName('tr');

                for (let i = 1; i < tr.length; i++) {
                    const td = tr[i].getElementsByTagName('td')[0]; // Full Name column
                    if (td) {
                        const txtValue = td.textContent || td.innerText;
                        tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
                    }
                }
            }
        </script>

        <!-- Agents with Unsettled Payments -->
        <h2>Agents with Unsettled Payments</h2>
        <table id="unsettledTable" border="1">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Total Due</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($unsettled_data as $row): ?>
                    <tr>
                        <td><?= trim("{$row['agent_fname']} {$row['agent_mname']} {$row['agent_lname']}") ?></td>
                        <td><?= $row['total_due'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Agents with No Overdue Records -->
        <h2>Agents with No Overdue Records for a Month or No Records at All</h2>
        <table id="noOverdueTable" border="1">
            <thead>
                <tr>
                    <th>Full Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($no_overdue_data as $row): ?>
                    <tr>
                        <td><?= trim("{$row['agent_fname']} {$row['agent_mname']} {$row['agent_lname']}") ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Agents with Overdue Records -->
        <h2>Agents with Overdue Records</h2>
        <table id="overdueTable" border="1">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Overdue Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($overdue_data as $row): ?>
                    <tr>
                        <td><?= trim("{$row['agent_fname']} {$row['agent_mname']} {$row['agent_lname']}") ?></td>
                        <td><?= $row['overdue_count'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>