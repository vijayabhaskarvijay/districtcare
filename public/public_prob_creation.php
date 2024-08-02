<?php
session_start();

// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "urbanlink";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $userLocation = $_POST['userLocation'] ?? '';
    $userMainArea = $_POST['userMainArea'] ?? '';
    $problemDept = $_POST['problemDept'] ?? '';
    $problemType = $_POST['problemType'] ?? '';
    $problemDesc = $_POST['problemDesc'] ?? '';
    $problemLoc = $_POST['problemLoc'] ?? '';
    $problemDate = $_POST['problemDate'] ?? '';
    $currentDateTime = date("Y-m-d H:i:s");

    // Get latitude and longitude from problemLoc
    list($newLat, $newLng) = explode(', ', $_POST['problemLoc']);

    // Prepare SQL statement to check for similar reports
    $stmt_check = $conn->prepare("SELECT * FROM prob_details WHERE prob_dept = ? AND prob_type = ? AND prob_user_loc = ? AND prob_user_mainarea = ?");
    $stmt_check->bind_param("ssss", $problemDept, $problemType, $userLocation, $userMainArea);

    // Execute the statement
    $stmt_check->execute();

    // Check if a similar report already exists
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Assuming prob_loc is a string in the format "latitude, longitude"
            list($existingLat, $existingLng) = explode(', ', $row['prob_loc']);

            // Calculate distance between existing and new coordinates
            $distance = sqrt(pow($existingLat - $newLat, 2) + pow($existingLng - $newLng, 2));

            // Define a threshold for similarity (adjust as needed)
            $threshold = 0.001; // Example threshold, you may need to fine-tune this

            if ($distance < $threshold) {
                $error = "Similar problem from the same location has already been reported.";
                break; // Stop checking further, as we've found a similar report
            }
        }
    }

    // Initialize the $stmt_insert variable
    $stmt_insert = null;

    // If no similar reports found, proceed with inserting the new report
    if (empty($error)) {
        // Check if the form is submitted with valid values
        if (!empty($phoneNumber) && !empty($problemDate) && !empty($problemLoc)) {
            // Prepare the SQL statement
            $probId = "PROB" . uniqid();
            $stmt_insert = $conn->prepare("INSERT INTO prob_details (prob_id, prob_user_id, prob_user_name, prob_user_phone, prob_user_loc, prob_user_mainarea, prob_dept,prob_type, prob_desc,prob_loc,prob_date,prob_time,  problem_status) VALUES (?, ?, ?, ?,?, ?, ?,?, ?, ?, ?, ?, 'NEW')");
            $stmt_insert->bind_param("ssssssssssss", $probId, $userId, $username, $phoneNumber,  $userLocation, $userMainArea, $problemDept, $problemType,  $problemDesc, $problemLoc, $problemDate, $currentDateTime);

            // Execute the SQL statement
            if ($stmt_insert->execute()) {
                $_SESSION['prob_id'] = $probId; // Store problem ID in session variable
                $_SESSION['form_values'] = []; // Clear form values in session
                $success = "Problem created successfully. Problem ID: " . $probId;
            } else {
                $error = "Failed to insert data into the database: " . $stmt_insert->error;
            }
        } else {
            $error = "Please fill in all the required fields.";
        }
    }

    // Close the statements
    $stmt_check->close();
    if ($stmt_insert) {
        $stmt_insert->close();
    }
}

// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html>
<title>Report Area Problems</title>
<link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: sans-serif;
        width: 100%;
        max-height: 600px;
        background-image: url("../images/6286273.jpg");
        background-size: cover;
    }

    /* CSS styles for the success message */
    .toast {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #4CAF50;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 9999;
    }

    /* Additional styles for the success message */
    .success-message {
        background-color: #4CAF50;
    }

    /* Styles for other types of toast messages (e.g., warning, error) */
    .warning-message {
        background-color: #f0ad4e;
    }

    .error-message {
        background-color: #d9534f;
    }

    #map-container {
        width: 100%;
        height: 300px;
        margin-bottom: 15px;
    }

    .container {
        width: 65%;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ccc;
        height: 600px;
        display: flex;
        border-radius: 5px;
        box-shadow: 3px 3px 5px rgba(0, 0, 0, 2);
        background-color: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        position: relative;
    }

    .left {
        position: relative;
        left: 90px;
        width: 500px;
        top: 25px;
    }

    .right {
        position: relative;
        width: 300px;
        left: 550px;
        top: -510px;
    }

    .form-group select {
        width: 390px;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 15px;
        transition: border-color 0.3s ease-in-out;
    }

    .container h2 {
        text-align: center;
        margin-bottom: 20px;
        position: relative;
        left: -30px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group input[type="file"],
    .form-group input[type="tel"],
    .form-group textarea {
        width: 370px;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 15px;
        transition: border-color 0.3s ease-in-out;
    }

    /* GO BACK  START*/

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
    }


    /* BO BACK ENDS */
    .form-group input[type="text"]:focus,
    .form-group input[type="tel"]:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #4CAF50;
    }

    .form-group button[type="submit"] {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }

    .form-group button[type="submit"]:hover {
        background-color: #45a049;
    }

    .form-group .error {
        color: #ff0000;
    }

    .form-group .success {
        color: #008000;
    }

    .submit {
        position: absolute;
        top: 660px;
        left: 450px;
        cursor: pointer;
        padding: 15px;
        border: 3px solid black;
        background-color: #ff0000;
        color: white;
        border-radius: 5px;
        transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    .submit:hover {
        background-color: #cc0000;
        transform: scale(1.1);
    }

    .alert-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 0;
        overflow: hidden;
        transition: height 0.3s ease-in-out;
    }

    .alert {
        width: 300px;
        padding: 10px;
        margin-top: 20px;
        border-radius: 4px;
        font-weight: bold;
        text-align: center;
    }


    .alert-success {
        background-color: #008000;
        color: white;
    }

    .alert-error {
        background-color: #ff0000;
        color: white;
    }

    /* Media queries */
    /* 900 TO 1100 */
    @media only screen and (min-width: 901px) and (max-width: 1200px) {
        body {
            width: 1100px;
            height: fit-content;
            /* background-color: #AFDCEC; */
            background-attachment: fixed;
            background-position: center top;
        }

        .container {
            flex-direction: column;
            height: auto;
            /* width: 900px; */
            position: relative;
            /* left: 30px; */
            width: 60%;
        }

        .left,
        .right {
            position: relative;
            width: 70%;
            left: 120px;
            top: 0;
            margin-bottom: 30px;
        }

        .submit {
            position: relative;
            left: 250px;
            top: -20px;
            transform: none;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-group input[type="text"],
        .form-group input[type="tel"] {
            width: 90%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: border-color 0.3s ease-in-out;
        }

        .form-group input[type="date"],
        .form-group textarea {
            width: 90%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: border-color 0.3s ease-in-out;
        }

        .form-group select {
            width: 95%;
        }
    }




    /* 600 TO 900 */
    @media only screen and (min-width: 601px) and (max-width: 900px) {
        body {
            max-width: 900px;
            height: fit-content;
            /* background-color: #AFDCEC; */
            background-attachment: fixed;
            background-position: center top;
        }

        .container {
            flex-direction: column;
            height: auto;
            width: 1200px;
            position: relative;
            top: 20px;
            /* left: 30px; */
            width: 60%;
        }

        .left,
        .right {
            position: relative;
            width: 100%;
            left: 0;
            top: 0;
            margin-bottom: 30px;
        }

        .submit {
            position: relative;
            left: 180px;
            top: -20px;
            transform: none;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-group input[type="text"],
        .form-group input[type="tel"] {
            width: 90%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: border-color 0.3s ease-in-out;
        }

        .form-group input[type="date"],
        .form-group textarea {
            width: 90%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: border-color 0.3s ease-in-out;
        }

        .form-group select {
            width: 95%;
        }
    }

    /* 300 TO 600 */
    @media only screen and (min-width:300px) and (max-width:600px) {
        body {
            /* height: fit-content; */
            background-color: #AFDCEC;
            background-image: none;
        }

        .container {
            flex-direction: column;
            height: auto;
            width: 800px;
            position: relative;
            /* left: 30px; */
            top: 10px;
            width: 60%;
        }

        .left,
        .right {
            position: relative;
            width: 100%;
            left: 0;
            top: 0;
            margin-bottom: 30px;
        }

        .submit {
            position: relative;
            left: 80px;
            top: -20px;
            transform: none;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-group input[type="text"],
        .form-group input[type="tel"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 90%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: border-color 0.3s ease-in-out;
        }
    }
</style>

<!-- MAPS API INTEGRATION STARTS-->
<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css" rel="stylesheet" />

<!-- MAPS API INTEGRATION ENDS -->

</head>

<body>
    <div class="back-button">
        <a href="public_user_landing.php" class="go-back">⬅️ GO BACK</a>
    </div>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group left">
                <h2>Personal Details</h2>
                <label for="userId">User ID:</label>
                <input type="text" name="userId" id="userId" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>" readonly>
                <label for="username">Your Name:</label>
                <input type="text" name="username" id="username" value="<?php echo $_SESSION['username']; ?>" readonly>
                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" name="phoneNumber" id="phoneNumber" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required placeholder="Enter your phone number" value="<?php echo isset($_SESSION['form_values']['phoneNumber']) ? $_SESSION['form_values']['phoneNumber'] : ''; ?>">

                <!-- DROPDOWN FOR USERLOCATION AND USER MAIN AREA STARTS -->

                <label for="userLocation">User Location:</label>
                <select name="userLocation" id="userLocation" required onchange="updateMainAreas()">
                    <option value="">Select Location</option>
                    <option value="Gobichettipalayam" <?php echo isset($_SESSION['form_values']['userLocation']) && $_SESSION['form_values']['userLocation'] === 'Gobichettipalayam' ? 'selected' : ''; ?>>Gobichettipalayam</option>
                    <option value="Sathyamangalam" <?php echo isset($_SESSION['form_values']['userLocation']) && $_SESSION['form_values']['userLocation'] === 'Sathyamangalam' ? 'selected' : ''; ?>>Sathyamangalam</option>
                    <!-- Add more location options as needed -->
                </select>

                <label for="userMainArea">Problem Location:</label>
                <select name="userMainArea" id="userMainArea" required>
                    <option value="">Select Problem Location</option>
                </select>
                <!-- DROPDOWN FOR USERLOCATION AND USER MAIN AREA ENDS-->
                <label for="problemDesc">Problem Description:</label>
                <textarea name="problemDesc" id="problemDesc" required placeholder="Enter problem description"><?php echo isset($_SESSION['form_values']['problemDesc']) ? $_SESSION['form_values']['problemDesc'] : ''; ?></textarea>
            </div>

            <div class="form-group right">
                <h2>Problem Details</h2>
                <label for="problemDept">Problem Department:</label>
                <select name="problemDept" id="problemDept" required onchange="updateProblemTypes()">
                    <option value="">Select Problem Related Department</option>
                    <option value="Revenue Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Revenue Department' ? 'selected' : ''; ?>>Revenue Department</option>
                    <option value="Public Works Department (PWD)" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Public Works Department (PWD)' ? 'selected' : ''; ?>>Public Works Department (PWD)</option>
                    <option value="Municipal Corporation" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Municipal Corporation' ? 'selected' : ''; ?>>Municipal Corporation</option>
                    <option value="Health Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Health Department' ? 'selected' : ''; ?>>Health Department</option>
                    <option value="Education Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Education Department' ? 'selected' : ''; ?>>Education Department</option>
                    <option value="Agriculture Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Agriculture Department' ? 'selected' : ''; ?>>Agriculture Department</option>
                    <option value="Police Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Police Department' ? 'selected' : ''; ?>>Police Department</option>
                    <option value="Fire and Rescue Services Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Fire and Rescue Services Department' ? 'selected' : ''; ?>>Fire and Rescue Services Department</option>
                    <option value="Social Welfare Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Social Welfare Department' ? 'selected' : ''; ?>>Social Welfare Department</option>
                    <option value="Rural Development Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Rural Development Department' ? 'selected' : ''; ?>>Rural Development Department</option>
                    <option value="Transport Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Transport Department' ? 'selected' : ''; ?>>Transport Department</option>
                    <option value="Forest Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Forest Department' ? 'selected' : ''; ?>>Forest Department</option>
                    <option value="Animal Husbandry Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Animal Husbandry Department' ? 'selected' : ''; ?>>Animal Husbandry Department</option>
                    <option value="Town Planning Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Town Planning Department' ? 'selected' : ''; ?>>Town Planning Department</option>
                    <option value="Electricity Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Electricity Department' ? 'selected' : ''; ?>>Electricity Department</option>
                    <option value="Water Supply and Sanitation Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Water Supply and Sanitation Department' ? 'selected' : ''; ?>>Water Supply and Sanitation Department</option>
                    <option value="Information and Public Relations Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Information and Public Relations Department' ? 'selected' : ''; ?>>Information and Public Relations Department</option>
                    <option value="Other Department" <?php echo isset($_SESSION['form_values']['problemDept']) && $_SESSION['form_values']['problemDept'] === 'Other Department' ? 'selected' : ''; ?>>Other</option>
                </select>

                <label for="problemType">Problem Type:</label>
                <select name="problemType" id="problemType" required>
                    <option value="">Select Problem Type</option>
                    <!-- Problem types will be populated dynamically using JavaScript -->
                </select>

                <!-- MAP INTEGRATION HTML STARTS -->
                <label for="problemLoc">Problem Location:</label>
                <div id="map" style="width: 100%; height: 200px;"></div>
                <input type="hidden" id="problemLoc" name="problemLoc" value="">
                <!-- MAP INTEGRATION HTML ENDS -->
                <label for="problemDate">Problem Date:</label>
                <input type="date" name="problemDate" id="problemDate" required value="<?php echo isset($_SESSION['form_values']['problemDate']) ? $_SESSION['form_values']['problemDate'] : ''; ?>">
            </div>

            <button type="submit" name="submit" class="submit" value="submit">Report Problem</button>
        </form>
    </div>
    <div class="alert-container">
        <?php if ($error) : ?>
            <div id="errorAlert" class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success) : ?>
            <div id="successAlert" class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
    </div>

    <script>
        function showSuccessMessage(message) {
            var successAlert = document.getElementById("successAlert");
            successAlert.textContent = message;
            successAlert.parentElement.style.height = "auto";
            setTimeout(function() {
                successAlert.parentElement.style.height = "0";
                // Redirect after displaying the success message
                window.location.href = "public_user_landing.php";
            }, 3000); // Redirect after 3 seconds (adjust as needed)
        }

        function showErrorMessage(message) {
            var errorAlert = document.getElementById("errorAlert");
            errorAlert.textContent = message;
            errorAlert.parentElement.style.height = "auto";
            setTimeout(function() {
                errorAlert.parentElement.style.height = "0";
            }, 3000);
        }

        <?php if ($error) : ?>
            window.addEventListener("DOMContentLoaded", function() {
                showErrorMessage("<?php echo $error; ?>");
            });
        <?php endif; ?>

        <?php if ($success) : ?>
            window.addEventListener("DOMContentLoaded", function() {
                showSuccessMessage("<?php echo $success; ?>");
            });
        <?php endif; ?>
    </script>
    <!-- MAP SCRIPT STARTS -->
    <!-- Initialize the map and set it to user's location -->
    <script>
        var map = L.map('map').setView([0, 0], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([0, 0], {
            draggable: true
        }).addTo(map);

        var latLng; // Declare latLng variable

        map.on('click', function(e) {
            updateMarkerAndLocation(e.latlng);
        });

        marker.on('dragend', function(e) {
            updateMarkerAndLocation(e.target.getLatLng());
        });

        function updateMarkerAndLocation(latLng) {
            marker.setLatLng(latLng);
            updateLocation(latLng);

            // Log latitude and longitude to the console
            console.log('Latitude:', latLng.lat, 'Longitude:', latLng.lng);
        }

        function updateLocation(latLng) {
            var accessToken = 'sk.eyJ1Ijoic3JpcmFtLXYiLCJhIjoiY2xsOHc2eHpnMWg3NDNmbzFjeWtmdG5iZSJ9.BU5uRK_DtKF1gB0n3TVAHg'; // Replace with your Mapbox access token
            var geocodingUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${latLng.lng},${latLng.lat}.json?access_token=${accessToken}`;

            fetch(geocodingUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        document.getElementById('problemLoc').value = latLng.lat + ', ' + latLng.lng;
                        // var address = data.features[0].place_name;
                        // document.getElementById('problemLoc').value = address;
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                });
        }

        // Initialize the map to user's location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var latLng = L.latLng(position.coords.latitude, position.coords.longitude);
                map.setView(latLng);
                marker.setLatLng(latLng);
                updateLocation(latLng);
            }, function(error) {
                console.error('Error getting geolocation:', error);
            });
        }
    </script>
    <!-- MAP SCRIPT ENDS -->
    <!-- PROBLEM TYPE UPDATION SCRIPS STARTS -->
    <script>
        var problemTypesByDepartment = {
            "Revenue Department": [
                "Tax Evasion",
                "Land Ownership Disputes",
                "Unfair Property Assessment",
                "Corruption in Land Transactions",
                "Delayed Land Title Issuance",
                "Inconsistent Property Tax Calculation",
                "Unauthorized Land Encroachments",
                "Land Use Violations",
                "Fraudulent Land Transfers",
                "Lack of Transparent Land Records",
                "Others"
            ],
            "Public Works Department (PWD)": [
                "Poor Road Conditions",
                "Insufficient Road Signage",
                "Inadequate Drainage Systems",
                "Traffic Congestion at Junctions",
                "Dilapidated Bridges and Overpasses",
                "Neglected Public Infrastructure",
                "Inefficient Street Lighting",
                "Unsafe Pedestrian Walkways",
                "Slow Construction Projects",
                "Lack of Maintenance Planning",
                "Others"
            ],
            "Municipal Corporation": [
                "Improper Waste Disposal",
                "Inadequate Garbage Collection",
                "Insufficient Recycling Facilities",
                "Lack of Public Toilets",
                "Unsanitary Slums and Settlements",
                "Noise Pollution Violations",
                "Inadequate Green Spaces",
                "Unsafe Building Structures",
                "Overcrowded Public Transport",
                "Ineffective Urban Planning",
                "Others"
            ],
            "Health Department": [
                "Inadequate Healthcare Access",
                "Shortage of Medical Staff",
                "Insufficient Medical Facilities",
                "Disease Outbreak Preparedness",
                "Poor Hospital Sanitation",
                "Inadequate Ambulance Services",
                "Limited Access to Medicines",
                "High Maternal and Infant Mortality",
                "Mental Health Neglect",
                "Lack of Preventive Healthcare Programs",
                "Others"
            ],
            "Education Department": [
                "Shortage of Qualified Teachers",
                "Inadequate Educational Facilities",
                "Outdated Curriculum",
                "High Dropout Rates",
                "Lack of Digital Learning Resources",
                "Educational Inequality",
                "Bullying and School Safety",
                "Limited Extracurricular Activities",
                "Teacher Training Deficiencies",
                "Insufficient Funding for Education",
                "Others"
            ],
            "Agriculture Department": [
                "Water Scarcity for Farming",
                "Crop Disease Outbreaks",
                "Inadequate Agricultural Extension Services",
                "Lack of Modern Farming Techniques",
                "Land Degradation and Soil Erosion",
                "Unfair Market Practices",
                "Pesticide Misuse and Environmental Impact",
                "Limited Access to Agricultural Credit",
                "Inefficient Irrigation Systems",
                "Lack of Agribusiness Opportunities",
                "Others"
            ],
            "Police Department": [
                "High Crime Rates",
                "Cybercrime and Digital Fraud",
                "Gang and Organized Crime Activities",
                "Drug Trafficking and Substance Abuse",
                "Community-Police Relations",
                "Insufficient Police Presence in Certain Areas",
                "Corruption in Law Enforcement",
                "Violence Against Women and Domestic Abuse",
                "Homicides and Murders",
                "Identity Theft and Robberies",
                "Others"
            ],
            "Fire and Rescue Services Department": [
                "Lack of Fire Safety Education",
                "Obsolete Firefighting Equipment",
                "Inadequate Emergency Response Training",
                "Urban Fire Hazards",
                "Industrial and Chemical Accidents",
                "Insufficient Fire Hydrants",
                "High-rise Building Evacuation Challenges",
                "Inadequate Fire Safety Regulations",
                "Poor Fire Drills and Preparedness",
                "Wildfire Prevention and Control",
                "Others"
            ],
            "Social Welfare Department": [
                "Homelessness and Lack of Shelter",
                "Elderly Care and Support",
                "Child Welfare and Protection",
                "Substance Abuse Rehabilitation",
                "Access to Healthcare for Vulnerable Groups",
                "Poverty Alleviation Programs",
                "Gender Discrimination and Violence",
                "Discrimination Against LGBTQ+ Community",
                "Disability Rights and Inclusion",
                "Mental Health Support",
                "Others"
            ],
            "Rural Development Department": [
                "Agricultural Productivity Gap",
                "Limited Access to Credit",
                "Lack of Basic Infrastructure",
                "Unemployment and Underemployment",
                "Landlessness and Land Rights",
                "Insufficient Rural Healthcare Services",
                "Educational Disparities in Rural Areas",
                "Lack of Potable Drinking Water",
                "Inadequate Connectivity to Markets",
                "Agricultural Market Exploitation",
                "Others"
            ],
            "Transport Department": [
                "Traffic Congestion",
                "Road Safety Concerns",
                "Public Transportation Issues",
                "Parking Problems",
                "Licensing and Registration",
                "Vehicle Pollution",
                "Infrastructure Maintenance",
                "Traffic Signal Malfunction",
                "Accidents and Collisions",
                "Pedestrian Safety",
                "Others"
            ],
            "Forest Department": [
                "Illegal Logging",
                "Wildlife Poaching",
                "Forest Fires",
                "Habitat Destruction",
                "Encroachment",
                "Illegal Mining",
                "Biodiversity Conservation",
                "Protected Area Management",
                "Forest Restoration",
                "Eco-Tourism",
                "Others"
            ],
            "Animal Husbandry Department": [
                "Animal Cruelty",
                "Disease Outbreaks",
                "Livestock Management",
                "Veterinary Services",
                "Animal Welfare",
                "Poultry Farming Concerns",
                "Breeding Practices",
                "Zoonotic Diseases",
                "Dairy Industry Issues",
                "Animal Vaccination",
                "Others"
            ],
            "Town Planning Department": [
                "Zoning Violations",
                "Illegal Constructions",
                "Urban Development Planning",
                "Land Use Disputes",
                "Heritage Preservation",
                "Infrastructure Development",
                "Public Spaces Management",
                "Building Code Violations",
                "Housing and Slum Issues",
                "Green Spaces and Parks",
                "Others"
            ],
            "Electricity Department": [
                "Power Outages",
                "High Electricity Bills",
                "Electrical Safety Concerns",
                "Faulty Wiring",
                "Meter Tampering",
                "Voltage Fluctuations",
                "Infrastructure Maintenance",
                "Renewable Energy Integration",
                "Billing Errors",
                "Transformer Failures",
                "Others"
            ],
            "Water Supply and Sanitation Department": [
                "Water Shortages",
                "Poor Water Quality",
                "Sanitation Facilities",
                "Sewage Management",
                "Water Contamination",
                "Water Treatment Plants",
                "Open Defecation",
                "Drainage Problems",
                "Waste Disposal",
                "Public Toilets Maintenance",
                "Others"
            ],
            "Public Distribution System (PDS) Department": [
                "Ration Card Issues",
                "Food Quality and Safety",
                "Fair Price Shops Management",
                "Distribution Inequities",
                "PDS Corruption",
                "Supply Chain Problems",
                "Beneficiary Identification",
                "Subsidy Distribution",
                "Nutrition Programs",
                "PDS Reforms",
                "Others"
            ],
            "Information and Public Relations Department": [
                "Misinformation and Fake News",
                "Communication Gaps",
                "Public Awareness Campaigns",
                "Government Outreach",
                "Media Relations",
                "Transparency and Accountability",
                "Crisis Management",
                "Information Dissemination",
                "Citizen Engagement",
                "Digital Communication",
                "Others"
            ],
            "Other Department": [
                "Urban Planning Inefficiencies",
                "Inadequate Waste Management",
                "Environmental Pollution",
                "Inefficient Public Transportation",
                "Lack of Accessible Healthcare",
                "Educational Disparities",
                "Unemployment and Underemployment",
                "Housing Affordability Issues",
                "Inadequate Water Supply",
                "Community Safety Concerns",
                "Misinformation and Fake News",
                "Communication Gaps",
                "Others"
            ]
            // Add more departments and problem types as needed
        };

        function updateProblemTypes() {
            var departmentSelect = document.getElementById("problemDept");
            var problemTypeSelect = document.getElementById("problemType");
            var selectedDepartment = departmentSelect.value;

            // Clear existing options
            problemTypeSelect.innerHTML = "";

            // Populate problem types based on selected department
            if (problemTypesByDepartment[selectedDepartment]) {
                problemTypesByDepartment[selectedDepartment].forEach(function(problemType) {
                    var option = document.createElement("option");
                    option.value = problemType;
                    option.text = problemType;
                    problemTypeSelect.appendChild(option);
                });
            }
        }
    </script>

    <!-- PROBLEM TYPE UPDATION SCRIPS ENDS -->

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
        };

        function updateMainAreas() {
            var locationSelect = document.getElementById("userLocation");
            var mainAreaSelect = document.getElementById("userMainArea");
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