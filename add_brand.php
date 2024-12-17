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

// Handle the deletion of a brand
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteBrandId"])) {
    $deleteBrandId = $_POST["deleteBrandId"];
    
    try {
        // Delete query using PDO
        $deleteSql = "DELETE FROM brands WHERE brand_id = :brandId";
        $stmt = $pdo->prepare($deleteSql);
        $stmt->bindParam(':brandId', $deleteBrandId, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect to refresh the page after delete
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        die("Error deleting brand: " . $e->getMessage());
    }
}

// Handle the edit brand operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editBrandId"])) {
    $editBrandId = $_POST["editBrandId"];
    $editBrandName = $_POST["editBrandName"];
    
    try {
        // Update query using PDO
        $updateSql = "UPDATE brands SET brand_name = :brandName WHERE brand_id = :brandId";
        $stmt = $pdo->prepare($updateSql);
        $stmt->bindParam(':brandName', $editBrandName, PDO::PARAM_STR);
        $stmt->bindParam(':brandId', $editBrandId, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect to refresh the page after update
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        die("Error updating brand: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Page - Add Brand</title>
    <link href="assets/img/logo/2.png" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css"> 
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/admin.css" rel="stylesheet">
    
    <style>
        /* Enhanced Styling */
        .admin-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        #brandTable {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        #brandTable th{
            background-color:rgb(61, 61, 61);
            color: white;
            font-weight: 600;
            text-align: center;
        }

        #brandTable td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
            transition: background-color 0.3s;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .edit-btn:hover {
            background-color: #008a00;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        /* Modal Styles */
        .modal-content {
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
        }

        .modal-header {
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <main id="main">
        <div class="admin-container">
            <h2>ADD NEW BRAND</h2>

            <!-- Form for adding a new brand -->
            <form method="post" action="">
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
                                    <!-- Edit Button to Open Modal -->
                                    <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $brandRow['brand_id']; ?>">Edit</button>
                                    <!-- Delete Button Form -->
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="deleteBrandId" value="<?php echo $brandRow['brand_id']; ?>">
                                        <button class="delete-btn" type="submit" onclick="return confirm('Are you sure you want to delete this brand?');">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $brandRow['brand_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Brand</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post">
                                                <div class="form-group">
                                                    <input type="text" name="editBrandName" value="<?php echo $brandRow['brand_name']; ?>" required class="form-control">
                                                </div>
                                                <input type="hidden" name="editBrandId" value="<?php echo $brandRow['brand_id']; ?>">
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

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

    <!-- Bootstrap JS -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
