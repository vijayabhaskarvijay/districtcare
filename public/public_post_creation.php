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
        $description = $_POST['description'];
        $location = $_POST['location'];
        $userArea = $_POST['userArea'];
        $date = $_POST['date'];
        $type = $_POST['gp_type'];
        $currentTime = date("H:i:s");
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
            $stmt = $conn->prepare("INSERT INTO public_posts (pp_id, pp_user_id, pp_username, pp_userphone, pp_userpost_description, pp_userloc, pp_userarea, pp_date, pp_time, pp_type) VALUES (:postID, :user_id, :username, :phoneNumber, :description, :location, :userArea, :date, :time,:gp_type)");
            $stmt->bindParam(':postID', $postID);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':userArea', $userArea);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $currentTime); // Bind the current time pp_time,
            $stmt->bindParam(':gp_type', $type);
            $stmt->execute();

            // Display the success message and redirect after3 seconds
            $successMessage = "Post created successfully. Post ID: $postID";
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
        <title>Create Post</title>
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
                color: #fff;
                padding: 20px;
                border-radius: 5px;
                z-index: 9999;
                display: none;
            }

            .popup h3 {
                margin-top: 0;
            }

            /* ABOVE 1100 PX */
            /* here start */

            /* Rest of the CSS styles */
            body {
                font-family: Arial, sans-serif;
                background-image: url("../images/public_post_creation_bg.jpg");
                background-size: cover;
                background-repeat: no-repeat;
        /* height: ; */
            }

            .container {
                width: 1000px;
                margin: 0 auto;
                padding: 10px;
                margin-top: 50px;
                /* background-color: rgba(25, 255, 255, 0.6); */
                /* background-color: rgba(255, 255, 255, 0.8); */
                backdrop-filter: blur(10px);
                border-radius: 5px;
                box-shadow: 0px 0px 10px rgba(1, 1, 1, 0.2);
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                position: relative;
                top: -30px;
            }

            .column {
                width: 50%;
                padding: 50px;
                box-sizing: border-box;
            }

            /* MARQUEE CODE CSS SECTION STARTS */

            .marquee-container {
                width: 60%;
                overflow: hidden;
                white-space: nowrap;
                /* background-color: #f7f7f7; */
                padding: 10px 0;
                position: relative;
                top: -0px;
                border: 1px solid black;
                border-radius: 25px;
                left: 18%;
                background: linear-gradient(45deg, #ffcc00, #ff6666, #ff66b2, #cc66ff, #6699ff, #66ccff);
                background-size: 600% 600%;
                animation: gradientAnimation 20s linear infinite;
                color: #fff;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            }

            /* Style for the marquee content */
            .marquee-content {
                display: inline-block;
                margin-right: 100%;
                animation: marquee 15s linear infinite;
            }

            /* Keyframes for the marquee animation */
            @keyframes marquee {
                0% {
                    transform: translateX(100%);
                }

                100% {
                    transform: translateX(-100%);
                }
            }

            @keyframes gradientAnimation {
                0% {
                    background-position: 0% 0%;
                }

                100% {
                    background-position: 600% 600%;
                }
            }

            /* MARQUEE CODE CSS SECTION ENDS */

            .column-2 {
                width: 50%;
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
            input[type="date"] {
                width: 100%;
                padding: 10px;
                border-radius: 3px;
                border: 1px solid #cccccc;
            }

            textarea {
                width: 100%;
                padding: 10px;
                border-radius: 3px;
                border: 1px solid #cccccc;
                resize: vertical;
            }

            select {
                width: 100%;
                padding: 10px;
                border-radius: 3px;
                border: 1px solid #cccccc;
            }

            .note {
                font-size: 14px;
                color: #666666;
                margin-bottom: 10px;
            }

            .file-input {
                margin-bottom: 10px;
            }

            .file-input label {
                display: block;
                font-size: 14px;
                padding: 10px;
                background-color: white;
                width: 50%;
                border: 2px solid slategray;
                cursor: pointer;
                color: slateblue;
                text-align: center;
                margin-bottom: 5px;
            }

            .file-input label:hover {
                background-color: #008000;
                transition: 0.2s ease;
                color: white;
            }

            .file-input input[type="file"] {
                display: none;
            }


            .file-input .file-button {
                display: inline-block;
                padding: 8px 12px;
                background-color: #4CAF50;
                color: #ffffff;
                border: none;
                cursor: pointer;
                border-radius: 3px;
            }

            .error {
                color: #FF0000;
                margin-bottom: 10px;
            }

            .success {
                color: #008000;
                margin-bottom: 10px;
            }

            .container {
                background-color: #f8f9fa;
            }

            h2 {
                color: #343a40;
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
                    /* height: 70vh; */
                    background-position: 50%;
                    width: 90%;
                    font-size: 28px;
                    background-attachment: fixed;
                }

                .container {
                    width: 830px;
                    height: 1220px;
                    background-color: rgba(255, 255, 255, 0.8);
                    backdrop-filter: blur(10px);
                    position: relative;
                    top: 30px;
                    left: 20px;
                    font-size: 22px;
                }

                .column {
                    width: 95%;
                    position: relative;
                    top: -10px;
                    font-size: 25px;
                }

                .column-2 {
                    width: 95%;
                    position: relative;
                    top: -120px;
                    font-size: 25px;
                }

                input[type="submit"] {
                    position: relative;
                    top: -160px;
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
                    /* height: 70vh; */
                    background-position: 50%;
                    width: 90%;
                    font-size: 22px;
                }

                .container {
                    width: 700px;
                    height: 980px;
                    background-color: rgba(255, 255, 255, 0.8);
                    backdrop-filter: blur(10px);
                    position: relative;
                    top: 30px;
                    left: 30px;
                    font-size: 22px;
                }

                .column {
                    width: 95%;
                    position: relative;
                    top: -50px;
                }

                .column-2 {
                    width: 95%;
                    position: relative;
                    top: -160px;
                }

                input[type="submit"] {
                    position: relative;
                    top: -200px;
                }
            }

            /* 300 TO 600 */
            @media only screen and (min-width:300px) and (max-width: 600px) {
                body {
                    background-color: #AFDCEC;
                    background-image: none;
                }

                .container {
                    width: 93%;
                    height: 1090px;
                    background-color: rgba(255, 255, 255, 0.8);
                    backdrop-filter: blur(10px);
                    position: relative;
                    top: -20px;
                    left: 0px;
                    box-shadow: 0px 0px 20px rgba(0, 0, 0, 1);
                }

                .column {
                    width: 850px;
                    position: relative;
                    top: -70px;
                    font-size: 20px;
                }

                .column-2 {
                    width: 850px;
                    position: relative;
                    top: -180px;
                    font-size: 20px;
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
                    top: -200px;
                    /* width: 70%; */
                }

                .file-input label {
                    width: 70%;

                }

                .test {
                    font-size: 20px;
                }

                .marquee-container {
                    width: 80%;
                    overflow: hidden;
                    white-space: nowrap;
                    /* background-color: #f7f7f7; */
                    padding: 10px 0;
                    position: relative;
                    top: -0px;
                    border: 1px solid black;
                    border-radius: 25px;
                    left: 18%;
                    background: linear-gradient(45deg, #ffcc00, #ff6666, #ff66b2, #cc66ff, #6699ff, #66ccff);
                    background-size: 600% 600%;
                    animation: gradientAnimation 20s linear infinite;
                    color: #fff;
                    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
                    position: relative;
                    left: 7%;
                }

                .btn {
                    box-sizing: border-box;
                    position: relative;
                    width: 100%;
                }

                .back-button {
                    position: relative;
                    top: 30px;
                    left: -5px;

                }

                .go-back {
                    padding: 10px;
                    text-align: center;
                    background-color: orange;
                    cursor: pointer;
                    color: white;
                    text-decoration: none;
                    border: 2px solid white;
                    border-radius: 5px;
                    height: 50px;
                    width: 50px;
                    z-index: 1;
                }
            }
        </style>
    </head>

    <body>

        <div class="back-button">
            <a href="public_user_landing.php" class="go-back">⬅️ GO BACK</a>
        </div>

        <div class="container">
            <div class="column">
                <h2>User Details</h2>
                <form method="POST" action="public_post_creation.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="user_id">User ID</label>
                        <input type="text" id="user_id" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="tel" id="phoneNumber" name="phoneNumber" oninput="validatePhoneNumber(this)" pattern="[0-9]{10}"  required>
                    </div>
                    <div class="form-group">
                        <label for="description">Post Description</label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                    </div>
            </div>
            <div class="column-2">
                <h2>Post Details</h2>
                <div class="form-group">
                    <label for="location" class="test">Location</label>
                    <select id="location" name="location" required onchange="updateMainAreas()">
                        <option class="test" value="Select Place">Select Place</option>
                        <option class="test" value="Gobichettipalayam">Gobichettipalayam</option>
                        <option class="test" value="Sathyamangalam">Sathyamangalam</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="userArea">User Main Area Location:</label>
                    <select name="userArea" id="userArea" required>
                        <option value="">Select Problem Location</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gp_type">Post Type</label>
                    <select id="gp_type" name="gp_type" required>
                        <option value="Jobs">Jobs</option>
                        <option value="Social Service">Social Service</option>
                        <option value="Events">Events</option>
                        <option value="News">News</option>
                        <option value="Announcements">Announcements</option>
                        <option value="Volunteer Opportunities">Volunteer Opportunities</option>
                        <option value="Education">Education</option>
                        <option value="Health and Wellness">Health and Wellness</option>
                        <option value="Environment">Environment</option>
                        <option value="Community Development">Community Development</option>
                        <option value="Fundraising">Fundraising</option>
                        <option value="Arts and Culture">Arts and Culture</option>
                        <option value="Sports and Recreation">Sports and Recreation</option>
                        <option value="Public Safety">Public Safety</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Technology and Innovation">Technology and Innovation</option>
                        <option value="Civic Engagement">Civic Engagement</option>
                        <option value="Local Initiatives">Local Initiatives</option>
                        <option value="Resources and Services">Resources and Services</option>
                        <option value="Advocacy">Advocacy</option>
                        <option value="Food and Nutrition">Food and Nutrition</option>
                        <option value="Housing">Housing</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class=" form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
            </div>

            <div class="form-group" style="text-align: center; width: 100%;">
                <input type="submit" value="Create Post">
            </div>
            </form>
        </div>

        <!-- MARQUEE STARTS -->
        <div class="marquee-container">
            <div class="marquee-content">
                Let's create a supportive and respectful environment. Please refrain from sharing inappropriate content, engaging in spam, or promoting any prohibited activities. Together, we can build a positive community.
            </div>
        </div>
        <!-- MARQUEE ENDS -->
        <script>
            // Display the popup
            document.addEventListener("DOMContentLoaded", function() {
                var popup = document.getElementById("popup");
                popup.style.display = "block";
            });
        </script>
        <!--USERLOCATION AND MAIN AREA CHOOSING DROPDOWN SCRIPT CODE STARTS  -->
        <script>
            var mainAreasByLocation = {
                "Gobichettipalayam": [
                    "Alingiam(gobi)", "Basuvanapuram", "Elathur Chettipalayam", "Erangattur",
                    "Getticheyur", "Gobichettipalayam East", "Gobichettipalayam South", "Kallipatti",
                    "Karattadipalayam", "Kasipalayam (erode)", "Kidarai", "Kodiveri",
                    "Kolappalur (erode)", "Kummakalipalayam", "Nambiyur", "Nanjagoundenpalayam",
                    "Pariyur Vellalapalayam", "Pattimaniakaranpalayam", "Perumugaipudur", "Pudukkaraipudur",
                    "Pudupalayam (erode)", "Sakthinagar", "Sokkumaripalayam", "Suriappampalayam",
                    "Theethampalayam", "Thuckanaickenpalayam"
                ],
                "Sathyamangalam": [
                    "Araipalayam", "Ariyappampalayam", "Bannari", "Bhavanisagar", "Chikkahalli",
                    "Dasappagoundanpudur", "Desipalayam", "Dhimbam", "Doddapura", "Germalam",
                    "Gumtapuram", "Kalkadambur", "Karalayam", "Karapadi", "Kembanaickenpalayam",
                    "Komarapalayam Sathy", "Kondapanaickenpalayam", "Kottuveerampalayam", "Nochikuttai",
                    "Periyur", "Puduvadavalli", "Punjai Puliampatti", "Rangasamudram", "Sathy Bazaar",
                    "Sathyamangalam", "Savakattupalayam", "Soosaipuram", "Talavadi", "Thingalur"
                ]
                // Add more locations and areas as needed
            };

            function updateMainAreas() {
                var locationSelect = document.getElementById("location");
                var mainAreaSelect = document.getElementById("userArea");
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

            // Initial call to populate main areas based on the default selection (if any)
            updateMainAreas();
        </script>
        <!--USERLOCATION AND MAIN AREA CHOOSING DROPDOWN SCRIPT CODE ENDS -->
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