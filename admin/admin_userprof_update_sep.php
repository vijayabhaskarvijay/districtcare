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

// Function to fetch user details based on its ID and table name
function getUserById($userID, $table)
{
    global $conn;

    $column_name = ($table == 'gobi_users') ? 'gobi_user' : 'sathy_user';

    $query = "SELECT * FROM $table WHERE {$column_name}_id = '$userID'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Function to update user details based on its ID and table name
function updateUser($userID, $userData, $table)
{
    global $conn;
    $userKeys = array_keys($userData);
    $userValues = array_map(function ($value) {
        return "'" . $value . "'";
    }, $userData);

    $userUpdates = implode(
        ", ",
        array_map(function ($key, $value) {
            return $key . " = " . $value;
        }, $userKeys, $userValues)
    );

    $column_name = ($table == 'gobi_users') ? 'gobi_user' : 'sathy_user';
    $query = "UPDATE $table SET $userUpdates WHERE {$column_name}_id = '$userID';";

    if ($conn->multi_query($query) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user ID and table name from the form submission
    $userID = $_POST["user_id"];
    $table = $_POST["table"];

    $column_name = ($table == 'gobi_users') ? 'gobi_user' : 'sathy_user';

    // Get the user details from the form submission
    $userData = array(
        "{$column_name}_name" => $_POST["user_name"],
        "{$column_name}_dob" => $_POST["user_dob"],
        "{$column_name}_phone_number" => $_POST["user_phone"],
        "{$column_name}_place" => $_POST["user_place"],
        "{$column_name}_email" => $_POST["user_email"],
        "{$column_name}_password" => $_POST["user_pwd"],
        "{$column_name}_address" => $_POST["user_address"],
        "{$column_name}_main_area" => $_POST["user_main_area"],
        "{$column_name}_acc_status" => $_POST["user_acc_status"],
        "{$column_name}_mpin" => $_POST["user_mpin"]
    );

    // Update the user details in the database
    $updateResult = updateUser($userID, $userData, $table);

    // Check if the update was successful
    if ($updateResult) {
        $successMessage = "Updated successfully.";
        echo '<div id="popup" class="popup">
                    <h3>Success!</h3>
                    <p>' . $successMessage . '</p>
                </div>';
        echo '<script>
                    setTimeout(function() {
                        window.location.href = "admin_user_management.php";
                    }, 3000);
                </script>';
    } else {
        // Redirect back to the admin_user_management.php page with an error message
        header("Location: admin_user_management.php?message=error");
        exit();
    }
}


// Check if the user ID and table name are provided in the URL
if (isset($_GET["id"]) && isset($_GET["table"])) {
    $userID = $_GET["id"];
    $table = $_GET["table"];

    $column_name = ($table == 'gobi_users') ? 'gobi_user' : 'sathy_user';
    // Fetch the user details based on its ID and table name
    $userDetails = getUserById($userID, $table);

    if (!$userDetails) {
        // If the user ID is not found, redirect back to admin_user_management.php
        header("Location: admin_user_management.php");
        exit();
    }
} else {
    // If the user ID and table name are not provided in the URL, redirect back to admin_user_management.php
    header("Location: admin_user_management.php");
    exit();
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Update User Details</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
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
            border: 2px solid cadetblue;
            padding: 5px;
            border-radius: 5px;
            background-color: cadetblue;
            cursor: pointer;
        }

        .toggle-password:hover {
            color: #de390b;
        }

        .container {
            width: 40%;
            margin: 10px auto;
            background-color: rgba(212, 212, 212, 0.9);
            backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 10px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            box-shadow: 0 0 20px rgba(0, 0, 0, 1);
            position: relative;
            height: 70%;
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
            top: -30px;
        }

        form {
            display: flex;
            flex-direction: column;
            position: relative;
            top: -30px;
            left: 20%;
        }

        label {
            font-weight: bold;
            width: 200px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"] {
            width: 60%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: none;
            border-bottom: 2px solid black;
            border-radius: 5px;
            font-size: 14px;
        }

        select {
            width: 63%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: none;
            border-bottom: 2px solid black;
            /* border-radius: 5px; */
            font-size: 14px;
        }

        input[type="submit"] {
            position: relative;
            left: 5%;
            width: 50%;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #4bb300;
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
        <a href="admin_user_management.php" class="go-back">⬅️</a>
    </div>
    <div class="container">
        <h1>Update User Profile</h1>
        <form method="post" action="">
            <input type="hidden" name="user_id" value="<?php echo $userID; ?>">
            <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>"> <!-- Added hidden input for table name -->

            <?php
            $table = $_GET['table'];
            $column_name = ($table == 'gobi_users') ? 'gobi_user' : 'sathy_user';
            ?>

            <label for="user_name"> User Name:</label>
            <input type="text" name="user_name" value="<?php echo $userDetails[$column_name . '_name']; ?>" required><br>

            <label for="user_dob"> User DOB:</label>
            <input type="date" name="user_dob" value="<?php echo $userDetails[$column_name . '_dob']; ?>" required><br>

            <label for="user_phone"> User Phone:</label>
            <input type="tel" name="user_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" value="<?php echo $userDetails[$column_name . '_phone_number']; ?>" required><br>

            <label for="user_email"> User Email:</label>
            <input type="email" name="user_email" value="<?php echo $userDetails[$column_name . '_email']; ?>" required><br>

            <label for="user_pwd"> User Password:</label>
            <input type="password" name="user_pwd" id="user_pwd" value="<?php echo $userDetails[$column_name . '_password']; ?>" required>
            <i class="fas fa-eye toggle-password"></i>

            <label for="user_place">User Location:</label>
            <select name="user_place" required>
                <option value="-- Select Option --">-- Select Option --</option>
                <option value="Gobichettipalayam" <?php if ($userDetails[$column_name . '_place'] == 'Gobichettipalayam') echo 'selected'; ?>>Gobichettipalayam</option>
                <option value="Sathyamangalam" <?php if ($userDetails[$column_name . '_place'] == 'Sathyamangalam') echo 'selected'; ?>>Sathyamangalam</option>
            </select><br>

            <label for="user_address">User Address:</label>
            <input type="text" name="user_address" value="<?php echo $userDetails[$column_name . '_address']; ?>" required><br>

            <label for="user_main_area">User Main Area:</label>
            <input type="text" name="user_main_area" value="<?php echo $userDetails[$column_name . '_main_area']; ?>" required><br>

            <label for="user_acc_status">User Account Status:</label>
            <select name="user_acc_status" required>
                <option value="UNBLOCKED" <?php if ($userDetails[$column_name . '_acc_status'] == 'UNBLOCKED') echo 'selected'; ?>>UNBLOCKED</option>
                <option value="BLOCKED" <?php if ($userDetails[$column_name . '_acc_status'] == 'BLOCKED') echo 'selected'; ?>>BLOCKED</option>
            </select><br>

            <label for="user_mpin">User MPIN:</label>
            <input type="text" name="user_mpin" value="<?php echo $userDetails[$column_name . '_mpin']; ?>" required><br>

            <input type="submit" value="Update User Profile">
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
        const passwordInput = document.getElementById('user_pwd');
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