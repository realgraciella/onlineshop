<?php
// Include configuration for PDO database connection
include 'database/db_connect.php';

if (isset($_GET['agent_id'])) {
    $agentId = $_GET['agent_id'];

    try {
        // Prepare the query to get the agent's details by agent_id
        $query = "SELECT * FROM agents WHERE agent_id = :agent_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':agent_id', $agentId, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch the agent data
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($agent) {
            // Return the agent data as JSON
            echo json_encode($agent);
        } else {
            echo json_encode(['error' => 'Agent not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error fetching agent details: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Agent ID is required']);
}
