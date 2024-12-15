<?php
// Include configuration for database connection
include 'database/db_connect.php';

// Fetch data from 'brands' table using PDO
try {
    $query = "SELECT * FROM brands";
    $stmt = $pdo->query($query);

    // Fetch data as an associative array
    $brandData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

// Check if form is submitted to add a new brand
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addBrand"])) {
    $brandName = trim($_POST["brandName"]);
    
    if (!empty($brandName)) {
        try {
            // Prepare and execute the insert query using PDO
            $insertQuery = "INSERT INTO brands (brand_name) VALUES (:brandName)";
            $stmt = $pdo->prepare($insertQuery);
            $stmt->bindParam(':brandName', $brandName, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Redirect to the same page to avoid form resubmission and show the updated list
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Error: Could not add the brand.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Page - Add Brand</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600|Raleway:300,400,500|Poppins:300,400,500" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/admin.css" rel="stylesheet">

    <style>
        .admin-container {
            max-width: 700px;
            margin: 65px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .admin-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        #brandForm {
            padding: 15px 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(255, 196, 81, 0.4);
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: black;
            border: 1px #008a00 solid;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #008a00;
            color: #fff;
        }

        #brandTable {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        #brandTable th,
        #brandTable td {
            text-align: center;
            padding: 12px;
            border: 1px solid #ccc;
        }

        #brandTable th {
            background-color:rgb(61, 61, 61);
            color: white;
            font-weight: 600;
        }

        .edit-btn,
        .delete-btn {
            padding: 5px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: #fff;
        }

        .edit-btn:hover {
            background-color: #008a00;
        }

        .delete-btn {
            background-color: #dc3545;
            color: #fff;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        @media (max-width: 600px) {
            #brandForm,
            #brandTable {
                font-size: 14px;
            }
            input[type="text"] {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <?php include 'admin_header.php'; ?>

    <main id="main">
        <div class="admin-container">
            <h2>ADD NEW BRAND</h2>

            <!-- Form for adding a new brand -->
            <form id="brandForm" method="post" action="">
                <div class="form-group">
                    <label for="brandName">Brand Name:</label>
                    <input type="text" id="brandName" name="brandName" placeholder="Enter brand name" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="addBrand">Save</button>
                </div>
            </form>

            <!-- Table displaying brand data -->
            <table id="brandTable">
                <thead>
                    <tr>
                        <th>Brand Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($brandData)) : ?>
                        <?php foreach ($brandData as $brandRow) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($brandRow['brand_name']); ?></td>
                                <td>
                                    <button class="edit-btn">Edit</button>
                                    <button class="delete-btn">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="2">No brands found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
