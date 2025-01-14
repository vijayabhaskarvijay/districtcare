<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['ngo_username'])) {
    header("Location: ngo_login.php");
    exit();
}

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

$username = $_SESSION['ngo_username']; // Assuming this session variable is set
$sql = "SELECT * FROM ngo_details WHERE ngo_user_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['ngo_user_name'];
    $position = $row['ngo_user_position'];
    $userPhone = $row['ngo_user_phone'];
    $userEmail = $row['ngo_user_email'];
    $userPassword = $row['ngo_user_pwd'];
    $orgName = $row['ngo_org_name'];
    // $orgPlace = $row['ngo_org_place'];
    $orgPhone = $row['ngo_org_phone'];
    $orgEmail = $row['ngo_org_mail'];
    $usermpin = $row['ngo_user_mpin'];
} else {
    echo "<div class='error'>User not found.</div>";
    exit();
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $position = $_POST['position'];
    $userPhone = $_POST['user_phone'];
    $userEmail = $_POST['user_email'];
    $userPassword = $_POST['user_password'];
    $orgName = $_POST['org_name'];
    // $orgPlace = $_POST['org_place'];
    $orgPhone = $_POST['org_phone'];
    $orgEmail = $_POST['org_email'];
    $usermpin = $_POST['mpin'];

    // Validate form data
    // TODO: Add your validation logic here


    // Prepare SQL query
    $stmt = $conn->prepare("UPDATE ngo_details SET ngo_user_name = ?, ngo_user_position = ?, ngo_user_phone = ?, ngo_user_email = ?, ngo_user_pwd = ?, ngo_org_name = ?, ngo_org_phone = ?, ngo_org_mail = ?,ngo_user_mpin=? WHERE ngo_id = ?");
    $stmt->bind_param("sssssssssi", $username, $position, $userPhone, $userEmail, $userPassword, $orgName, $orgPhone, $orgEmail, $usermpin, $_SESSION['ngo_id']);
    // $stmt = $conn->prepare("UPDATE ngo_details SET ngo_user_name = ?, ngo_user_position = ?, ngo_user_phone = ?, ngo_user_email = ?, ngo_user_pwd = ?, ngo_org_name = ?, ngo_org_place = ?, ngo_org_phone = ?, ngo_org_mail = ?,ngo_user_mpin=? WHERE ngo_id = ?");
    // $stmt->bind_param("ssssssssssi", $username, $position, $userPhone, $userEmail, $userPassword, $orgName, $orgPlace, $orgPhone, $orgEmail, $usermpin, $_SESSION['ngo_id']);

    // Execute the query
    if ($stmt->execute()) {
        $successMessage = "Profile updated successfully";
        // Update the session variables
        $_SESSION['ngo_username'] = $username;
        $_SESSION['ngo_userposition'] = $position;
        $_SESSION['ngo_userphone'] = $userPhone;
        $_SESSION['ngo_useremail'] = $userEmail;
        $_SESSION['ngo_userpassword'] = $userPassword;
        $_SESSION['ngo_orgname'] = $orgName;
        // $_SESSION['ngo_orgplace'] = $orgPlace;
        $_SESSION['ngo_orgphone'] = $orgPhone;
        $_SESSION['ngo_orgemail'] = $orgEmail;
        $_SESSION['ngo_mpin'] = $usermpin;
    } else {
        $errorMessage = "Failed to update profile";
    }

    // Close database connection
    $stmt->close();
    $conn->close();

    // Refresh the page
    header("Refresh: 2"); // Delay of 2 seconds before refreshing the page
}
?>





<!DOCTYPE html>
<html>

<head>
    <title>NGO Profile Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <style>
        .password-input {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 74%;
            left: 60%;
            transform: translateY(-50%);
            color: #aaa;
            cursor: pointer;
        }

        .toggle-mpin {
            position: relative;
            top: 10px;
            left: 2.2%;
            transform: translateY(-50%);
            color: #aaa;
            cursor: pointer;
        }

        .toggle-password:hover {
            color: #555;
        }

        .toggle-mpin:hover {
            color: #555;
        }

        .email {
            padding: 11px;
            width: 350px;
            border: 1px solid #BFC9CA;
            border-radius: 5px;
        }

        body {
            font-family: Arial, sans-serif;
            background-image: url("../images/climpek.png"), linear-gradient(to right, #4b6cb7, #182848);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            flex-direction: column;
        }

        .container {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            padding: 20px;
            width: 40%;
            box-sizing: border-box;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
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
            top: 30px;
            margin-left: 10px;
            left: -40%;
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            position: relative;
            left: 50px;

        }

        .form-group input {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            position: relative;
            left: 50px;

        }

        .form-group select {
            width: 74%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            position: relative;
            left: 50px;
        }

        .form-group .password input {
            font-family: monospace;
        }

        .form-group .password-toggle {
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

        @media only screen and (min-width: 901px) and (max-width: 1200px) {
            body {
                width: 80%;
            }

            .container {
                width: 60%;
            }

            .container h2 {
                text-align: center;
                margin-bottom: 20px;
                font-size: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                /* font-weight: bold; */
                font-size: 15px;
            }

            .form-group input {
                width: 90%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
            }

            .form-group select {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                /* display: flex; */
                position: relative;
                top: -35px;
                left: 52%;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;
                height: 20px;
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

        @media only screen and (min-width: 701px) and (max-width: 900px) {
            .container {
                width: 60%;
            }

            .container h2 {
                text-align: center;
                margin-bottom: 20px;
                font-size: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                /* font-weight: bold; */
                font-size: 15px;
            }

            .form-group input {
                width: 90%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
            }

            .form-group select {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                /* display: flex; */
                position: relative;
                top: -35px;
                left: 53%;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;
                height: 20px;
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

        @media only screen and (min-width: 501px) and (max-width: 700px) {
            body {
                max-width: 800px;
                width: 500px;
                padding: 0;
                margin: 0;
                background-size: contain;
            }

            .container {
                width: 60%;
                position: relative;
            }

            .container h2 {
                text-align: center;
                margin-bottom: 20px;
                font-size: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                /* font-weight: bold; */
                font-size: 15px;
            }

            .form-group input {
                width: 90%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
            }

            .form-group select {
                width: 97%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                /* display: flex; */
                position: relative;
                top: -35px;
                left: 56%;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;
                height: 20px;
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
            .container {
                width: 80%;
                position: relative;
                left: -10px;
            }

            .form-group label {
                display: block;
                margin-bottom: 7px;
                font-weight: bold;
            }

            .form-group input {
                width: 90%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-bottom: 5px;
            }

            .form-group select {
                width: 95%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }

            .form-group .password-toggle {
                /* display: flex; */
                position: relative;
                top: -40px;
                left: 56%;
                margin-top: 5px;
                margin-bottom: 0;
                padding: 0;
                height: 30px;
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
    </style>
</head>

<body>
    <div class="back-button">
        <a href="ngo_landing.php" class="go-back">⬅️ GO BACK</a>
    </div>
    <div class="container">
        <h2>NGO Profile Management</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="id">ID</label>
                <input type="text" id="id" value="<?php echo $_SESSION['ngo_id']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $_SESSION['ngo_username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" id="position" name="position" value="<?php echo $position ?>" placeholder="Enter Your Position">
            </div>
            <div class="form-group">
                <label for="user_phone">User Phone</label>
                <input type="text" id="user_phone" name="user_phone" value="<?php echo $_SESSION['ngo_userphone']; ?>" placeholder="Enter Your Phone Number">
            </div>
            <div class="form-group">
                <label for="user_email">User Email</label>
                <input type="email" id="user_email" name="user_email" value="<?php echo $_SESSION['ngo_useremail']; ?>" required>
            </div>
            <div class="form-group password">
                <label for="user_password">User Password</label>
                <input type="password" id="user_password" name="user_password" placeholder="Enter Your New Password or Enter Old one" value="<?php echo $userPassword ?>" required>
                <i class="fas fa-eye toggle-password"></i>

            </div>

            <div class="form-group">
                <div class="mpin-inputs">
                    <label for="mpin">MPIN:</label>
                    <input type="password" class="mpin-digit" maxlength="6" name="mpin" id="mpin" value="<?php echo $usermpin ?>" required>
                    <i class="fas fa-eye toggle-mpin"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="org_name">Organization Name</label>
                <input type="text" id="org_name" name="org_name" value="<?php echo $orgName ?>" placeholder="Enter Your Organization name">
            </div>
            <!-- <div class="form-group">
                <label for="org_place">Organization Place</label>
                <select id="org_place" name="org_place">
                    <option value="Gobichettipalayam" <?php if ($_SESSION['ngo_location'] == 'Gobichettipalayam') echo 'selected'; ?>>Gobichettipalayam</option>
                    <option value="Sathyamangalam" <?php if ($_SESSION['ngo_location'] == 'Sathyamangalam') echo 'selected'; ?>>Sathyamangalam</option>
                </select>
            </div> -->
            <div class="form-group">
                <label for="org_phone">Organization Phone</label>
                <input type="text" id="org_phone" name="org_phone" value="<?php echo $orgPhone ?>" placeholder="Enter Your Organization Phone ">
            </div>
            <div class="form-group">
                <label for="org_email">Organization Email</label>
                <input type="email" id="org_email" name="org_email" value="<?php echo $orgEmail ?>" placeholder="Enter Your Organization Email">
            </div>
            <button type="submit" class="btn">Update</button>
        </form>
        <?php if (isset($successMessage)) { ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php } ?>
        <?php if (isset($errorMessage)) { ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php } ?>
    </div>

    <div class="background-pattern">
        <div class="stars"></div>
        <div class="twinkling"></div>
    </div>

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
        const mpinInput = document.getElementById('mpin');
        const togglempin = document.querySelector('.toggle-mpin');

        togglempin.addEventListener('click', function() {
            const type = mpinInput.getAttribute('type') === 'password' ? 'text' : 'password';
            mpinInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>