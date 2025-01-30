<?php
// Include the database connection
include 'database/db_connect.php'; 

// Get today's date and the date for reminders (e.g., 3 days from now)
$today = new DateTime('2025-01-30'); // Set to Thursday, January 30, 2025
$reminderDate = new DateTime('2025-01-30');
$reminderDate->modify('+3 days');

$sql = "
    SELECT a.agent_id, a.agent_fname, a.agent_lname, a.agent_contact, o.due_date, o.payment_status
    FROM agents a
    LEFT JOIN orders o ON a.agent_user = o.username
    WHERE o.due_date BETWEEN :today AND :reminderDate
    AND o.payment_status = 'Unpaid'
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['today' => $today->format('Y-m-d'), 'reminderDate' => $reminderDate->format('Y-m-d')]);

$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SMS Gateway API credentials
$smsApiUrl = 'rp43dy.api.infobip.com'; // Ensure the correct URL
$smsApiKey = 'ede6484fcad532204d18a4fd2cfa82f4-811448ca-33ce-452c-bb07-9ae8e68ebe46';

// Handle SMS sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_sms'])) {
    $agentId = $_POST['agent_id'];
    $agent = array_filter($agents, fn($a) => $a['agent_id'] == $agentId);
    if ($agent) {
        $agent = reset($agent);
        $agentName = htmlspecialchars($agent['agent_fname'] . ' ' . $agent['agent_lname']);
        $dueDate = htmlspecialchars($agent['due_date']);
        $contactNumber = htmlspecialchars($agent['agent_contact']);

        // Prepare the SMS message
        $message = "Hello $agentName, this is a reminder that you have an upcoming due date on $dueDate. Please ensure to settle your bills on time.";

        // Send SMS using the SMS gateway API
        $data = [
            'to' => $contactNumber,
            'message' => $message,
            'api_key' => $smsApiKey
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($smsApiUrl, false, $context);

        if ($result === FALSE) {
            echo "Error sending SMS to $contactNumber\n";
        } else {
            // Optionally decode the response and check for success
            $response = json_decode($result, true);
            if (isset($response['success']) && $response['success']) {
                echo "SMS sent to $contactNumber\n";
            } else {
                echo "Failed to send SMS to $contactNumber: " . htmlspecialchars($response['error'] ?? 'Unknown error') . "\n";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Reminders</title>
    <link href="assets/img/logo/2.png" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css"> 
    <link href="assets/vendor /remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-top: 80px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
    <h1>Agents with Upcoming Bills</h1>
    <table>
        <thead>
            <tr>
                <th>Agent ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Contact</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($agents as $agent): ?>
                <tr>
                    <td><?php echo htmlspecialchars($agent['agent_id']); ?></td>
                    <td><?php echo htmlspecialchars($agent['agent_fname']); ?></td>
                    <td><?php echo htmlspecialchars($agent['agent_lname']); ?></td>
                    <td><?php echo htmlspecialchars($agent['agent_contact']); ?></td>
                    <td><?php echo htmlspecialchars($agent['due_date']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="agent_id" value="<?php echo htmlspecialchars($agent['agent_id']); ?>">
                            <button type="submit" name="send_sms">Send Reminder</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>