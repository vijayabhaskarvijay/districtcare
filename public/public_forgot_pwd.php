<?php
session_start();
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "urbanlink";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $location = $_POST['location'];
    $mpin = $_POST['mpin'];
    $_SESSION['reset_location'] = $location;
    $_SESSION['reset_email'] = $email;

    // Check the location and validate user details
    $table = ($location === 'gobi') ? 'gobi_users' : 'sathy_users';
    $userColumn = ($location === 'gobi') ? 'gobi_user_name' : 'sathy_user_name';
    $emailColumn = ($location === 'gobi') ? 'gobi_user_email' : 'sathy_user_email';
    $phoneColumn = ($location === 'gobi') ? 'gobi_user_phone_number' : 'sathy_user_phone_number';
    $dobColumn = ($location === 'gobi') ? 'gobi_user_dob' : 'sathy_user_dob';
    $mpinColumn = ($location === 'gobi') ? 'gobi_user_mpin' : 'sathy_user_mpin';

    try {
        $stmt = $conn->prepare("SELECT * FROM $table WHERE $userColumn = :username AND $emailColumn = :email AND $phoneColumn = :phone AND $dobColumn = :dob AND $mpinColumn = :mpin");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':mpin', $mpin);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $userId = $row[($location === 'gobi') ? 'gobi_user_id' : 'sathy_user_id'];
            $_SESSION['user_id'] = $userId;
            $successMessage = "User found. Please reset your password.";
            echo "<script>
                    setTimeout(function() {
                                window.location.href='public_reset_pwd.php';
                                }, 3000);
                </script>";
        } else {
            $errorMessage = "User not found. Please check your details.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

<!-- HTML Form for Password Reset -->
<!DOCTYPE html>
<html>

<head>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
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

        input[type="text"],
        input[type="email"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        select {
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
    <h1>Password Reset Authentication Page</h1>
    <?php if (isset($successMessage)) echo "<p>$successMessage</p>"; ?>
    <?php if (isset($errorMessage)) echo "<p>$errorMessage</p>"; ?>
    <div class="container">
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br><br>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required oninput="validatePhoneNumber(this)" pattern="[0-9]{10}"><br><br>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required><br><br>
            </div>
            <div class="form-group">
                <label for="mpin">MPIN:</label>
                <input type="text" id="mpin" maxlength="6" name="mpin" required><br><br>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <select id="location" name="location" required>
                    <option value="gobi">Gobichettipalayam</option>
                    <option value="sathy">Sathyamangalam</option>
                </select><br><br>
            </div>
            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>

    <script>
        function validatePhoneNumber(input) {
            // Remove non-numeric characters
            input.value = input.value.replace(/\D/g, '');

            // Limit the length to 10 characters
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }
    </script>
</body>

</html>