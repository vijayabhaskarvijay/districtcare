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

// Function to fetch NGO details based on its ID
function getNGOById($ngoID)
{
    global $conn;

    $query = "SELECT * FROM ngo_details WHERE ngo_id = '$ngoID'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}
// Function to update NGO details based on its ID
function updateNGO($ngoID, $userData)
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


    $query = "UPDATE ngo_details SET $userUpdates WHERE ngo_id = '$ngoID';";

    if ($conn->multi_query($query) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the NGO ID from the form submission
    $ngoID = $_POST["ngo_id"];

    // Get the user and organization details from the form submission
    $userData = array(
        "ngo_user_name" => $_POST["ngo_user_name"],
        "ngo_user_position" => $_POST["ngo_user_position"],
        "ngo_user_phone" => $_POST["ngo_user_phone"],
        "ngo_user_email" => $_POST["ngo_user_email"],
        "ngo_user_pwd" => $_POST["ngo_user_pwd"],
        "ngo_org_name" => $_POST["ngo_org_name"],
        "ngo_org_place" => $_POST["ngo_org_place"],
        "ngo_org_phone" => $_POST["ngo_org_phone"],
        "ngo_org_mail" => $_POST["ngo_org_mail"]
    );

    // Update the NGO details in the database
    $updateResult = updateNGO($ngoID, $userData);

    // Check if the update was successful
    if ($updateResult) {
        $successMessage = "Updated successfully. NGO ID: $ngoID";
        echo '<div id="popup" class="popup">
                    <h3>Success!</h3>
                    <p>' . $successMessage . '</p>
                </div>';
        echo '<script>
                    setTimeout(function() {
                        window.location.href = "admin_ngo_manage.php";
                    }, 3000);
                </script>';
    } else {
        // Redirect back to the admin_ngo_manage.php page with an error message
        header("Location: admin_ngo_manage.php?message=error");
        exit();
    }
}

// Check if the NGO ID is provided in the URL
if (isset($_GET["id"])) {
    $ngoID = $_GET["id"];
    // Fetch the NGO details based on its ID
    $ngoDetails = getNGOById($ngoID);

    if (!$ngoDetails) {
        // If the NGO ID is not found, redirect back to admin_ngo_manage.php
        header("Location: admin_ngo_manage.php");
        exit();
    }
} else {
    // If the NGO ID is not provided in the URL, redirect back to admin_ngo_manage.php
    header("Location: admin_ngo_manage.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Update NGO Details</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #007bff;
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
            width: 40%;
            margin: 10px auto;
            background-color: rgba(255, 255, 255, 0.9);
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
        <a href="admin_ngo_manage.php" class="go-back">⬅️</a>
    </div>
    <div class="container">
        <h1>Update NGO Details</h1>
        <form method="post" action="">
            <input type="hidden" name="ngo_id" value="<?php echo $ngoID; ?>">
            <label for="ngo_user_name">NGO User Name:</label>
            <input type="text" name="ngo_user_name" value="<?php echo $ngoDetails["ngo_user_name"]; ?>" required><br>

            <label for="ngo_user_position">NGO User Position:</label>
            <input type="text" name="ngo_user_position" value="<?php echo $ngoDetails["ngo_user_position"]; ?>" required><br>

            <label for="ngo_user_phone">NGO User Phone:</label>
            <input type="tel" name="ngo_user_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" value="<?php echo $ngoDetails["ngo_user_phone"]; ?>" required><br>

            <label for="ngo_user_email">NGO User Email:</label>
            <input type="email" name="ngo_user_email" value="<?php echo $ngoDetails["ngo_user_email"]; ?>" required><br>

            <label for="ngo_user_pwd">NGO User Password:</label>
            <input type="password" name="ngo_user_pwd" id="ngo_user_pwd" value="<?php echo $ngoDetails["ngo_user_pwd"]; ?>" required>
            <i class="fas fa-eye toggle-password"></i>

            <label for="ngo_org_name">Organization Name:</label>
            <input type="text" name="ngo_org_name" value="<?php echo $ngoDetails["ngo_org_name"]; ?>" required><br>

            <label for="ngo_org_place">Organization Place:</label>
            <input type="text" name="ngo_org_place" value="<?php echo $ngoDetails["ngo_org_place"]; ?>" required><br>

            <label for="ngo_org_phone">Organization Phone:</label>
            <input type="tel" name="ngo_org_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" value="<?php echo $ngoDetails["ngo_org_phone"]; ?>" required><br>

            <label for="ngo_org_mail">Organization Email:</label>
            <input type="email" name="ngo_org_mail" value="<?php echo $ngoDetails["ngo_org_mail"]; ?>" required><br>

            <input type="submit" value="Update NGO">
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
        const passwordInput = document.getElementById('ngo_user_pwd');
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