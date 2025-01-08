<!DOCTYPE html>
<html lang="en">
<head>
<link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries</title>
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

        .inquiry-box {
            width: 50%;
            max-width: 600px;
            padding: 20px;
            border: 2px solid #FFDEAD;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .inquiry-box h1 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #343a40;
        }

        .inquiry-box form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .form-group textarea {
            resize: none;
            height: 100px;
        }

        .submit-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php include 'customer_header.php'; ?>
    <div class="inquiry-box">
        <h1>Submit Your Inquiry</h1>
        <form id="inquiryForm">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name">
                <div class="error" id="nameError"></div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email">
                <div class="error" id="emailError"></div>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Enter your message"></textarea>
                <div class="error" id="messageError"></div>
            </div>
            <button type="submit" class="submit-btn">Send Inquiry</button>
        </form>
    </div>

    <script>
        document.getElementById('inquiryForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent form submission for validation
            
            let isValid = true;

            // Clear previous error messages
            document.getElementById('nameError').textContent = '';
            document.getElementById('emailError').textContent = '';
            document.getElementById('messageError').textContent = '';

            // Validate name
            const name = document.getElementById('name').value.trim();
            if (name === '') {
                document.getElementById('nameError').textContent = 'Name is required.';
                isValid = false;
            }

            // Validate email
            const email = document.getElementById('email').value.trim();
            const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            if (email === '') {
                document.getElementById('emailError').textContent = 'Email is required.';
                isValid = false;
            } else if (!emailPattern.test(email)) {
                document.getElementById('emailError').textContent = 'Enter a valid email.';
                isValid = false;
            }

            // Validate message
            const message = document.getElementById('message').value.trim();
            if (message === '') {
                document.getElementById('messageError').textContent = 'Message is required.';
                isValid = false;
            }

            // If valid, simulate submission
            if (isValid) {
                alert('Your inquiry has been submitted. Thank you!');
                document.getElementById('inquiryForm').reset();
            }
        });
    </script>
</body>
</html>