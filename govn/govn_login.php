    <?php
    session_start(); // Start the session

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "urbanlink";

    $conn = mysqli_connect($host, $username, $password, $database);

    if (isset($_GET['error']) && $_GET['error'] === 'not_logged_in') {
        echo '<p style="color: red;">You must be logged in to access this page.</p>';
    }


    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['department']) && isset($_POST['place'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $department = $_POST['department'];
        $place = $_POST['place'];

        $query = "SELECT * FROM govn_staff_details WHERE govn_staff_name = '$username' AND govn_staff_email = '$email' AND govn_staff_password = '$password' AND govn_staff_work_dept = '$department' AND govn_staff_location = '$place'";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            // Successful verification
            $row = mysqli_fetch_assoc($result);

            $_SESSION['sv_gvn_staffname'] = $row['govn_staff_name'];
            $_SESSION['sv_gvn_staffemail'] = $row['govn_staff_email'];
            $_SESSION['sv_gvn_staffloc'] = $row['govn_staff_location'];
            $_SESSION['sv_gvn_staffid'] = $row['govn_staff_id'];
            $_SESSION['sv_gvn_staffphone'] = $row['govn_staff_phone'];
            $_SESSION['sv_gvn_staffdept'] = $row['govn_staff_work_dept']; // Save department in session

            header("Location: govn_landing.php");
            exit();
        } else {
            // Error message
            $error = "Invalid credentials. Please try again.";
        }
    }
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Government Login</title>
        <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                font-family: 'Roboto Slab', sans-serif;
                margin: 0;
                padding: 0;
                background-image: url("../images/checkered-pattern.png"), linear-gradient(to right top, white, white);
                height: 87vh;
                overflow: hidden;
            }

            body::-webkit-scrollbar {
                display: none;
            }


            .container {
                position: absolute;
                background-color: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                left: 50%;
                top: 35%;
                display: grid;
                grid-template-columns: repeat(2, 50%);
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 900px;
                margin: 100px auto;
                padding: 20px;
                border-radius: 20px;
                box-shadow: 10px 10px 10px rgba(0, 0, 0, 1);
                height: 550px;

            }

            .toggle-password {
                position: absolute;
                top: 70%;
                right: 50px;
                transform: translateY(-50%);
                color: white;
                background-color: pink;
                cursor: pointer;
                padding: 5px;
                border-radius: 10px;
            }

            .toggle-password:hover {
                color: #555;
            }

            img {
                float: right;
            }

            h2 {
                text-align: center;
                color: #333333;
                margin-bottom: 20px;
                position: relative;
                /* top: -10px; */
                left: 220px;
            }

            .form-group {
                margin-bottom: 15px;
            }

            label {
                font-weight: normal;
                display: block;
                margin-bottom: 2px;
                margin-top: 10px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            select {

                width: 80%;
                padding: 10px;
                border-radius: 3px;
                border: none;
                border-bottom: 2px solid #ccc;
                outline: none;
            }

            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="password"]:focus,
            select:focus {
                border-bottom: 2px solid #0088cc;
            }


            input[type="submit"] {
                width: 80%;
                padding: 10px;
                background-color: lightseagreen;
                color: #ffffff;
                border: none;
                cursor: pointer;
                border-radius: 3px;
                /* margin-top: 30px; */
            }

            input[type="submit"]:hover {
                background-color: green;
                transition: 0.2s ease-in;
            }

            .loginpic {
                position: relative;
                height: 300px;
                width: 300px;
                left: 480px;
                top: -300px;
                border-radius: 10px;
            }

            .posi {
                position: relative;
                left: -370px;
                top: 50px;
                margin-bottom: 10px;
            }

            .error {
                color: #FF0000;
                margin-bottom: 10px;
            }

            .success {
                color: #008000;
                margin-bottom: 10px;
            }

            @media only screen and (min-width: 701px) and (max-width: 900px) {

                body {
                    width: 100%;
                    overflow-y: scroll;
                    height: 100vh;
                }

                .container {
                    grid-template-columns: 80%;
                    width: 50%;
                    height: 750px;
                    margin: 20px 0px;
                    position: relative;
                    top: 380px;
                    align-items: center;
                    justify-content: center;
                    border-radius: 20px;
                }

                h2 {
                    top: -20px;
                    left: 0;
                    text-align: center;
                    /* Center the heading */
                }


                .posi {
                    top: 150px;
                    position: relative;
                    left: 20px;
                    width: 90%;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                select {
                    width: 100%;
                }

                input[type="submit"] {
                    width: 100%;
                    margin-top: 20px;
                    /* Adjust margin for better spacing */
                }

                img.loginpic {
                    position: relative;
                    top: -500px;
                    left: 50px;
                    height: 200px;
                    width: 200px;
                    z-index: 1;
                    align-items: center;
                }
            }

            @media only screen and (min-width: 501px) and (max-width: 700px) {

                body {
                    width: 100%;
                    overflow-y: scroll;
                    height: 100vh;
                }

                .container {
                    grid-template-columns: 80%;
                    width: 80%;
                    height: 750px;
                    margin: 20px 0px;
                    position: relative;
                    top: 380px;
                    align-items: center;
                    justify-content: center;
                    border-radius: 20px;
                }

                h2 {
                    top: -20px;
                    left: 0;
                    text-align: center;
                    /* Center the heading */
                }


                .posi {
                    top: 150px;
                    position: relative;
                    left: -10px;
                    width: 110%;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                select {
                    width: 100%;
                    /* Make inputs fill the container */
                }

                input[type="submit"] {
                    width: 100%;
                    margin-top: 20px;
                    /* Adjust margin for better spacing */
                }

                img.loginpic {
                    position: relative;
                    top: -500px;
                    left: 50px;
                    height: 200px;
                    width: 200px;
                    z-index: 1;
                    align-items: center;
                }
            }

            @media only screen and (min-width: 300px) and (max-width: 500px) {

                body {
                    width: 100%;
                    overflow-y: scroll;
                    height: 100vh;
                }

                .container {
                    grid-template-columns: 80%;
                    width: 80%;
                    height: 750px;
                    margin: 20px 0px;
                    position: relative;
                    top: 380px;
                    align-items: center;
                    justify-content: center;
                    border-radius: 20px;
                }

                h2 {
                    top: -20px;
                    left: 0;
                    text-align: center;
                    /* Center the heading */
                }


                .posi {
                    top: 150px;
                    position: relative;
                    left: -10px;
                    width: 110%;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                select {
                    width: 100%;
                    /* Make inputs fill the container */
                }

                input[type="submit"] {
                    width: 100%;
                    margin-top: 20px;
                    /* Adjust margin for better spacing */
                }

                img.loginpic {
                    position: relative;
                    top: -500px;
                    left: -10px;
                    height: 200px;
                    width: 200px;
                    z-index: 1;
                }
            }
        </style>
    </head>

    <body>
        <div class="container">
            <h2>Government Login</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="posi">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="posi">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your user-email" required>
                </div>
                <div class="posi">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye toggle-password"></i>
                </div>
                <div class="posi">
                    <label for="department">Department:</label>
                    <select id="department" name="department" required>
                        <option class=".select-options" value="">Select Department</option>
                        <option class=".select-options" value="Revenue Department">Revenue Department</option>
                        <option class=".select-options" value="Public Works Department (PWD)">Public Works Department (PWD)</option>
                        <option class=".select-options" value="Municipal Corporation">Municipal Corporation</option>
                        <option class=".select-options" value="Health Department">Health Department</option>
                        <option class=".select-options" value="Education Department">Education Department</option>
                        <option class=".select-options" value="Agriculture Department">Agriculture Department</option>
                        <option class=".select-options" value="Police Department">Police Department</option>
                        <option class=".select-options" value="Fire and Rescue Services Department">Fire and Rescue Services Department</option>
                        <option class=".select-options" value="Social Welfare Department">Social Welfare Department</option>
                        <option class=".select-options" value="Rural Development Department">Rural Development Department</option>
                        <option class=".select-options" value="Forest Department">Forest Department</option>
                        <option class=".select-options" value="Animal Husbandry Department">Animal Husbandry Department</option>
                        <option class=".select-options" value="Town Planning Department">Town Planning Department</option>
                        <option class=".select-options" value="Electricity Department">Electricity Department</option>
                        <option class=".select-options" value="Water Supply and Sanitation Department">Water Supply and Sanitation Department</option>
                        <option class=".select-options" value="Tax Department (Income Tax, Sales Tax, etc.)">Tax Department (Income Tax, Sales Tax, etc.)</option>
                        <option class=".select-options" value="Public Distribution System (PDS) Department">Public Distribution System (PDS) Department</option>
                        <option class=".select-options" value="Information and Public Relations Department">Information and Public Relations Department</option>
                    </select>
                </div>
                <div class="posi">
                    <label for="place">Place:</label>
                    <select id="place" name="place" required>
                        <option class=".select-options" value="">Select Place</option>
                        <option class=".select-options" value="Gobichettipalayam">Gobichettipalayam</option>
                        <option class=".select-options" value="Sathyamangalam">Sathyamangalam</option>
                    </select>
                </div>

                <div class="posi pwd">
                    <a href="govn_forgot_pwd.php">Forgot Password?</a>
                </div>
                <div class="posi">
                    <input type="submit" value="Login">
                </div>
            </form>
            <img class="loginpic" src="../images/Login-Register.png">
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
    </body>

    </html>