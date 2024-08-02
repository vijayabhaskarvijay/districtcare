    <?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
        // Redirect to the login page
        header("Location: main_login.php");
        exit();
    }

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form values
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $phoneNumber = $_POST['phoneNumber'];
        $email = $_POST['email'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $date = $_POST['date'];

        // Connect to the database
        $servername = "localhost";
        $dbUsername = "root";
        $dbPassword = "";
        $dbname = "urbanlink";

        try {
            // Create a PDO instance
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Generate a unique post ID
            $postID = uniqid();

            // Prepare and execute the query
            $stmt = $conn->prepare("INSERT INTO public_contact_help (pch_id, pch_user_id, pch_user_name, pch_user_phone, pch_user_email, pch_user_location, pch_desc, pch_date) VALUES (:postID, :user_id, :username, :phoneNumber,:email, :location,:description, :date)");
            $stmt->bindParam(':postID', $postID);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':date', $date);
            $stmt->execute();

            // Display the success message and redirect after3 seconds
            $successMessage = "QUESTION  SUBMITTED SUCCESSFULLY. QUESTION ID: $postID";
            echo '<div id="popup" class="popup">
                    <h3>Success!</h3>
                    <p>' . $successMessage . '</p>
                </div>';
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "public_user_landing.php";
                    }, 3000);
                </script>';
        } catch (PDOException $e) {
            // Handle database connection or query errors
            $errorMessage = "Database error: " . $e->getMessage();
        }
    }
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Help - Ask Question </title>
        <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            .go-back {
                padding: 10px;
                text-align: center;
                background-color: orange;
                cursor: pointer;
                color: white;
                text-decoration: none;
                margin: 20px;
                position: relative;
                top: -20px;
                left: -8px;
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

            /* CSS styles for the popup */
            .popup {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 20px;
                border-radius: 5px;
                z-index: 9999;
                display: none;
            }

            .popup h3 {
                margin-top: 0;
            }

            body {
                font-family: Arial, sans-serif;
                background-color: #AFDCEC;
                height: auto;
            }

            .container {
                width: 50%;
                margin: 0 auto;
                padding: 10px;
                margin-top: 50px;
                backdrop-filter: blur(10px);
                border-radius: 5px;
                background-color: #f8f9fa;
                box-shadow: 0px 0px 10px rgba(1, 1, 1, 2);
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                position: relative;
                top: -30px;
            }

            .column {
                width: 70%;
                padding: 50px;
                box-sizing: border-box;
            }

            h2 {
                text-align: center;
                color: #333333;
                margin-bottom: 20px;
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
            input[type="tel"],
            input[type="email"],
            input[type="date"] {
                width: 100%;
                padding: 10px;
                border-radius: 3px;
                border: 2px solid #cccccc;
                color: black;
            }

            textarea {
                width: 100%;
                padding: 10px;
                border-radius: 3px;
                border: 1px solid #cccccc;
                resize: vertical;
            }

            select {
                width: 105%;
                padding: 10px;
                border-radius: 3px;
                border: 1px solid #cccccc;
            }

            .error {
                color: #FF0000;
                margin-bottom: 10px;
            }

            .success {
                color: #008000;
                margin-bottom: 10px;
            }

            input[type="submit"] {
                background-color: #007bff;
                color: #ffffff;
                display: block;
                margin: 0 auto;
                padding: 10px;
                border: 1px solid white;
                border-radius: 10px;
                cursor: pointer;
            }

            /* here end */

            /* 800PX TO 1100PX */
            @media only screen and (min-width:801px) and (max-width: 1100px) {
                body {
                    background-color: #AFDCEC;
                }

                .container {
                    width: 80%;
                    height: 70%;
                    background-color: rgba(255, 255, 255, 0.8);
                    backdrop-filter: blur(10px);
                    position: relative;
                    top: 30px;
                    font-size: 22px;
                }

                .column {
                    width: 90%;
                    position: relative;
                }


                input[type="submit"] {
                    position: relative;
                    top: 20px;
                    padding: 20px;
                    font-size: 25px;
                }

                input[type="text"],
                input[type="tel"],
                input[type="date"] {
                    width: 100%;
                    padding: 10px;
                    border-radius: 3px;
                    border: 1px solid #cccccc;
                    font-size: 22px;
                }

            }

            /* 600 to 800  */
            @media only screen and (min-width:601px) and (max-width: 800px) {
                body {
                    background-color: #AFDCEC;
                }

                .container {
                    width: 90%;
                    height: 80%;
                    background-color: rgba(255, 255, 255, 0.8);
                    backdrop-filter: blur(10px);
                    position: relative;
                    top: 30px;
                    font-size: 22px;
                }

                .column {
                    width: 85%;
                }


                input[type="submit"] {
                    position: relative;
                    top: 20px;
                }
            }

            /* 300 TO 600 */
            @media only screen and (min-width:300px) and (max-width: 600px) {
                body {
                    background-color: #AFDCEC;

                }

                .container {
                    position: absolute;
                    width: 85%;
                    height: 100% !important;
                    background-color: rgba(255, 255, 255, 0.8);
                    backdrop-filter: blur(10px);
                    top: 10px;
                    left: 15px;
                    box-shadow: 0px 0px 20px rgba(0, 0, 0, 1);
                }

                .column {
                    position: relative;
                    width: 100%;
                    font-size: 20px;
                    top: -50px;
                }


                input[type="text"],
                input[type="tel"],
                input[type="date"] {
                    width: 100%;
                    padding: 10px;
                    border-radius: 3px;
                    border: 1px solid #cccccc;
                    font-size: 20px;
                }

                input[type="submit"] {
                    position: relative;
                    top: 20px;
                    padding: 20px;
                }

                .test {
                    font-size: 20px;
                }


                .back-button {
                    position: relative;
                    top: 30px;
                    left: -5px;
                }

            }
        </style>
    </head>

    <body>

        <div class="back-button">
            <a href="public_help_faq.php" class="go-back">⬅️ GO BACK</a>
        </div>
        <div class="container">
            <div class="column">
                <h2>Question Request</h2>
                <form method="POST" action="public_question_form.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="user_id">User ID</label>
                        <input type="text" id="user_id" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="tel" id="phoneNumber" name="phoneNumber" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Your Doubt 🤔:</label>
                        <textarea id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="location" class="test">Location</label>
                        <select id="location" name="location" required onchange="updateMainAreas()">
                            <option class="test" value="Select Place">Select Place</option>
                            <option class="test" value="Gobichettipalayam">Gobichettipalayam</option>
                            <option class="test" value="Sathyamangalam">Sathyamangalam</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Enter your E-mail" required>
                    </div>
                    <div class=" form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group" style="text-align: center; width: 100%;">
                        <input type="submit" value="Submit Question">
                    </div>
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