<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['sv_admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $userPhone = $_POST['user_phone'];
    $userEmail = $_POST['user_email'];
    $userPassword = $_POST['user_password'];

    // Database connection
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "urbanlink";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query
    $stmt = $conn->prepare("UPDATE admin_details SET admin_name = ?, admin_phone_number = ?, admin_email = ?, admin_pass = ? WHERE admin_id = ?");
    $stmt->bind_param("ssssi", $username, $userPhone, $userEmail, $userPassword, $_SESSION['sv_admin_id']);

    // Execute the query
    if ($stmt->execute()) {
        $successMessage = "Profile updated successfully";
        // Update the session variables
        $_SESSION['sv_admin_username'] = $username;
        $_SESSION['sv_admin_email'] = $userEmail;
        $_SESSION['sv_admin_phone'] = $userPhone;
    } else {
        $errorMessage = "Failed to update profile";
    }

    // Close database connection
    $stmt->close();
    $conn->close();

    // Refresh the page to display updated values
    header("Refresh: 2"); // Delay of 2 seconds before refreshing the page
}
// Ensure session variables are set before accessing them
$adminID = isset($_SESSION['sv_admin_id']) ? $_SESSION['sv_admin_id'] : '';
$adminUsername = isset($_SESSION['sv_admin_username']) ? $_SESSION['sv_admin_username'] : '';
$adminEmail = isset($_SESSION['sv_admin_email']) ? $_SESSION['sv_admin_email'] : '';
$adminPhone = isset($_SESSION['sv_admin_phone']) ? $_SESSION['sv_admin_phone'] : '';

?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Profile Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            /* background: linear-gradient(to right, #4b6cb7, #182848); */
            /* display: flex; */
            justify-content: center;
            align-items: center;
            height: 95vh;
            margin: 0;
            padding: 0;
            background-image: url("../images/admin_prof_bg.jpg");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .container {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            padding: 20px;
            width: 40%;
            box-sizing: border-box;
            box-shadow: 0 0 20px rgba(0, 0, 0, 1);
            backdrop-filter: blur(10px);
            position: relative;
            left: 30%;
            top: 10%;
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
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
            left: 5%;
        }

        .toggle-password:hover {
            color: #de390b;
        }

        .go-back {
            padding: 10px;
            text-align: center;
            background-color: orange;
            cursor: pointer;
            color: white;
            text-decoration: none;
            border-radius: 5px;
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

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            position: relative;
            left: 9%;

        }

        .form-group input {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            position: relative;
            left: 15%;

        }

        .form-group .password input {
            font-family: monospace;
        }

        /* .form-group .password-toggle {
            display: flex;
            align-items: center;
            margin-top: 5px;
            position: relative;
            top: -40px;
            left: 43%;
            height: 30px;
        }

        .form-group .password-toggle input {
            margin-right: 5px;
        } */

        .btn {
            display: block;
            width: 74%;
            padding: 10px;
            text-align: center;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            left: 15%;
        }

        .success-message,
        .error-message {
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #182848;
            transition: 0.2s ease-in;
        }



        @media only screen and (min-width: 901px) and (max-width: 1100px) {
            body {
                background-size: cover;
                width: 100%;
            }

            .container {
                width: 50%;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
                position: absolute;
                top: 40px;
                left: 220px;
                margin: 0;
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                font-weight: bold;
                position: relative;
                left: 1%;
            }

            .form-group input {
                width: 80%;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
                position: relative;
                left: 8%;
            }

            .form-group select {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                /* display: flex; */
                position: absolute;
                top: 420px;
                left: 40px;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;
                width: 50px;
            }

            .btn {
                display: block;
                width: 86%;
                padding: 10px;
                text-align: center;
                background-color: #4CAF50;
                color: #fff;
                text-decoration: none;
                border-radius: 3px;
                cursor: pointer;
                position: relative;
                left: 35px;
            }
        }

        @media only screen and (min-width: 701px) and (max-width: 900px) {
            body {
                background-size: cover;
                width: 68%;
            }

            .container {
                width: 60%;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
                position: absolute;
                top: 10px;
                left: 120px;
                margin: 0;
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                font-weight: bold;
                position: relative;
                left: -1%;
            }

            .form-group input {
                width: 90%;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
                position: relative;
                left: 1%;
            }

            .form-group select {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                /* display: flex; */
                position: absolute;
                top: 420px;
                left: 5px;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;
                width: 50px;
            }

            .btn {
                display: block;
                width: 100%;
                padding: 10px;
                text-align: center;
                background-color: #4CAF50;
                color: #fff;
                text-decoration: none;
                border-radius: 3px;
                cursor: pointer;
            }
        }

        @media only screen and (min-width: 601px) and (max-width: 700px) {
            body {
                background-size: cover;
                width: 68%;
            }

            .container {
                width: 60%;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
                position: absolute;
                top: 10px;
                left: 120px;
                margin: 0;
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                font-weight: bold;
                position: relative;
                left: -1%;
            }

            .form-group input {
                width: 90%;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
                position: relative;
                left: 1%;
            }

            .form-group select {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                /* display: flex; */
                position: absolute;
                top: 420px;
                left: 5px;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;
                width: 50px;
            }

            .btn {
                display: block;
                width: 100%;
                padding: 10px;
                text-align: center;
                background-color: #4CAF50;
                color: #fff;
                text-decoration: none;
                border-radius: 3px;
                cursor: pointer;
            }
        }

        @media only screen and (min-width: 501px) and (max-width: 600px) {
            body {
                background-size: cover;
                width: 68%;
            }

            .container {
                width: 60%;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
                position: absolute;
                top: 10px;
                left: 120px;
                margin: 0;
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                font-weight: bold;
                position: relative;
                left: -1%;
            }

            .form-group input {
                width: 90%;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
                position: relative;
                left: 1%;
            }

            .form-group select {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                /* display: flex; */
                position: absolute;
                top: 450px;
                left: 25px;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;
            }

            .btn {
                display: block;
                width: 100%;
                padding: 10px;
                text-align: center;
                background-color: #4CAF50;
                color: #fff;
                text-decoration: none;
                border-radius: 3px;
                cursor: pointer;
            }
        }

        @media only screen and (min-width:300px) and (max-width: 500px) {

            body {
                background-image: none;
                max-width: 100% !important;
                background-color: #4b6cb7;
                margin: 0;
                padding: 0;
            }


            .container {
                position: absolute;
                display: flex;
                flex-direction: column;
                width: 90%;
                top: 70px;
                left: 20px;
                height: 550px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                font-weight: bold;
                position: relative;
                left: 1%;
            }

            .form-group input {
                width: 85%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
                position: relative;
                left: 10px;
            }

            .form-group select {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                position: relative;
                top: -10px;
                left: -40px;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;

                width: 100px;
            }


            .btn {
                width: 100%;
                padding: 10px;
                text-align: center;
                background-color: #4CAF50;
                color: #fff;
                text-decoration: none;
                border-radius: 10px;
                cursor: pointer;
                position: relative;
                top: -30px;
            }



        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="admin_landing.php" class="go-back">⬅️</a>
    </div>
    <!-- Your HTML content for the admin profile management page -->
    <div class="container">
        <h2>Admin Profile Management</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Admin profile form fields -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $adminUsername; ?>" required>
            </div>
            <div class="form-group">
                <label for="user_phone">User Phone</label>
                <input type="text" id="user_phone" name="user_phone" value="<?php echo $adminPhone; ?>" placeholder="Enter Your Phone Number" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)">
            </div>
            <div class="form-group">
                <label for="user_email">User Email</label>
                <input type="email" id="user_email" name="user_email" value="<?php echo $adminEmail; ?>" required>
            </div>
            <div class="form-group password">
                <label for="user_password">User Password</label>
                <input type="password" id="user_password" name="user_password" placeholder="Enter Your New Password or Enter Old one" required>
                <i class="fas fa-eye toggle-password"></i>
            </div>
            <!-- Add any additional form fields for admin profile management if needed -->

            <button type="submit" class="btn">Update</button>
        </form>
        <?php if (isset($successMessage)) { ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php } ?>
        <?php if (isset($errorMessage)) { ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php } ?>
    </div>

    <!-- Add your JavaScript code here -->
    <script>
        const passwordInput = document.getElementById('user_password');
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