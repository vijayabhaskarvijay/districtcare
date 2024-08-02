<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanlink";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $place = $_POST['place'];
    $mainArea = $_POST['main_area'];
    $Mpin = $_POST['mpin'];

    // Calculate age based on the entered date of birth
    $birthDate = new DateTime($dob);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;

    // Check if an account with the same values already exists

    // Check if the user is at least 20 years old
    if ($age >= 20) {
        // Generate unique user ID based on the selected place
        $prefix = ($place == 'Gobichettipalayam') ? 'GBI' : 'STH';
        $user_id = $prefix . uniqid();

        // Insert into public_user_details table
        $sql_public = "INSERT INTO public_user_details (public_user_id, public_user_name, public_user_dob, public_user_phone, public_user_email, public_user_password, public_user_address, public_user_place, public_user_main_area)
                VALUES ('$user_id', '$name', '$dob', '$phone', '$email', '$password', '$address', '$place', '$mainArea')";

        // Execute query for public_user_details table
        if ($conn->query($sql_public) === TRUE) {
            $success_message = "Registration successful! Your user ID is: " . $user_id;

            // Insert into selected place table (gobi_users or sathy_users)
            if ($place == 'Gobichettipalayam') {
                $sql_place = "INSERT INTO gobi_users (gobi_user_id, gobi_user_name, gobi_user_dob, gobi_user_phone_number, gobi_user_email, gobi_user_password, gobi_user_address, gobi_user_place, gobi_user_main_area, gobi_user_age,gobi_user_mpin)
                        VALUES ('$user_id', '$name', '$dob', '$phone', '$email', '$password', '$address', '$place', '$mainArea', '$age','$Mpin')";
            } elseif ($place == 'Sathyamangalam') {
                $sql_place = "INSERT INTO sathy_users (sathy_user_id, sathy_user_name, sathy_user_dob, sathy_user_phone_number, sathy_user_email, sathy_user_password, sathy_user_address, sathy_user_place, sathy_user_main_area, sathy_user_age,sathy_user_mpin)
                        VALUES ('$user_id', '$name', '$dob', '$phone', '$email', '$password', '$address', '$place', '$mainArea', '$age','$Mpin')";
            } else {
                $error_message = "Invalid place selected.";
            }

            // Execute query for selected place table
            if (isset($sql_place) && $conn->query($sql_place) !== TRUE) {
                $error_message = "Error: " . $sql_place . "<br>" . $conn->error;
            } else {
                // Registration successful
                $success_message = "Registration successful! Your user ID is: " . $user_id;
                // Redirect to index.php after 3 seconds
                echo "<script>setTimeout(function(){window.location.href='../index.php';}, 3000);</script>";
            }
        } else {
            $error_message = "Error: " . $sql_public . "<br>" . $conn->error;
        }
    } else {
        $warning_message = "You must be at least 20 years old to register.";
    }
}

// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html>

<head>
    <title>UrbanLink - Registration</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .password-conditions {
            position: absolute;
            background: white;
            border: 2px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: red;
            z-index: 1;
        }

        .password-conditions ul {
            list-style: none;
            padding: 0;
            z-index: 1;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("../images/9176102_6590.jpg");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center top;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .container {
            width: 1000px;
            margin: 0 auto;
            padding: 20px;
            color: #000;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            animation: fade-in 0.5s ease-in-out;
            position: relative;
            top: 50px;
            height: 550px;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: black;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: black;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group select,
        .form-group textarea {
            width: 350px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease-in-out;
            box-sizing: border-box;
        }

        .form-group select option {
            font-size: 16px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group input[type="text"]:hover,
        .form-group input[type="email"]:hover,
        .form-group input[type="password"]:hover,
        .form-group select:hover,
        .form-group textarea:hover {
            border-color: #435f75;
        }

        .form-group .error-message {
            color: #ff0000;
            margin-top: 5px;
        }

        #dob {
            padding: 10px;
        }

        .left {
            width: 500px;
            margin-right: 10px;
            position: relative;
            left: 100px;
        }

        .right {
            width: 450px;
            position: relative;
            left: 550px;
            top: -440px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            position: relative;
            top: -400px;
            background-color: lightseagreen;
            color: black;
            font-size: 16px;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .btn:hover {
            background-color: green;
            transition: 0.2s ease-in;
        }

        .toast {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 9999;
        }


        /* Additional styles for the success message */
        @keyframes fadeOutDown {
            0% {
                opacity: 1;
                transform: translateY(0);
            }

            100% {
                opacity: 0;
                transform: translateY(50px);
            }
        }

        .success-message {
            background-color: #4CAF50;
            color: rgb(217, 221, 217);
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            animation: fadeIn 3s forwards;
            /* animation: fadeOutDown 3s forwards, fadeIn 3s forwards; */
        }

        /* Styles for other types of toast messages (e.g., warning, error) */
        .warning-message {
            background-color: #f0ad4e;
        }

        .error-message-password {
            border: 1px solid white;
            border-radius: 5px;
            padding: 5px 10px;
            color: #4CAF50;
            -webkit-animation: NAME-YOUR-ANIMATION 1s infinite;
            -moz-animation: NAME-YOUR-ANIMATION 1s infinite;
            -o-animation: NAME-YOUR-ANIMATION 1s infinite;
            animation: NAME-YOUR-ANIMATION 1s infinite;
            text-align: center;
            font-weight: 700;
        }

        .error-message {
            background-color: #d9534f;
            color: white;
            text-align: center;
            width: 25%;
        }

        .mpin-inputs {
            display: flex;
            width: 20px;
            width: 24px;
        }

        .mpin-digit {
            font-size: 1.2em;
            margin-right: 5px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .note-flash {
            border: 1px solid white;
            border-radius: 5px;
            padding: 5px 10px;
            color: #4CAF50;
            -webkit-animation: NAME-YOUR-ANIMATION 1s infinite;
            /* Safari 4+ */
            -moz-animation: NAME-YOUR-ANIMATION 1s infinite;
            /* Fx 5+ */
            -o-animation: NAME-YOUR-ANIMATION 1s infinite;
            /* Opera 12+ */
            animation: NAME-YOUR-ANIMATION 1s infinite;
            /* IE 10+, Fx 29+ */
        }

        @-webkit-keyframes NAME-YOUR-ANIMATION {

            0%,
            49% {
                background-color: #000;

            }

            50%,
            100% {
                background-color: #e50000;

            }
        }

        @media only screen and (min-width:901px) and (max-width:1050px) {
            body {
                background-position: fixed;
                background-size: cover;
                margin: 0;
                padding: 0;
                height: 100%;
                overflow-y: scroll;
            }

            .container {
                width: 80%;
                position: relative;
                top: 20px;
                height: 90%;
                /* left: -10px; */
            }

            .left,
            .right {
                position: relative;
                width: 100%;
                left: 20px;
                top: 0;
                /* margin-bottom: 30px; */
            }

            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="date"],
            .form-group input[type="password"],
            .form-group select,
            .form-group textarea {
                width: 90%;
                font-weight: 500;
            }

            .note-flash {
                width: 86%;
                font-weight: 500;
            }

            .form-group input[type="date"] {
                width: 86%;
            }

            .mpin-inputs {
                width: 100%;
            }

            .btn {
                position: relative;
                top: 0px;
            }
        }

        @media only screen and (min-width:701px) and (max-width:900px) {
            body {
                background-position: fixed;
                background-size: cover;
                margin: 0;
                padding: 0;
                height: 100%;
                overflow-y: scroll;
            }

            .container {
                width: 80%;
                position: relative;
                top: 20px;
                height: 90%;
                /* left: -10px; */
            }

            .left,
            .right {
                position: relative;
                width: 100%;
                left: 20px;
                top: 0;
                /* margin-bottom: 30px; */
            }

            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="date"],
            .form-group input[type="password"],
            .form-group select,
            .form-group textarea {
                width: 90%;
                font-weight: 500;
            }

            .note-flash {
                width: 86%;
                font-weight: 500;
            }

            .form-group input[type="date"] {
                width: 86%;
            }

            .mpin-inputs {
                width: 100%;
            }

            .btn {
                position: relative;
                top: 0px;
            }
        }

        @media only screen and (min-width:501px) and (max-width:700px) {
            body {
                background-image: none;
                background-color: #435f75;
                margin: 0;
                padding: 0;
                height: 100%;
                overflow-y: scroll;
            }

            .container {
                width: 80%;
                position: relative;
                top: 20px;
                height: 90%;
                /* left: -10px; */
            }

            .left,
            .right {
                position: relative;
                width: 100%;
                left: 0;
                top: 0;
                /* margin-bottom: 30px; */
            }

            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="date"],
            .form-group input[type="password"],
            .form-group select,
            .form-group textarea {
                width: 100%;
                max-width: 100%;
            }

            .form-group input[type="date"] {
                width: 92%;
            }

            .mpin-inputs {
                width: 100%;
            }

            .btn {
                position: relative;
                top: 0px;
            }
        }

        @media only screen and (min-width:300px) and (max-width:500px) {
            body {
                background-image: none;
                background-color: #435f75;
                margin: 0;
                padding: 0;
                height: 100%;
                overflow-y: scroll;
            }

            .container {
                width: 80%;
                position: relative;
                top: 20px;
                height: 90%;
                /* left: -10px; */
            }

            .left,
            .right {
                position: relative;
                width: 100%;
                left: 0;
                top: 0;
                /* margin-bottom: 30px; */
            }

            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="date"],
            .form-group input[type="password"],
            .form-group select,
            .form-group textarea {
                width: 100%;
                max-width: 100%;
            }

            .form-group input[type="date"] {
                width: 92%;
            }

            .mpin-inputs {
                width: 100%;
            }

            .btn {
                position: relative;
                top: 0px;
            }
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var conditionsMet = true;
            var conditionsRegex = [
                /.{8,}/, // Minimum 8 characters
                /[A-Z]/, // Minimum 1 uppercase letter
                /[a-z]/, // Minimum 1 lowercase letter
                /\d/, // Minimum 1 number
                /[!@#\$%\^&\*\(\)_\+{}\|:"<>\?]/ // Minimum 1 special character
            ];

            for (var i = 0; i < conditionsRegex.length; i++) {
                if (!password.match(conditionsRegex[i])) {
                    conditionsMet = false;
                    break;
                }
            }

            if (!conditionsMet) {
                var errorDiv = document.getElementById("password-error");
                errorDiv.style.display = "block";
                // Hide the error message after 3 seconds
                setTimeout(function() {
                    errorDiv.style.display = "none";
                }, 3000);

                // Prevent the form from being submitted
                return false;
            }

            return true; // Allow form submission if conditions are met
        }
    </script>

</head>

<body>
    <div id="password-error" class="error-message-password" style="display:none;">
        <span>❌</span>
        <p>Password must meet the specified criteria.</p>
    </div>
    <div class="container">
        <h2>Registration Form</h2>
        <div class="form-wrapper">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
                <div class="left">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" placeholder="Enter your Username" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" id="phone" name="phone" pattern="[0-9]{10}" title="Please enter exactly 10 digits." placeholder="Enter your Phone Number" oninput="validatePhoneNumber(this)" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Enter your E-mail" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" onfocus="showPasswordConditions()" oninput="checkPassword()" required autocomplete="new-password">
                        <div id="password-conditions" class="password-conditions" style="display: none;">
                            <ul>
                                <li id="length">Minimum 8 characters</li>
                                <li id="uppercase">Minimum 1 uppercase letter</li>
                                <li id="lowercase">Minimum 1 lowercase letter</li>
                                <li id="number">Minimum 1 number</li>
                                <li id="special">Minimum 1 special character</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" required autocomplete="off">
                    </div>
                </div>
                <div class="right">
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" rows="4" placeholder="Enter your Residential Address" required autocomplete="off"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="place">Place:</label>
                        <select id="place" name="place" required onchange="updateMainAreas()">
                            <option value="">Select a place</option>
                            <option value="Gobichettipalayam">Gobichettipalayam</option>
                            <option value="Sathyamangalam">Sathyamangalam</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="main_area">Main Area:</label>
                        <select name="main_area" id="main_area" required>
                            <option value="">Select Main Area:</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mpin">MPIN:</label>
                        <div class="mpin-inputs">
                            <input type="text" class="mpin-digit" maxlength="6" pattern="[0-9]{6}" placeholder="Enter Exactly 6 digits" title="Please enter exactly 6 digits." name="mpin" id="mpin" required autocomplete="off">
                        </div>
                    </div>
                    <div class=" form-group">
                        <p class="note-flash">NOTE: THE ABOVE MPIN IS FOR SECURITY PURPOSE,IT WILL BE ASKED FOR SECURITY REASONS. KINDLY NOTE IT DOWN.</p>
                    </div>
                </div>
                <input type="submit" name="submit" value="Register" class="btn">
            </form>
        </div>
        <?php if (isset($success_message)) { ?>
            <div class="toast success-message" id="success-message">
                <p><?php echo $success_message; ?></p>
            </div>
            <script>
                // Show toast messages
                setTimeout(function() {
                    var successMessage = document.querySelector('.toast.success-message');
                    if (successMessage) {
                        successMessage.style.display = 'block';
                    }
                }, 0);
                setTimeout(function() {
                    $("#success-message").fadeOut();
                }, 3000);
                // Redirect to index.php after 3 seconds
                setTimeout(function() {
                    window.location.href = '../index.php';
                }, 4000);
            </script>
        <?php } elseif (isset($warning_message)) { ?>
            <div class="toast warning-message">
                <p><?php echo $warning_message; ?></p>
            </div>
            <script>
                // Redirect to index.php after 3 seconds
                setTimeout(function() {
                    window.location.href = '../index.php';
                }, 4000);
            </script>
        <?php } elseif (isset($error_message)) { ?>
            <div class="toast error-message">
                <p><?php echo $error_message; ?></p>
            </div>
            <script>
                // Show toast messages
                setTimeout(function() {
                    var errorMessage = document.querySelector('.toast.error-message');
                    if (errorMessage) {
                        errorMessage.style.display = 'block';
                    }
                }, 0);
                // Redirect to index.php after 3 seconds
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000);
            </script>
        <?php } ?>
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
    <script>
        var mainAreasByLocation = {
            "Gobichettipalayam": [
                "-- SELECT YOUR NEARBY PLACE --", "Alingiam(gobi)", "Basuvanapuram", "Elathur Chettipalayam", "Erangattur",
                "Getticheyur", "Gobichettipalayam East", "Gobichettipalayam South", "Kallipatti",
                "Karattadipalayam", "Kasipalayam (erode)", "Kidarai", "Kodiveri",
                "Kolappalur (erode)", "Kummakalipalayam", "Nambiyur", "Nanjagoundenpalayam",
                "Pariyur Vellalapalayam", "Pattimaniakaranpalayam", "Perumugaipudur", "Pudukkaraipudur",
                "Pudupalayam (erode)", "Sakthinagar", "Sokkumaripalayam", "Suriappampalayam",
                "Theethampalayam", "Thuckanaickenpalayam"
            ],
            "Sathyamangalam": [
                "-- SELECT YOUR NEARBY PLACE --", "Araipalayam", "Ariyappampalayam", "Bannari", "Bhavanisagar", "Chikkahalli",
                "Dasappagoundanpudur", "Desipalayam", "Dhimbam", "Doddapura", "Germalam",
                "Gumtapuram", "Kalkadambur", "Karalayam", "Karapadi", "Kembanaickenpalayam",
                "Komarapalayam Sathy", "Kondapanaickenpalayam", "Kottuveerampalayam", "Nochikuttai",
                "Periyur", "Puduvadavalli", "Punjai Puliampatti", "Rangasamudram", "Sathy Bazaar",
                "Sathyamangalam", "Savakattupalayam", "Soosaipuram", "Talavadi", "Thingalur"
            ]
        };

        function updateMainAreas() {
            var locationSelect = document.getElementById("place");
            var mainAreaSelect = document.getElementById("main_area");
            // var otherAreaInput = document.getElementById("other_area");
            var selectedLocation = locationSelect.value;

            // Clear existing options
            mainAreaSelect.innerHTML = "";

            // Populate main areas based on selected location
            if (mainAreasByLocation[selectedLocation]) {
                mainAreasByLocation[selectedLocation].forEach(function(mainArea) {
                    var option = document.createElement("option");
                    option.value = mainArea;
                    option.text = mainArea;
                    mainAreaSelect.appendChild(option);
                });
            }

        }

        updateMainAreas();
    </script>
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
        var placeSelect = document.getElementById('main_area');
        var otherAreaDiv = document.getElementById('other_area');

            placeSelect.addEventListener('change', function() {
                if (placeSelect.value === 'Other') {
                    otherAreaDiv.style.display = 'block';
                } else {
                    otherAreaDiv.style.display = 'none';
                }
            });
        });
    </script> -->
    <script>
        function showPasswordConditions() {
            var passwordConditions = document.getElementById("password-conditions");
            passwordConditions.style.display = "block";
        }

        function hidePasswordConditions() {
            var passwordConditions = document.getElementById("password-conditions");
            passwordConditions.style.display = "none";
        }

        function checkPassword() {
            var password = document.getElementById("password").value;
            var conditions = document.getElementById("password-conditions").getElementsByTagName("li");

            var allConditionsMet = true;
            for (var i = 0; i < conditions.length; i++) {
                if (password.match(conditionsRegex[i])) {
                    conditions[i].style.textDecoration = "line-through";
                    conditions[i].style.color = "green";
                } else {
                    conditions[i].style.textDecoration = "none";
                    conditions[i].style.color = "red";
                    allConditionsMet = false;
                }
            }

            if (allConditionsMet) {
                hidePasswordConditions();
            }
        }
        // Define regular expressions for conditions
        var conditionsRegex = [
            /.{8,}/, // Minimum 8 characters
            /[A-Z]/, // Minimum 1 uppercase letter
            /[a-z]/, // Minimum 1 lowercase letter
            /\d/, // Minimum 1 number
            /[!@#\$%\^&\*\(\)_\+{}\|:"<>\?]/ // Minimum 1 special character
        ];

        document.addEventListener('click', function(e) {
            var passwordField = document.getElementById("password");
            var passwordConditions = document.getElementById("password-conditions");

            if (e.target !== passwordField && e.target !== passwordConditions) {
                hidePasswordConditions();
            }

        });

        // FOR MPIN
        document.addEventListener('DOMContentLoaded', function() {
            var mpinInput = document.getElementById('mpin');

            mpinInput.addEventListener('keypress', function(event) {
                var key = event.keyCode || event.charCode;
                if (key < 48 || key > 57) { // Check if the key pressed is not a number
                    event.preventDefault(); // Prevent the default action (typing the character)
                }
            });
        });
    </script>

</body>

</html>