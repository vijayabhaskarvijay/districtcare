<!DOCTYPE html>
<html>

<head>
    <title>Problem Report Confirmation</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 50px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .container p {
            margin-bottom: 15px;
        }

        .container strong {
            color: #4CAF50;
        }

        .container a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .container {
                max-width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Problem Report Confirmation</h2>

        <?php
        session_start();

        // Check if the problem ID is set in the session
        if (isset($_SESSION['prob_id'])) {
            $probId = $_SESSION['prob_id'];
            echo "<p>Your problem report has been submitted successfully.</p>";
            echo "<p>Problem ID: <strong>$probId</strong></p>";
            echo "<p>Thank you for reporting the problem. Our team will review it soon.</p>";

            // Clear the problem ID from the session
            unset($_SESSION['prob_id']);
        } else {
            echo "<p>No problem report found. Please go back and try again.</p>";
        }
        ?>

        <a href="public_user_landing.php">Go Back to Home Page</a>
    </div>
</body>

</html>