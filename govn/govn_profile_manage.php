<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['sv_gvn_staffid'])) {
    header("Location: govn_login.php");
    exit();
}

// Database connection details
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "urbanlink";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $govn_staff_name = $_POST['govn_staff_name'];
    $govn_staff_phone = $_POST['govn_staff_phone'];
    $govn_staff_email = $_POST['govn_staff_email'];
    $govn_staff_password = $_POST['govn_staff_password'];
    $govn_staff_dept = $_POST['govn_staff_work_dept'];
    $govn_staff_mpin = $_POST['mpin'];
    $govn_staff_location = $_POST['govn_staff_location'];
    // $govn_staff_id = $_SESSION['sv_gvn_staffid'];

    // Update user profile in the database
    $stmt = $conn->prepare("UPDATE govn_staff_details SET govn_staff_name = ?, govn_staff_work_dept = ?, govn_staff_phone = ?, govn_staff_email = ?, govn_staff_password = ?, govn_staff_location = ?, govn_staff_mpin = ? WHERE govn_staff_id = ?");
    $stmt->bind_param("sssssssi", $govn_staff_name, $govn_staff_dept, $govn_staff_phone, $govn_staff_email, $govn_staff_password, $govn_staff_location, $govn_staff_mpin, $_SESSION['sv_gvn_staffid']);

    if ($stmt->execute()) {
        $successMessage = "Profile updated successfully";
        // Update session variables with new values
        $_SESSION['sv_gvn_staffname'] = $govn_staff_name;
        $_SESSION['sv_gvn_staffloc'] = $govn_staff_location;
        $_SESSION['sv_gvn_staffphone'] = $govn_staff_phone;
        $_SESSION['sv_gvn_staffemail'] = $govn_staff_email;
        $_SESSION['sv_gvn_staffdept'] = $govn_staff_dept;
    } else {
        $errorMessage = "Failed to update profile";
    }

    $stmt->close();
}

// Retrieve the user's current information for autofilling
$govn_staff_id = $_SESSION['sv_gvn_staffid'];
$sql = "SELECT * FROM govn_staff_details WHERE govn_staff_id = '$govn_staff_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <title>Government Staff Profile Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("../images/govn_prof_bg.jpg");
            background-size: 90%;
            background-position: 30px -180px;
            margin: 0;
            padding: 0;
        }


        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2), 0 0 20px rgba(0, 0, 0, 0.1);
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

        h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
        }

        .password-toggle {
            display: flex;
            align-items: center;
        }

        .password-toggle input {
            margin: 0;
        }

        .password-toggle span {
            margin-left: 5px;
        }

        .success-message {
            background-color: #4caf50;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .btn {
            background-color: #0088cc;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            left: 20%;
            width: 53%;
        }

        .btn:hover {
            background-color: #45a049;
            transition: 0.5s ease;
        }

        .go-back {
            padding: 10px;
            text-align: center;
            background-color: orange;
            cursor: pointer;
            color: white;
            text-decoration: none;
        }

        .go-back:hover {
            background-color: #0088cc;
            transition: 0.2s linear;
        }

        .back-button {
            position: relative;
            top: 20px;
            margin-left: 10px;
        }

        /* Media Queries */
        @media only screen and (min-width:701) and (max-width: 900px) {
            .container {
                width: 90%;
            }
        }

        @media only screen and (min-width:501) and (max-width: 700px) {
            .container {
                width: 90%;
            }
        }

        @media only screen and (min-width:300) and (max-width: 500px) {

            body {
                background-image: none;
                background-color: #0088cc;
                margin: 0;
                padding: 0;
                height: 100vh;
                width: 80%;
            }

            .container {
                width: 65%;
                position: relative;
                left: 500px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                width: 60%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
        }
    </style>

</head>

<body>
    <div class="back-button">
        <a href="govn_landing.php" class="go-back">⬅️</a>
    </div>
    <div class="container">
        <h2>Government Staff Profile Management</h2>
        <?php if (isset($successMessage)) : ?>
            <div class="success-message">
                <?php echo $successMessage; ?>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'govn_landing.php';
                }, 3000);
            </script>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="govn_staff_name">Your Name</label>
                <input type="text" id="govn_staff_name" name="govn_staff_name" value="<?php echo $row['govn_staff_name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="govn_staff_phone">Phone</label>
                <input type="text" id="govn_staff_phone" name="govn_staff_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" value="<?php echo $row['govn_staff_phone']; ?>" required>
            </div>

            <div class="form-group">
                <label for="govn_staff_email">Email</label>
                <input type="email" id="govn_staff_email" name="govn_staff_email" value="<?php echo $row['govn_staff_email']; ?>" required>
            </div>

            <div class="form-group">
                <label for="govn_staff_password">Password</label>
                <div class="password-toggle">
                    <input type="password" id="govn_staff_password" name="govn_staff_password" value="<?php echo $row['govn_staff_password']; ?>" required>
                    <input type="checkbox" onclick="togglePasswordVisibility(this)">
                    <span>Show Password</span>
                </div>
            </div>

            <div class="form-group">
                <label for="govn_staff_work_dept">Department</label>
                <select id="govn_staff_work_dept" name="govn_staff_work_dept" required>
                    <option value="Revenue Department" <?php if ($row['govn_staff_work_dept'] == 'Revenue Department') echo 'selected'; ?>>Revenue Department</option>
                    <option value="Public Works Department (PWD)" <?php if ($row['govn_staff_work_dept'] == 'Public Works Department (PWD)') echo 'selected'; ?>>Public Works Department (PWD)</option>
                    <option value="Municipal Corporation" <?php if ($row['govn_staff_work_dept'] == 'Municipal Corporation') echo 'selected'; ?>>Municipal Corporation</option>
                    <option value="Health Department" <?php if ($row['govn_staff_work_dept'] == '') echo 'selected'; ?>>Health Department</option>
                    <option value="Education Department" <?php if ($row['govn_staff_work_dept'] == '') echo 'selected'; ?>>Education Department</option>
                    <option value="Agriculture Department" <?php if ($row['govn_staff_work_dept'] == '') echo 'selected'; ?>>Agriculture DepartmentPolice Department</option>
                    <option value="Police Department" <?php if ($row['govn_staff_work_dept'] == 'Police Department') echo 'selected'; ?>>Police Department</option>
                    <option value="Fire and Rescue Services Department" <?php if ($row['govn_staff_work_dept'] == 'Fire and Rescue Services Department') echo 'selected'; ?>>Fire and Rescue Services Department</option>
                    <option value="Social Welfare Department" <?php if ($row['govn_staff_work_dept'] == 'Social Welfare Department') echo 'selected'; ?>>Social Welfare Department</option>
                    <option value="Rural Development Department" <?php if ($row['govn_staff_work_dept'] == 'Rural Development Department') echo 'selected'; ?>>Rural Development Department</option>
                    <option value="Transport Department" <?php if ($row['govn_staff_work_dept'] == 'Transport Department') echo 'selected'; ?>>Transport Department</option>
                    <option value="Forest Department" <?php if ($row['govn_staff_work_dept'] == 'Forest Department') echo 'selected'; ?>>Forest Department</option>
                    <option value="Animal Husbandry Department" <?php if ($row['govn_staff_work_dept'] == 'Animal Husbandry Department') echo 'selected'; ?>>Animal Husbandry Department</option>
                    <option value="Town Planning Department" <?php if ($row['govn_staff_work_dept'] == 'Town Planning Department') echo 'selected'; ?>>Town Planning Department</option>
                    <option value="Electricity Department" <?php if ($row['govn_staff_work_dept'] == 'Electricity Department') echo 'selected'; ?>>Electricity Department</option>
                    <option value="Water Supply and Sanitation Department" <?php if ($row['govn_staff_work_dept'] == 'Water Supply and Sanitation Department') echo 'selected'; ?>>Water Supply and Sanitation Department</option>
                    <option value="Public Distribution System (PDS) Department" <?php if ($row['govn_staff_work_dept'] == 'Public Distribution System (PDS) Department') echo 'selected'; ?>>Public Distribution System (PDS) Department</option>
                    <option value="Information and Public Relations Department" <?php if ($row['govn_staff_work_dept'] == 'Information and Public Relations Department') echo 'selected'; ?>>Information and Public Relations Department</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mpin">MPIN</label>
                <input type="password" id="mpin" name="mpin" minlength="6" maxlength="6" value="<?php echo $row['govn_staff_mpin']; ?>" required>
            </div>

            <div class="form-group">
                <label for="govn_staff_location">Location</label>
                <select id="govn_staff_location" name="govn_staff_location" required>
                    <option value="Gobichettipalayam" <?php if ($row['govn_staff_location'] == 'Gobichettipalayam') echo 'selected'; ?>>Gobichettipalayam</option>
                    <option value="Sathyamangalam" <?php if ($row['govn_staff_location'] == 'Sathyamangalam') echo 'selected'; ?>>Sathyamangalam</option>
                </select>
            </div>

            <input type="submit" value="Update">
        </form>
    </div>

    <script>
        function togglePasswordVisibility(checkbox) {
            var passwordInput = document.getElementById("govn_staff_password");
            if (checkbox.checked) {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>
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