        <?php
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $govn_staff_name = $_POST['govn_staff_name'];
            $govn_staff_email = $_POST['govn_staff_email'];
            $govn_staff_password = $_POST['govn_staff_password'];
            $govn_staff_work_dept = $_POST['govn_staff_work_dept'];
            $govn_staff_phone = $_POST['govn_staff_phone'];
            $govn_staff_location = $_POST['govn_staff_location'];
            $govn_staff_mpin = $_POST['govn_staff_mpin'];

            // Generate govn_staff_id
            $shortform = "";
            switch ($govn_staff_work_dept) {
                case "Revenue Department":
                    $shortform = "RVN";
                    break;
                case "Public Works Department (PWD)":
                    $shortform = "PWD";
                    break;
                case "Municipal Corporation":
                    $shortform = "MNC";
                    break;
                case "Health Department":
                    $shortform = "HLD";
                    break;
                case "Education Department":
                    $shortform = "EDU";
                    break;
                case "Agriculture Department":
                    $shortform = "AGR";
                    break;
                case "Police Department":
                    $shortform = "PLC";
                    break;
                case "Fire and Rescue Services Department":
                    $shortform = "FRS";
                    break;
                case "Social Welfare Department":
                    $shortform = "SWD";
                    break;
                case "Rural Development Department":
                    $shortform = "RDD";
                    break;
                case "Transport Department":
                    $shortform = "TRD";
                    break;
                case "Forest Department":
                    $shortform = "FST";
                    break;
                case "Animal Husbandry Department":
                    $shortform = "AHD";
                    break;
                case "Town Planning Department":
                    $shortform = "TPD";
                    break;
                case "Electricity Department":
                    $shortform = "ELD";
                    break;
                case "Water Supply and Sanitation Department":
                    $shortform = "WSD";
                    break;
                case "Public Distribution System (PDS) Department":
                    $shortform = "PDS";
                    break;
                case "Information and Public Relations Department":
                    $shortform = "IPR";
                    break;
            }
            // Generate unique ID (you may add additional logic for uniqueness)
            $govn_staff_id = uniqid();

            // Insert data into the database
            $servername = "localhost";
            $dbUsername = "root";
            $dbPassword = "";
            $dbname = "urbanlink";

            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("INSERT INTO govn_staff_details (govn_staff_id, govn_staff_name, govn_staff_email, govn_staff_password, govn_staff_work_dept, govn_staff_phone, govn_staff_location, govn_staff_mpin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$govn_staff_id, $govn_staff_name, $govn_staff_email, $govn_staff_password, $govn_staff_work_dept, $govn_staff_phone, $govn_staff_location, $govn_staff_mpin]);

                $successMessage = "Registration successful. Your Government Staff ID is: $govn_staff_id";
                if (isset($successMessage)) {
                    echo "<div class='message success'>$successMessage</div>";
                    echo "<script>
                setTimeout(function() {
                    window.location.href = 'admin_landing.php';
                }, 3000);
            </script>";
                    exit();
                }

                if (isset($errorMessage)) {
                    echo "<div class='message error'>$errorMessage</div>";
                }
            } catch (PDOException $e) {
                $errorMessage = "Error: " . $e->getMessage();
            }
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
            <title>Government Staff Registration</title>
            <style>
                body {
                    font-family: 'Roboto Slab', sans-serif;
                    background-color: #f4f4f4;
                    text-align: center;
                    margin: 0;
                }

                .container {
                    max-width: 400px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                    text-align: left;
                }

                .form-group {
                    margin-bottom: 15px;
                }

                label {
                    font-weight: bold;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"] {
                    width: 100%;
                    padding: 8px;
                    border: none;
                    border-bottom: 2px solid #ccc;
                    outline: none;
                    box-sizing: border-box;
                }

                input[type="text"]:focus,
                input[type="email"]:focus,
                input[type="password"]:focus,
                select:focus {
                    border-bottom: 2px solid #0088cc;
                }

                select {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    box-sizing: border-box;
                }

                input[type="submit"] {
                    background-color: #009688;
                    color: #fff;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                }

                input[type="submit"]:hover {
                    background-color: #00796b;
                }

                .message {
                    padding: 10px;
                    border-radius: 4px;
                    margin-bottom: 10px;
                }

                .success {
                    background-color: #FFD700;
                }

                .error {
                    background-color: #E32636;
                    color: #fff;
                }

                h1 {
                    text-align: center;
                    color: #333333;
                    margin-bottom: 20px;
                }
            </style>
        </head>

        <body>
            <h1>Government Staff Registration</h1>
            <div class="container">

                <?php if (isset($successMessage)) echo "<p class='message success'>$successMessage</p>"; ?>
                <?php if (isset($errorMessage)) echo "<p class='message error'>$errorMessage</p>"; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="govn_staff_name">Full Name:</label>
                        <input type="text" id="govn_staff_name" name="govn_staff_name" required><br><br>
                    </div>
                    <div class="form-group">
                        <label for="govn_staff_email">Email:</label>
                        <input type="email" id="govn_staff_email" name="govn_staff_email" required><br><br>
                    </div>
                    <div class="form-group">
                        <label for="govn_staff_password">Password:</label>
                        <input type="password" id="govn_staff_password" name="govn_staff_password" required><br><br>
                    </div>
                    <div class="form-group">
                        <label for="govn_staff_work_dept">Department:</label>
                        <select id="govn_staff_work_dept" name="govn_staff_work_dept" required>
                            <option value="-- Select Department --">-- Select Department --</option>
                            <option value="Revenue Department">Revenue Department</option>
                            <option value="Public Works Department (PWD)">Public Works Department (PWD)</option>
                            <option value="Municipal Corporation">Municipal Corporation</option>
                            <option value="Health Department">Health Department</option>
                            <option value="Education Department">Education Department</option>
                            <option value="Agriculture Department">Agriculture Department</option>
                            <option value="Police Department">Police Department</option>
                            <option value="Fire and Rescue Services Department">Fire and Rescue Services Department</option>
                            <option value="Social Welfare Department">Social Welfare Department</option>
                            <option value="Rural Development Department">Rural Development Department</option>
                            <option value="Transport Department">Transport Department</option>
                            <option value="Forest Department">Forest Department</option>
                            <option value="Animal Husbandry Department">Animal Husbandry Department</option>
                            <option value="Town Planning Department">Town Planning Department</option>
                            <option value="Electricity Department">Electricity Department</option>
                            <option value="Water Supply and Sanitation Department">Water Supply and Sanitation Department</option>
                            <option value="Public Distribution System (PDS) Department">Public Distribution System (PDS) Department</option>
                            <option value="Information and Public Relations Department">Information and Public Relations Department</option>
                        </select><br><br>
                    </div>
                    <div class="form-group">
                        <label for="govn_staff_phone">Phone:</label>
                        <input type="text" id="govn_staff_phone" name="govn_staff_phone" required pattern="[0-9]{10}" oninput="validatePhoneNumber(this)"><br><br>
                    </div>
                    <div class="form-group">
                        <label for="govn_staff_location">Location:</label>
                        <select id="govn_staff_location" name="govn_staff_location" required>
                            <option value="Gobichettipalayam">Gobichettipalayam</option>
                            <option value="Sathyamangalam">Sathyamangalam</option>
                        </select><br><br>
                    </div>
                    <div class="form-group">
                        <label for="govn_staff_mpin">MPIN:</label>
                        <input type="text" id="govn_staff_mpin" maxlength="6" name="govn_staff_mpin" required><br><br>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Register">
                    </div>
                </form>
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
        </body>

        </html>