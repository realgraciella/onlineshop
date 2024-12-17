<?php
// Include configuration for PDO database connection
include 'database/db_connect.php';

// Check if client_id is set in the GET request
if (isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];

    try {
        // Prepare the SQL statement to fetch client data
        $query = "SELECT * FROM clients WHERE client_id = :client_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();

        // Fetch the client data
        $clientData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if client data was found
        if ($clientData) {
            // Return the client data as JSON
            echo json_encode($clientData);
        } else {
            // Return an error message if no client found
            echo json_encode(['error' => 'Client not found.']);
        }
    } catch (PDOException $e) {
        // Return an error message in case of a database error
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Return an error message if client_id is not provided
    echo json_encode(['error' => 'Client ID not provided.']);
}
?>