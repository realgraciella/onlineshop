<?php
include 'database/db_connect.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agent_id = $_POST['agent_id'];
    $agent_fname = $_POST['agent_fname'];
    $agent_mname = $_POST['agent_mname']; // Added middle name
    $agent_lname = $_POST['agent_lname'];
    $agent_sex = $_POST['agent_sex'];
    $agent_birthdate = $_POST['agent_birthdate'];
    $agent_age = $_POST['agent_age']; // Added age
    $agent_address = $_POST['agent_address'];
    $agent_contact = $_POST['agent_contact'];
    $agent_email = $_POST['agent_email'];
    $agent_status = $_POST['agent_status'];
    $credit_limit = $_POST['credit_limit']; // Added credit limit

    // Update the agent's details in the database
    $query = "UPDATE agents SET 
                agent_fname = :agent_fname, 
                agent_mname = :agent_mname, 
                agent_lname = :agent_lname, 
                agent_sex = :agent_sex,
                agent_age = :agent_age,
                agent_birthdate = :agent_birthdate, 
                agent_address = :agent_address, 
                agent_contact = :agent_contact,
                agent_email = :agent_email, 
                credit_limit = :credit_limit,  
                agent_status = :agent_status 
              WHERE agent_id = :agent_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':agent_id', $agent_id, PDO::PARAM_INT);
    $stmt->bindParam(':agent_fname', $agent_fname, PDO::PARAM_STR);
    $stmt->bindParam(':agent_mname', $agent_mname, PDO::PARAM_STR); 
    $stmt->bindParam(':agent_lname', $agent_lname, PDO::PARAM_STR);
    $stmt->bindParam(':agent_sex', $agent_sex, PDO::PARAM_STR);
    $stmt->bindParam(':agent_age', $agent_age, PDO::PARAM_INT); 
    $stmt->bindParam(':agent_birthdate', $agent_birthdate, PDO::PARAM_STR);
    $stmt->bindParam(':agent_address', $agent_address, PDO::PARAM_STR);
    $stmt->bindParam(':agent_contact', $agent_contact, PDO::PARAM_STR);
    $stmt->bindParam(':agent_email', $agent_email, PDO::PARAM_STR);
    $stmt->bindParam(':credit_limit', $credit_limit, PDO::PARAM_STR); // Bind credit limit
    $stmt->bindParam(':agent_status', $agent_status, PDO::PARAM_STR);

    session_start(); // Start the session

    if ($stmt->execute()) {
        // Set a success message in the session
        $_SESSION['update_success'] = "Agent $agent_fname information successfully updated!";
        header("Location: admin_agentInfo.php"); 
        exit();
    } else {
        // Set an error message in the session
        $_SESSION['update_error'] = "Error updating agent.";
        header("Location: admin_agentInfo.php"); 
        exit();
    }
}
?>