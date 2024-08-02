<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $staffName = $_POST['staff_name'];
    $staffPhone = $_POST['staff_phone'];
    $staffEmail = $_POST['staff_email'];
    $staffPlace = $_POST['staff_place'];
    $staffDept = $_POST['staff_dept'];
    $staffMpin = $_POST['staff_mpin'];

    // Verify the details in the database
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "urbanlink";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM govn_staff_details WHERE govn_staff_name = :staffName AND govn_staff_phone = :staffPhone AND govn_staff_email = :staffEmail AND govn_staff_work_dept = :staffDept AND govn_staff_location =:staffPlace  AND govn_staff_mpin = :staffMpin");
        $stmt->bindParam(':staffName', $staffName);
        $stmt->bindParam(':staffPhone', $staffPhone);
        $stmt->bindParam(':staffEmail', $staffEmail);
        $stmt->bindParam(':staffDept', $staffDept);
        $stmt->bindParam(':staffPlace', $staffPlace);
        $stmt->bindParam(':staffMpin', $staffMpin);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $govnId = $row['govn_staff_id'];
            $_SESSION['govn_id'] = $govnId;
            $successMessage = "User found. Redirecting to password reset page...";
            echo "<script>
                    setTimeout(function() {
                                window.location.href='govn_reset_pwd.php';
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
    <title>GOVN Password Reset</title>
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
    <h1>GOVN Password Reset</h1>
    <?php if (isset($successMessage)) echo "<p class='message success'>$successMessage</p>"; ?>
    <?php if (isset($errorMessage)) echo "<p class='message error'>$errorMessage</p>"; ?>
    <div class="container">
        <form method="post" action="">
            <div class="form-group">
                <label for="staff_name">Staff Name:</label>
                <input type="text" id="staff_name" name="staff_name" autocomplete="off" required><br><br>
            </div>
            <div class="form-group">
                <label for="staff_phone">Staff Phone:</label>
                <input type="text" id="staff_phone" name="staff_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" autocomplete="off" required><br><br>
            </div>
            <div class="form-group">
                <label for="staff_email">Staff Email:</label>
                <input type="email" id="staff_email" name="staff_email" autocomplete="off" required><br><br>
            </div>

            <div class="form-group">
                <label for="staff_place">Staff Place:</label>
                <select id="staff_place" name="staff_place" required>
                    <option value="Select">-- Select Option --</option>
                    <option value="Gobichettipalayam">Gobichettipalayam</option>
                    <option value="Sathyamangalam">Sathyamangalam</option>
                </select><br><br>
            </div>

            <div class="form-group">
                <label for="staff_dept">Staff Department:</label>
                <select id="staff_dept" name="staff_dept" required>
                    <option value="-- Select Department --">-- Select Department --</option>
                    <option value="Revenue Department">Revenue Department</option>
                    <option value="Public Works Department (PWD)">Public Works Department (PWD)</option>
                    <option value="Municipal Corporation">Municipal Corporation</option>
                    <option value="Health Department">Health Department</option>
                    <option value="Education Department">Education Department</option>
                    <option value="Agriculture Department">Agriculture Department</option>
                    <option value="Police Department">Police Department</option>
                    <option value="Fire and Rescue Services Department">Fire and Rescue Services Department</option>
                    <option value="Social Welfare Department">Social Welfare Department</option>
                    <option value="Rural Development Department">Rural Development Department</option>
                    <option value="Transport Department">Transport Department</option>
                    <option value="Forest Department">Forest Department</option>
                    <option value="Animal Husbandry Department">Animal Husbandry Department</option>
                    <option value="Town Planning Department">Town Planning Department</option>
                    <option value="Electricity Department">Electricity Department</option>
                    <option value="Water Supply and Sanitation Department">Water Supply and Sanitation Department</option>
                    <option value="Public Distribution System (PDS) Department">Public Distribution System (PDS) Department</option>
                    <option value="Information and Public Relations Department">Information and Public Relations Department</option>
                </select>
            </div>

            <div class="form-group">
                <label for="staff_mpin">Staff MPIN:</label>
                <input type="text" id="staff_mpin" maxlength="6" name="staff_mpin" autocomplete="off" required><br><br>
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