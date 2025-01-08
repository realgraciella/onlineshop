<!DOCTYPE html>
<html lang="en">
<head>
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
        }

        .feedback-box {
            width: 50%;
            max-width: 600px;
            padding: 20px;
            border: 2px solid #FFDEAD;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .feedback-box h1 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #343a40;
        }

        .feedback-box textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            resize: none;
            font-size: 1em;
        }

        .feedback-box button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1em;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .feedback-box button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'customer_header.php'; ?>
    <div class="feedback-box">
        <h1>Feedback</h1>
        <form id="feedbackForm">
            <textarea id="feedbackText" placeholder="Write your feedback here..."></textarea>
            <br>
            <button type="submit">Submit Feedback</button>
        </form>
        <p id="thankYouMessage" style="color: green; display: none; margin-top: 20px;">Thank you for your feedback!</p>
    </div>

    <script>
        const feedbackForm = document.getElementById('feedbackForm');
        const thankYouMessage = document.getElementById('thankYouMessage');
        const feedbackText = document.getElementById('feedbackText');

        feedbackForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent form submission

            if (feedbackText.value.trim() === "") {
                alert("Please enter your feedback before submitting.");
                return;
            }

            // Simulate a feedback submission (Replace this with backend logic)
            thankYouMessage.style.display = 'block';
            feedbackText.value = ""; // Clear the textarea
        });
    </script>
</body>
</html>