<?php
// Replace with your actual database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanlink";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetchGovn details based on its ID
function getGovnById($govnstaffID)
{
    global $conn;

    $query = "SELECT * FROM govn_staff_details WHERE govn_staff_id = '$govnstaffID'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Function to Update Govn Staff Details based on its ID
function updateGovnstaff($govnstaffID, $userData)
{
    global $conn;

    $userKeys = array_keys($userData);

    $userValues = array_map(function ($value) {
        return "'" . $value . "'";
    }, $userData);

    $userUpdates = implode(", ", array_map(function ($key, $value) {
        return $key . " = " . $value;
    }, $userKeys, $userValues));

    $query = "UPDATE govn_staff_details SET $userUpdates WHERE govn_staff_id = '$govnstaffID';";

    if ($conn->multi_query($query) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get theGovn ID from the form submission
    $govnstaffID = $_POST["govn_staff_id"];

    // Get the user and organization details from the form submission
    $userData = array(
        "govn_staff_id" => $_POST["govn_staff_id"],
        "govn_staff_name" => $_POST["govn_staff_name"],
        "govn_staff_phone" => $_POST["govn_staff_phone"],
        "govn_staff_email" => $_POST["govn_staff_email"],
        "govn_staff_location" => $_POST["govn_staff_location"],
        "govn_staff_work_dept" => $_POST["govn_staff_work_dept"],
        "govn_staff_password" => $_POST["govn_staff_password"],
    );

    // Update theGovn details in the database
    $updateResult = updateGovnstaff($govnstaffID, $userData);

    // Check if the update was successful
    if ($updateResult) {
        $successMessage = "Updated successfully. GOVN STAFF-ID: $govnstaffID";
        echo '<div id="popup" class="popup">
                    <h3>Success!</h3>
                    <p>' . $successMessage . '</p>
                </div>';
        echo '<script>
                    setTimeout(function() {
                        window.location.href = "admin_govn_manage.php";
                    }, 3000);
                </script>';
        // exit();
    } else {
        // Redirect back to the admin_govn_manage.php page with an error message
        header("Location: admin_govn_manage.php?message=error");
        exit();
    }
}

// Check if theGovn ID is provided in the URL
if (isset($_GET["id"])) {
    $govnstaffID = $_GET["id"];
    // Fetch theGovn details based on its ID
    $govnDetails = getGovnById($govnstaffID);

    if (!$govnDetails) {
        // If theGovn ID is not found, redirect back to admin_govn_manage.php
        header("Location: admin_govn_manage.php");
        exit();
    }
} else {
    // If theGovn ID is not provided in the URL, redirect back to admin_govn_manage.php
    header("Location: admin_govn_manage.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Update Govn Staff Details</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("../images/admin_govn_update_sep_bg.jpg");
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            z-index: 9999;
            display: none;
        }

        .popup h3 {
            margin-top: 0;
        }

        .toggle-password {
            position: relative;
            width: 20px;
            margin-bottom: 10px;
            color: #ffffff;
            border: 2px solid pink;
            padding: 5px;
            border-radius: 5px;
            background-color: pink;
            cursor: pointer;
        }

        .toggle-password:hover {
            color: #de390b;
        }

        .container {
            width: 50%;
            margin: 10px auto;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 10px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            height: 60%;

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

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            position: relative;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            position: relative;
            left: 10%;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            width: 200px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 77%;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 14px;
        }

        .custom-select {
            width: 80%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 14px;
        }


        input[type="submit"] {
            width: 50%;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            left: 15%;
        }

        input[type="submit"]:hover {
            background-color: green;
        }

        @media only screen and (min-width:601px) and (max-width: 900px) {
            .container {
                /* margin: 20px; */
                width: 80%;
                height: 800px;
            }

            form {
                max-width: 100%;
            }
        }

        @media only screen and (min-width:300px) and (max-width: 600px) {
            .container {
                position: relative;
                top: 25px;
                width: 80%;
                height: 650px;
                padding: 10px;
            }

            form {
                max-width: 100%;
            }

            form {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                position: relative;
                top: -30px;
            }

            label {
                font-weight: bold;
                width: 200px;
                position: relative;
                left: -50px;
            }
        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="admin_govn_manage.php" class="go-back">⬅️</a>
    </div>
    <div class="container">
        <h1>Update Govn Staff Details</h1>
        <form method="post" action="">
            <input type="hidden" name="govn_staff_id" value="<?php echo $govnstaffID; ?>">
            <label for="govn_staff_name">Govn Staff Name:</label>
            <input type="text" name="govn_staff_name" value="<?php echo $govnDetails["govn_staff_name"]; ?>" required><br>

            <label for="govn_staff_phone">Govn Staff Phone:</label>
            <input type="tel" name="govn_staff_phone" value="<?php echo $govnDetails["govn_staff_phone"]; ?>" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required><br>

            <label for="govn_staff_email">Govn Staff Email:</label>
            <input type="email" name="govn_staff_email" value="<?php echo $govnDetails["govn_staff_email"]; ?>" required><br>
            <label for="govn_staff_location">Govn Staff Location:</label>
            <select id="govn_staff_location" name="govn_staff_location" class="custom-select">
                <option value="-- Select Option --">-- Select Option --</option>
                <option value="Gobichettipalayam">Gobichettipalayam</option>
                <option value="Sathyamangalam">Sathyamangalam</option>
            </select>
            <label for="govn_staff_work_dept">Govn Staff Department:</label>
            <select id="govn_staff_work_dept" name="govn_staff_work_dept" class="custom-select">
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
            <label for="govn_staff_password">Govn Staff Password:</label>
            <input type="password" name="govn_staff_password" id="govn_staff_password" value="<?php echo $govnDetails["govn_staff_password"]; ?>" required>
            <i class="fas fa-eye toggle-password"></i>

            <input type="submit" value="Update Govn">
        </form>
    </div>
    <script>
        // Display the popup
        document.addEventListener("DOMContentLoaded", function() {
            var popup = document.getElementById("popup");
            popup.style.display = "block";
        });
    </script>

    <script>
        const passwordInput = document.getElementById('govn_staff_password');
        const togglePassword = document.querySelector('.toggle-password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
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

<?php
// Close database connection
$conn->close();
?>