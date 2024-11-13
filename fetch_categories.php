<?php
// Include PDO database connection
include 'database/db_connect.php';

if (isset($_GET['brand_id'])) {
    $brand_id = intval($_GET['brand_id']); // Convert to integer for security

    // Query to fetch categories based on the selected brand_id
    $stmt = $pdo->prepare("SELECT category_id, category_name FROM categories WHERE brand_id = :brand_id ORDER BY category_name ASC");
    $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
    $stmt->execute();

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate the options for the category dropdown
    if ($categories) {
        foreach ($categories as $category) {
            echo "<option value='" . htmlspecialchars($category['category_id']) . "'>" . htmlspecialchars($category['category_name']) . "</option>";
        }
    } else {
        echo "<option value=''>No categories available</option>";
    }
}
?>
