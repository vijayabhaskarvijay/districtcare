    <?php
    session_start(); // Start the session

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "urbanlink";

    $conn = mysqli_connect($host, $username, $password, $database);

    if (isset($_POST['username']) && isset($_POST['user_email']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $user_email = $_POST['user_email'];
        $password = $_POST['password'];

        $query = "SELECT * FROM admin_details WHERE admin_name = '$username' AND admin_email = '$user_email' AND admin_pass = '$password'";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            // Successful verification
            $row = mysqli_fetch_assoc($result);

            $_SESSION['sv_admin_id'] = $row['admin_id'];
            $_SESSION['sv_admin_username'] = $row['admin_name'];
            $_SESSION['sv_admin_email'] = $row['admin_email'];
            $_SESSION['sv_admin_phone'] = $row['admin_phone_number'];

            header("Location: admin_landing.php");
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
        <title>Admin Login</title>
        <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-image: url("../images/checkered-pattern.png"), linear-gradient(to right top, rgba(255, 255, 255, 0.19), rgba(255, 255, 255, 0.8));
                /* background-color: #435f75; */
                height: 87vh;
                overflow: hidden;
            }

            body::-webkit-scrollbar {
                display: none;
            }

            .container {
                position: absolute;
                background-color: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(20px);
                left: 35%;
                top: 50px;
                display: flex;
                flex-direction: column;
                width: 500px;
                margin: 100px auto;
                padding: 20px;
                border-radius: 20px;
                box-shadow: 0px 0px 20px rgba(0, 0, 0, 1);
                height: 400px;
            }

            h2 {
                text-align: center;
                color: #333333;
                margin-bottom: 20px;
                position: relative;
                /* top: -10px; */
                /* left: 220px; */
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
                width: 350px;
                padding: 10px;
                border-radius: 3px;
                border: 1px solid #cccccc;
            }

            input[type="submit"] {
                width: 350px;
                padding: 10px;
                background-color: lightseagreen;
                color: #ffffff;
                border: none;
                cursor: pointer;
                border-radius: 3px;
                margin-top: 30px;
            }

            input[type="submit"]:hover {
                background-color: green;
                color: #ffffff;
                transition: 0.2s ease-in;
            }

            .posi {
                position: relative;
                left: 30px;
                /* top: 50px; */
            }

            .error {
                color: #FF0000;
                margin-bottom: 10px;
            }

            .success {
                color: #008000;
                margin-bottom: 10px;
            }

            @media only screen and (min-width: 701px) and (max-width: 500px) {

                body {
                    width: 100%;
                }

                .container {
                    grid-template-columns: 80%;
                    width: 80%;
                    height: 550px;
                    margin: 20px 0px;
                    position: relative;
                    top: 280px;
                    align-items: center;
                    justify-content: center;
                    border-radius: 20px;
                    align-items: flex-start;
                }

                h2 {
                    top: -20px;
                    z-index: 1;
                    left: 0;
                    text-align: center;
                    /* Center the heading */
                }


                .posi {
                    position: relative;
                    top: 40px;
                    left: 5px;
                    width: 110%;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                select {
                    width: 80%;
                    /* Make inputs fill the container */
                }

                input[type="submit"] {
                    width: 80%;
                    margin-top: 20px;
                    /* Adjust margin for better spacing */
                }

                img.loginpic {
                    position: relative;
                    top: -500px;
                    left: -30%;
                    height: 180px;
                    width: 180px;
                    z-index: 1;
                }
            }

            @media only screen and (min-width: 701px) and (max-width: 900px) {
                body {
                    width: 100%;
                }

                .container {
                    width: 60%;
                    height: 400px;
                    position: relative;
                    top: 0px;
                    left: 0px;
                    border-radius: 20px;
                    align-items: flex-start;
                }

                h2 {
                    left: 120px;
                    color: turquoise;
                    border: none;

                }


                .posi {
                    position: relative;
                    top: 0px;
                    left: 25px;
                    width: 90%;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                select {
                    width: 90%;
                    /* Make inputs fill the container */
                }

                input[type="submit"] {
                    width: 90%;
                    margin-top: 20px;
                    /* Adjust margin for better spacing */
                }
            }

            @media only screen and (min-width: 601px) and (max-width: 700px) {

                body {
                    width: 100%;
                }

                .container {
                    width: 60%;
                    height: 400px;
                    position: relative;
                    top: 30px;
                    left: 140px;
                    border-radius: 20px;
                    align-items: flex-start;
                }

                h2 {
                    left: 120px;
                    color: turquoise;
                    border: none;

                }


                .posi {
                    position: relative;
                    top: 0px;
                    left: 25px;
                    width: 90%;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                select {
                    width: 90%;
                    /* Make inputs fill the container */
                }

                input[type="submit"] {
                    width: 90%;
                    margin-top: 20px;
                    /* Adjust margin for better spacing */
                }
            }

            @media only screen and (min-width: 501px) and (max-width: 600px) {
                body {
                    width: 100%;
                }

                .container {
                    width: 80%;
                    height: 450px;
                    /* margin: 20px 0px; */
                    position: relative;
                    top: -30px;
                    left: 0px;
                    border-radius: 20px;
                    align-items: flex-start;
                }

                h2 {
                    left: 120px;
                    color: turquoise;
                    border: none;

                }


                .posi {
                    position: relative;
                    top: 0px;
                    left: 25px;
                    width: 90%;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                select {
                    width: 90%;
                    /* Make inputs fill the container */
                }

                input[type="submit"] {
                    width: 90%;
                    margin-top: 20px;
                    /* Adjust margin for better spacing */
                }
            }

            @media only screen and (min-width: 300px) and (max-width: 500px) {

                body {
                    width: 100%;
                }

                .container {
                    width: 90%;
                    height: 450px;
                    /* margin: 20px 0px; */
                    position: relative;
                    top: -50px;
                    left: 0px;
                    border-radius: 20px;
                    align-items: flex-start;
                }

                h2 {
                    /* top: -100px; */
                    z-index: 1;
                    left: 100px;
                    text-align: center;
                    /* Center the heading */
                }


                .posi {
                    position: relative;
                    /* top: 40px; */
                    left: 25px;
                    width: 90%;
                    margin-bottom: 20px;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                select {
                    width: 90%;
                    /* Make inputs fill the container */
                }

                input[type="submit"] {
                    width: 90%;
                    margin-top: 20px;
                    /* Adjust margin for better spacing */
                }

            }
        </style>
    </head>

    <body>
        <div class="container">
            <h2>Admin Login</h2>
            <div class="posi">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="posi">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="posi">
                        <label for="email">Email:</label>
                        <input type="email" id="user_email" name="user_email" placeholder="Enter your user-email" required>
                    </div>
                    <div class="posi">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="posi">
                        <input type="submit" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </body>

    </html>