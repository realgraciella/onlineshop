<?php
include 'database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $agent_user = $data['agent_user'];
    $product_outs = $data['product_outs'];

    // Assuming you have a way to get agent_id from agent_user
    $agent_query = "SELECT agent_id FROM agents WHERE agent_user = ?";
    $agent_stmt = $pdo->prepare($agent_query);
    $agent_stmt->execute([$agent_user]);
    $agent = $agent_stmt->fetch(PDO::FETCH_ASSOC);

    if ($agent) {
        foreach ($product_outs as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            // Insert into product_under_agents
            $insert_query = "INSERT INTO product_under_agents (agent_id, product_id, quantity) VALUES (?, ?, ?)";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->execute([$agent['agent_id'], $product_id, $quantity]);

            // Update stock level in products table
            $update_product_query = "UPDATE products SET stock_level = stock_level - ? WHERE product_id = ?";
            $update_product_stmt = $pdo->prepare($update_product_query);
            $update_product_stmt->execute([$quantity, $product_id]);

            // Update stock level for the specific variation
            $update_variation_query = "UPDATE product_variations SET stock_per_variation = stock_per_variation - ? WHERE product_id = ?";
            $update_variation_stmt = $pdo->prepare($update_variation_query);
            $update_variation_stmt->execute([$quantity, $product_id]);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Agent not found.']);
    }
}
?>