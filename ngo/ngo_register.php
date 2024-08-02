    <?php
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get the form data
        $ngo_user_name = $_POST["ngo_user_name"];
        $ngo_user_position = $_POST["ngo_user_position"];
        $ngo_user_phone = $_POST["ngo_user_phone"];
        $ngo_user_email = $_POST["ngo_user_email"];
        $ngo_user_pwd = $_POST["ngo_user_pwd"];
        $ngo_org_name = $_POST["ngo_org_name"];
        $ngo_org_place = $_POST["ngo_org_place"];
        $ngo_org_phone = $_POST["ngo_org_phone"];
        $ngo_org_mail = $_POST["ngo_org_mail"];
        $ngo_mpin = $_POST["mpin"];

        // Handle image upload
        if (!empty($_FILES["ngo_id_image"]["tmp_name"])) {
            $imageData = addslashes(file_get_contents($_FILES["ngo_id_image"]["tmp_name"]));
        } else {
            $imageData = null;
        }

        // Create a connection to the database
        $conn = new mysqli("localhost", "root", "", "urbanlink");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Generate a unique ID
        $ngo_id = uniqid();

        // Prepare and execute the SQL statement to insert the data
        $sql = "INSERT INTO ngo_details (ngo_id, ngo_user_name, ngo_user_position, ngo_user_phone, ngo_user_email, ngo_user_pwd, ngo_org_name, ngo_org_place, ngo_org_phone, ngo_org_mail, ngo_id_image,ngo_user_mpin) VALUES ('$ngo_id', '$ngo_user_name', '$ngo_user_position', '$ngo_user_phone', '$ngo_user_email', '$ngo_user_pwd', '$ngo_org_name', '$ngo_org_place', '$ngo_org_phone', '$ngo_org_mail', '$imageData','$ngo_mpin')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful! Account Approval is Pending,will be updated shortly within 2 hours.Kindly Wait.');</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the database connection
        $conn->close();
    }
    ?>
    <!DOCTYPE html>
    <html>
    <!-- https://localhost/districtcare/public/main_login.php -->

    <head>
        <title>NGO Registration</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
        <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
        <style>
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

            .password-conditions {
                position: absolute;
                background: white;
                border: 2px solid #ccc;
                padding: 10px;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                color: red;
            }

            .password-conditions ul {
                list-style: none;
                padding: 0;
            }

            body {
                background-image: url("../images/ngo_register_bg.jpg");
                background-size: cover;
                background-repeat: no-repeat;
                font-family: 'Roboto Slab', sans-serif;

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
                top: 10px;
                margin-left: 10px;
            }

            .container {
                position: relative;
                top: 150px;
                display: flex;
                align-items: center;
                height: 100vh;
            }


            .form-container {
                position: relative;
                padding: 0px 20px 0px 20px;
                background-color: rgba(255, 255, 255, 0.9);
                border-radius: 10px;
                box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.9);
                width: 550px;
                margin-left: auto;
                margin-right: auto;
                margin-bottom: 3px;
                top: 30px;


            }

            .form-group {
                margin-bottom: 20px;
            }

            li:nth-child(even) {
                color: #ffa500;
            }

            .form-group label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .form-group input {
                width: 100%;
                padding: 8px;
                border: none;
                border-bottom: 2px solid black;
                font-size: 16px;
                /* border-radius: 5px;3 */
                box-sizing: border-box;

            }

            .form-group select {
                width: 100%;
                padding: 8px;
                border: 2px solid black;
                font-size: 16px;
                border-radius: 5px;
                box-sizing: border-box;

            }

            .form-group input[type="submit"] {
                background-color: #ffa500;
                border-radius: 5px;
                color: white;
                cursor: pointer;
                border: none !important;
                width: 80%;
                transition: 0.3s ease-in;
                background-image: linear-gradient(to right, #0cc91c 45%, orange 55%);
                background-size: 220% 100%;
                background-position: 100% 50%;

                &:hover {
                    background-position: 0% 50%;
                }
            }

            .form-group input[type="submit"]:hover {
                width: 100%;
                /* background-color: #37ff00; */

            }

            .goback {
                padding: 10px;
                text-align: center;
                background-color: #ffa500;
                color: white;
                cursor: pointer;
                width: 100px;
                margin-bottom: 20px;
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



            @media only screen and (min-width:901px) and (max-width: 1100px) {
                body {
                    background-position: -20px 400px;
                    background-size: cover;
                    margin: 0;
                    padding: 0;
                    overflow-y: scroll;
                    width: 100%;
                }

                .instructions {
                    height: 720px;
                    width: 300px;
                    position: relative;
                    top: 90px;
                    /* left: 50px; */
                }

                .form-container {
                    height: 1100px;
                    width: 450px;
                    position: relative;
                    top: 150px;
                    left: 20px;
                }
            }

            @media only screen and (min-width:701px) and (max-width: 900px) {
                body {
                    background-position: -20px 400px;
                    background-size: cover;
                    margin: 0;
                    padding: 0;
                    overflow-y: scroll;
                    width: 100%;
                }

                .form-container {
                    width: 60%;
                    position: relative;
                    top: 180px;
                    height: 1150px;
                    margin: 50px;
                    left: 120px;
                }

                .form-group {
                    position: relative;
                    left: 50px;
                }

                .form-group input[type="text"],
                .form-group input[type="email"],
                .form-group input[type="date"],
                .form-group input[type="password"],
                .form-group select,
                .form-group textarea {
                    width: 80%;
                    /* max-width: 100%; */
                }

                .form-group input[type="file"] {
                    width: 80%;
                }

                .mpin-inputs {
                    width: 100%;
                }

                .btn,
                .note-flash {
                    position: relative;
                    left: -50px;
                }

                .instructions {
                    display: none;
                }
            }


            @media only screen and (min-width:501px) and (max-width:700px) {
                body {
                    background-position: -100px 200px;
                    background-size: cover;
                    margin: 0;
                    padding: 0;
                    /* height: 100%; */
                    overflow-y: scroll;
                    width: 100%;
                }

                .form-container {
                    width: 85%;
                    position: relative;
                    top: 180px;
                    height: 1150px;
                    margin: 50px;
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

                .instructions {
                    display: none;
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
                    width: 100%;
                }

                .form-container {
                    width: 85%;
                    position: relative;
                    top: 180px;
                    height: 1140px;
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

                .instructions {
                    display: none;
                }
            }
        </style>
        <script>
            function confirmRegistration() {
                return confirm("Are you sure you want to register?");
            }
        </script>
        <script>
            function validateForm() {
                var password = document.getElementById("ngo_user_pwd").value;
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
        <!-- <input class="goback" type="button" value="Go Back" onclick="history.back()"> -->
        <div class="back-button">
            <a href="../index.php" class="go-back">⬅️ GO BACK</a>
        </div>

        <div class="container">
            <div class="form-container">
                <script>
                    function combinedSubmit() {
                        if (confirmRegistration() && validateForm()) {
                            return true; // If both functions return true, the form will be submitted
                        } else {
                            return false; // If any function returns false, the form submission will be cancelled
                        }
                    }
                </script>
                <h2 style="text-align: center;">NGO Registration</h2>
                <form method="POST" action="ngo_register.php" onsubmit="return combinedSubmit()" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="ngo_user_name">User Name *:</label>
                        <input type="text" id="ngo_user_name" name="ngo_user_name" placeholder="Enter your Username" required>
                    </div>
                    <div class="form-group">
                        <label for="ngo_user_position">User Position *:</label>
                        <input type="text" id="ngo_user_position" name="ngo_user_position" placeholder="Enter your Designation" required>
                    </div>
                    <div class="form-group">
                        <label for="ngo_user_phone">User Phone *:</label>
                        <input type="text" id="ngo_user_phone" name="ngo_user_phone" pattern="[0-9]{10}" title="Please enter exactly 10 digits." placeholder="Enter your Phone Number" oninput="validatePhoneNumber(this)" required>
                    </div>
                    <div class="form-group">
                        <label for="ngo_user_email">User Email *:</label>
                        <input type="email" id="ngo_user_email" name="ngo_user_email" placeholder="Enter your E-mail" required>
                    </div>
                    <div class="form-group">
                        <label for="ngo_user_pwd">User Password *:</label>
                        <input type="password" id="ngo_user_pwd" name="ngo_user_pwd" onfocus="showPasswordConditions()" oninput="checkPassword()" placeholder="Set Your password" required>
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
                        <label for="ngo_org_name">Organization Name *:</label>
                        <input type="text" id="ngo_org_name" name="ngo_org_name" placeholder="Enter your Organization Name" required>
                    </div>
                    <div class="form-group">
                        <label for="ngo_org_place">Organization Place *:</label>
                        <select id="ngo_org_place" name="ngo_org_place" required>
                            <option value="">Select Location</option>
                            <option value="Gobichettipalayam">Gobichettipalayam</option>
                            <option value="Sathyamangalam">Sathyamangalam</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ngo_org_phone">Organization Phone *:</label>
                        <input type="text" id="ngo_org_phone" name="ngo_org_phone" placeholder="Enter your Org Phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required>
                    </div>
                    <div class="form-group">
                        <label for="ngo_org_mail">Organization Email *:</label>
                        <input type="email" id="ngo_org_mail" name="ngo_org_mail" placeholder="Enter your Org E-mail" required>
                    </div>
                    <div class="form-group">
                        <label for="ngo_id_image">NGO ID Image *:</label>
                        <input type="file" id="ngo_id_image" name="ngo_id_image" accept=".jpg, .jpeg, .png">
                    </div>
                    <div class="form-group">
                        <label for="mpin">MPIN *:</label>
                        <div class="mpin-inputs">
                            <input type="text" class="mpin-digit" maxlength="6" pattern="[0-9]{6}" name="mpin" id="mpin" title="Please enter exactly 6 digits." placeholder="Enter Exactly 6 digits" title="Please enter exactly 6 digits." required>
                        </div>
                    </div>
                    <div class="form-group">
                        <p class="note-flash">NOTE: THE ABOVE MPIN IS FOR SECURITY PURPOSE,IT WILL BE ASKED FOR SECURITY REASONS. KINDLY NOTE IT DOWN.</p>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn" value="Register">
                    </div>
                </form>
                <div id="password-error" class="error-message-password" style="display:none;">
                    <span>❌</span>
                    <p>Password must meet the specified criteria.</p>
                </div>
            </div>
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
            function showPasswordConditions() {
                var passwordConditions = document.getElementById("password-conditions");
                passwordConditions.style.display = "block";
            }

            function hidePasswordConditions() {
                var passwordConditions = document.getElementById("password-conditions");
                passwordConditions.style.display = "none";
            }

            function checkPassword() {
                var password = document.getElementById("ngo_user_pwd").value;
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
                var passwordField = document.getElementById("ngo_user_pwd");
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