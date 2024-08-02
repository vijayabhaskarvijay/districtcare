    <?php
    session_start();
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "urbanlink";
    $table = "";
    $userColumn = "";
    $dobColumn = "";
    $ageColumn = "";
    $phoneColumn = "";
    $placeColumn = "";
    $emailColumn = "";
    $passwordColumn = "";
    $addressColumn = "";
    $mainAreaColumn = "";
    $mpinColumn = "";

    // Check if user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    // Determine the user's table based on their location
    $location = strtolower($_SESSION['location']);
    if ($location === 'gobichettipalayam' || $location === 'gobi') {
        $table = "gobi_users";
        $userColumn = "gobi_user_name";
        $dobColumn = "gobi_user_dob";
        $ageColumn = "gobi_user_age";
        $phoneColumn = "gobi_user_phone_number";
        $placeColumn = "gobi_user_place";
        $emailColumn = "gobi_user_email";
        $passwordColumn = "gobi_user_password";
        $addressColumn = "gobi_user_address";
        $mainAreaColumn = "gobi_user_main_area";
        $mpinColumn = "gobi_user_mpin";
    } elseif ($location === 'sathyamangalam' || $location === 'sathy') {
        $table = "sathy_users";
        $userColumn = "sathy_user_name";
        $dobColumn = "sathy_user_dob";
        $ageColumn = "sathy_user_age";
        $phoneColumn = "sathy_user_phone_number";
        $placeColumn = "sathy_user_place";
        $emailColumn = "sathy_user_email";
        $passwordColumn = "sathy_user_password";
        $addressColumn = "sathy_user_address";
        $mainAreaColumn = "sathy_user_main_area";
        $mpinColumn = "sathy_user_mpin";
    } else {
        echo "<div class='error'>Invalid location.</div>";
        exit();
    }

    // Connect to the database
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve user information from the appropriate table
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM $table WHERE $userColumn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user = $row[$userColumn];
        $dob = $row[$dobColumn];
        $age = $row[$ageColumn];
        $phone = $row[$phoneColumn];
        $place = $row[$placeColumn];
        $email = $row[$emailColumn];
        $password = $row[$passwordColumn];
        $address = $row[$addressColumn];
        $mainArea = $row[$mainAreaColumn];
        $mpin = $row[$mpinColumn];
    } else {
        echo "<div class='error'>User not found.</div>";
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update the user's information in the appropriate table
        $user = $_POST['user'];
        $dob = $_POST['dob'];
        $age = $_POST['age'];
        $phone = $_POST['phone'];
        // $place = $_POST['place'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $address = $_POST['address'];
        // $mainArea = $_POST['main_area'];
        $mpin = $_POST['mpin'];

        // $sql = "UPDATE $table SET $userColumn = ?, $dobColumn = ?, $ageColumn = ?, $phoneColumn = ?, $placeColumn = ?, $emailColumn = ?, $passwordColumn = ?, $addressColumn = ?, $mainAreaColumn = ?,$mpinColumn=? WHERE $userColumn = ?";
        $sql = "UPDATE $table SET $userColumn = ?, $dobColumn = ?, $ageColumn = ?, $phoneColumn = ?, $emailColumn = ?, $passwordColumn = ?, $addressColumn = ?, $mpinColumn=? WHERE $userColumn = ?";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("sssssssss", $user, $dob, $age, $phone, $email, $password, $address, $mpin, $username);
        // $stmt->bind_param("sssssssssss", $user, $dob, $age, $phone, $place, $email, $password, $address, $mainArea, $mpin, $username);

        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<div class='success'>User information updated successfully.</div>";
        } else {
            echo "<div class='error'>Failed to update user information.</div>";
        }
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage User Profile</title>
        <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <style>
            body {
                background-image: url("../images/15694619_5595285.jpg");
                background-size: cover;
                background-position: 0 -140px;
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
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
                top: 30px;
                margin-left: 10px;
            }

            .container {
                width: 1000px;
                height: 500px;
                margin: 30px auto;
                padding: 20px;
                background-color: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .section-left {
                width: 350px;
                position: relative;
                top: 30px;
                left: 50px;
                float: left;
            }

            .section-right {
                width: 350px;
                position: relative;
                top: 30px;
                left: -120px;
                float: right;
            }

            h2 {
                text-align: center;
                color: #333;
            }

            .form-group {
                margin-bottom: 15px;
            }

            label {
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
            }

            input[type="text"],
            input[type="date"],
            input[type="email"],
            input[type="password"],
            select {
                width: 100%;
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #ccc;
            }

            .password-input {
                position: relative;
            }

            .toggle-password {
                position: absolute;
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
                color: #aaa;
                cursor: pointer;
            }

            .toggle-mpin {
                position: relative;
                top: -20px;
                left: 90%;
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


            input[type="submit"] {
                width: 100%;
                background-color: #4caf50;
                color: #fff;
                padding: 10px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .success {
                background-color: #d4edda;
                color: #155724;
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 5px;
            }

            .error {
                background-color: #f8d7da;
                color: #721c24;
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 5px;
            }

            @media only screen and (min-width:1101px) {
                .back-button {
                    position: relative;
                    top: 5px;
                    border-radius: 10px;
                    left: -5px;
                }
            }

            /* 901 to 110 BELOW */

            @media only screen and (min-width:901px) and (max-width:1100px) {

                body {
                    background-position: -250px;
                    background-size: cover;
                    background-repeat: no-repeat;
                }

                .btn {
                    box-sizing: border-box;
                    position: relative;
                    width: 80%;
                }

                .back-button {
                    position: relative;
                    top: 10px;
                    left: -5px;
                }

                .go-back {
                    padding: 5px;
                    text-align: center;
                    background-color: orange;
                    cursor: pointer;
                    color: white;
                    text-decoration: none;
                    border: 2px solid white;
                    border-radius: 5px;
                    height: 50px;
                    width: 50px;
                }

                .container {
                    width: 70%;
                    height: auto;
                    margin: 20px;
                    padding: 10px;
                    position: relative;
                    top: 10px;
                    left: 10%;
                    box-shadow: 0px 0px 20px rgba(0, 0, 0, 1);
                }


                .section-right {
                    width: 70%;
                    float: none;
                    position: relative;
                    top: -10%;
                    left: 10%;
                }

                .section-left {
                    width: 70%;
                    float: none;
                    position: relative;
                    top: -10%;
                    left: 10%;
                }

                .section-right {
                    margin-top: 20px;
                }

                h2,
                label {
                    color: #721c24;
                }
            }

            /* 701 to 900 BELOW */

            @media only screen and (min-width:701px) and (max-width:900px) {

                body {
                    background-position: -250px;
                    background-size: cover;
                    background-repeat: no-repeat;
                }

                .btn {
                    box-sizing: border-box;
                    position: relative;
                    width: 80%;
                }

                .back-button {
                    position: relative;
                    top: 10px;
                    left: -5px;
                }

                .go-back {
                    padding: 5px;
                    text-align: center;
                    background-color: orange;
                    cursor: pointer;
                    color: white;
                    text-decoration: none;
                    border: 2px solid white;
                    border-radius: 5px;
                    height: 50px;
                    width: 50px;
                }

                .container {
                    width: 70%;
                    height: auto;
                    margin: 20px;
                    padding: 10px;
                    position: relative;
                    top: 10px;
                    left: 10%;
                    box-shadow: 0px 0px 20px rgba(0, 0, 0, 1);
                }


                .section-right {
                    width: 70%;
                    float: none;
                    position: relative;
                    top: -10%;
                    left: 10%;
                }

                .section-left {
                    width: 70%;
                    float: none;
                    position: relative;
                    top: -10%;
                    left: 10%;
                }

                .section-right {
                    margin-top: 20px;
                }

                h2,
                label {
                    color: #721c24;
                }
            }

            @media only screen and (min-width:501px) and (max-width:700px) {

                body {
                    background-image: none;
                    background-color: #aaa;
                }

                .btn {
                    box-sizing: border-box;
                    position: relative;
                    width: 80%;
                }

                .back-button {
                    position: relative;
                    top: 10px;
                    left: -5px;
                }

                .go-back {
                    padding: 5px;
                    text-align: center;
                    background-color: orange;
                    cursor: pointer;
                    color: white;
                    text-decoration: none;
                    border: 2px solid white;
                    border-radius: 5px;
                    height: 50px;
                    width: 50px;
                }

                .container {
                    width: 70%;
                    height: auto;
                    margin: 20px;
                    padding: 10px;
                    position: relative;
                    top: 10px;
                    left: 10%;
                    box-shadow: 0px 0px 20px rgba(0, 0, 0, 1);
                }


                .section-right {
                    width: 70%;
                    float: none;
                    position: relative;
                    top: -10%;
                    left: 10%;
                }

                .section-left {
                    width: 70%;
                    float: none;
                    position: relative;
                    top: -10%;
                    left: 10%;
                }

                .section-right {
                    margin-top: 20px;
                }

                h2,
                label {
                    color: #721c24;
                }
            }

            /* 300 to 500 BELOW */

            @media only screen and (min-width:300px) and (max-width:500px) {

                body {
                    background-image: none;
                    background-color: white;
                    margin: 0;
                    padding: 0;
                    width: 100%;
                    height: 100% !important;
                    overflow-y: scroll !important;
                }

                .btn {
                    box-sizing: border-box;
                    position: relative;
                    width: 80%;
                }

                .back-button {
                    position: relative;
                    top: 10px;
                    left: -5px;
                }

                .go-back {
                    padding: 5px;
                    text-align: center;
                    background-color: orange;
                    cursor: pointer;
                    color: white;
                    text-decoration: none;
                    border: 2px solid white;
                    border-radius: 5px;
                    height: 50px;
                    width: 50px;
                }

                .container {
                    width: 85%;
                    height: 100%;
                    padding: 10px;
                    position: relative;
                    top: 5%;
                    background-color: #aaa;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
                }

                .section-left,
                .section-right {
                    width: 80%;
                    position: relative;
                    left: 7%;
                    top: 0;
                    float: none;
                }

                .form-group {
                    margin-bottom: 20px;
                    margin-top: 20px;
                }

                .section-right {
                    margin-top: 20px;
                }

                h2,
                label {
                    color: #0088cc;
                }

                .btn {
                    position: relative;
                    left: 10px;
                }
            }
        </style>
    </head>

    <body>

        <div class="back-button">
            <a href="public_user_landing.php" class="go-back">⬅️ GO BACK</a>
        </div>

        <div class="container">
            <h2>Manage User Profile</h2>
            <div class="section-left">
                <form method="POST">
                    <div class="form-group">
                        <label for="user">User</label>
                        <input type="text" id="user" name="user" value="<?php echo $user; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="<?php echo $dob; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="text" id="age" name="age" value="<?php echo $age; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" pattern="[0-9]{10}" value="<?php echo $phone; ?>" oninput="validatePhoneNumber(this)" required>
                    </div>
                    <!-- <div class="form-group">
                        <label for="place">Place:</label>
                        <select id="place" name="place" required onchange="updateMainAreas()">
                            <option value="">Select a place</option>
                            <option value="Gobichettipalayam">Gobichettipalayam</option>
                            <option value="Sathyamangalam">Sathyamangalam</option>
                        </select>
                    </div> -->

            </div>
            <div class="section-right">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="email" value="<?php echo $email; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" value="<?php echo $password; ?>" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo $address; ?>" required>
                </div>
                <!-- <div class="form-group">
                    <label for="main_area">Main Area:</label>
                    <select name="main_area" id="main_area" required>
                        <option value="">Select Main Area:</option>
                    </select>
                </div>
                -->
                <div class="form-group">
                    <div class="mpin-inputs">
                        <label for="mpin">MPIN:</label>
                        <input type="password" class="mpin-digit" maxlength="6" name="mpin" id="mpin" value="<?php echo $mpin; ?>" required>
                        <i class="fas fa-eye toggle-mpin"></i>
                    </div>
                </div>
                <input type="submit" class="btn" value="Update">
                </form>
            </div>
        </div>

        <script>
            const passwordInput = document.getElementById('password');
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