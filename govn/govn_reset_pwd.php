<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password === $confirmPassword) {
        $servername = "localhost";
        $dbUsername = "root";
        $dbPassword = "";
        $dbname = "urbanlink";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $govnId = $_SESSION['govn_id'];

            $stmt = $conn->prepare("UPDATE govn_staff_details SET govn_staff_password = :password WHERE govn_staff_id = :govnId");
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':govnId', $govnId);
            $stmt->execute();

            $successMessage = "Password updated successfully. Redirecting to login page...";
            echo "<script>
                    setTimeout(function() {
                                window.location.href='govn_login.php';
                                }, 3000);
                </script>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Passwords do not match. Please try again.";
    }
}

?>

<!-- HTML Form for Password Reset -->
<!DOCTYPE html>
<html>

<head>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOVN Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #009688;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #00796b;
        }

        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .success {
            background-color: #FFD700;
        }

        .error {
            background-color: #E32636;
            color: #fff;
        }

        h1 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <h1>GOVN Reset Password</h1>
    <?php if (isset($successMessage)) echo "<p class='message success'>$successMessage</p>"; ?>
    <?php if (isset($errorMessage)) echo "<p class='message error'>$errorMessage</p>"; ?>
    <div class="container">
        <form method="post" action="">
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required><br><br>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>
            </div>
            <div class="form-group">
                <input type="submit" value="Reset Password">
            </div>
        </form>
    </div>
</body>

</html>