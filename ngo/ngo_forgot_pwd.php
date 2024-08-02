<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $userPhone = $_POST['user_phone'];
    $userEmail = $_POST['user_email'];
    $orgName = $_POST['org_name'];
    $orgPlace = $_POST['org_place'];
    $orgPhone = $_POST['org_phone'];
    $orgEmail = $_POST['org_email'];
    $mpin = $_POST['mpin'];

    // Verify the details in the database
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "urbanlink";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM ngo_details WHERE ngo_user_name = :username AND ngo_user_phone = :userPhone AND ngo_user_email = :userEmail AND ngo_org_name = :orgName AND ngo_org_place = :orgPlace AND ngo_org_phone = :orgPhone AND ngo_org_mail = :orgEmail AND ngo_user_mpin = :mpin");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':userPhone', $userPhone);
        $stmt->bindParam(':userEmail', $userEmail);
        $stmt->bindParam(':orgName', $orgName);
        $stmt->bindParam(':orgPlace', $orgPlace);
        $stmt->bindParam(':orgPhone', $orgPhone);
        $stmt->bindParam(':orgEmail', $orgEmail);
        $stmt->bindParam(':mpin', $mpin);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $ngoId = $row['ngo_id'];
            $_SESSION['ngo_id'] = $ngoId;
            $successMessage = "User found. Redirecting to password reset page...";
            echo "<script>
                    setTimeout(function() {
                                window.location.href='ngo_reset_pwd.php';
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

<!DOCTYPE html>
<html>

<head>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Password Reset</title>
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

        input[type="text"],
        input[type="email"],
        input[type="password"] {
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
    <h1>NGO Password Reset</h1>
    <?php if (isset($successMessage)) echo "<p class='message success'>$successMessage</p>"; ?>
    <?php if (isset($errorMessage)) echo "<p class='message error'>$errorMessage</p>"; ?>
    <div class="container">
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" autocomplete="off" required><br><br>
            </div>
            <div class="form-group">
                <label for="user_phone">User Phone:</label>
                <input type="text" id="user_phone" name="user_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" autocomplete="off" required><br><br>
            </div>
            <div class="form-group">
                <label for="user_email">User Email:</label>
                <input type="email" id="user_email" name="user_email" autocomplete="off" required><br><br>
            </div>
            <div class="form-group">
                <label for="org_name">Organization Name:</label>
                <input type="text" id="org_name" name="org_name" autocomplete="off" required><br><br>
            </div>
            <div class="form-group">
                <label for="org_place">Organization Place:</label>
                <select id="org_place" name="org_place" required>
                    <option value="Select">-- Select Option --</option>
                    <option value="Gobichettipalayam">Gobichettipalayam</option>
                    <option value="Sathyamangalam">Sathyamangalam</option>
                </select><br><br>
            </div>

            <div class="form-group">
                <label for="mpin">MPIN:</label>
                <input type="text" id="mpin" maxlength="6" name="mpin" autocomplete="off" required><br><br>
            </div>

            <div class="form-group">
                <label for="org_phone">Organization Phone:</label>
                <input type="text" id="org_phone" name="org_phone" autocomplete="off" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required><br><br>
            </div>
            <div class="form-group">
                <label for="org_email">Organization Email:</label>
                <input type="email" id="org_email" name="org_email" autocomplete="off" required><br><br>
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