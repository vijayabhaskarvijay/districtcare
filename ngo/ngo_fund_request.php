<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['ngo_username'])) {
    header("Location: ngo_login.php");
    exit();
}

// Get problem details from PROB ID
// Check if prob_id is set in the URL
if (isset($_GET['prob_id'])) {
    $prob_id = $_GET['prob_id'];
    // Create a connection to the database
    $conn = new mysqli("localhost", "root", "", "urbanlink");

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch problem details based on prob_id
    $stmt = $conn->prepare("SELECT * FROM prob_details WHERE prob_id = ?");
    $stmt->bind_param("s", $prob_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Get problem details
        $prob_id = $row['prob_id'];
        $user_name = $row['prob_user_name'];
        $user_phone = $row['prob_user_phone'];
        $user_loc = $row['prob_user_loc'];
        $user_mainarea = $row['prob_user_mainarea'];
        $prob_dept = $row['prob_dept'];
        $prob_type = $row['prob_type'];
        $prob_desc = $row['prob_desc'];
        $prob_loc = $row['prob_loc'];
        $prob_date = $row['prob_date'];
        $prob_date = $row['prob_date'];
        $fund_requested = $row['fund_requested'];
        // // Close the statement
        // $stmt->close();
    } else {
        echo '<div class="error-message">
                <span>❌</span>
                <p>Problem not found.</p>
            </div>';
        exit();
    }

    // Display form or message based on fund_requested value
    if ($fund_requested == 'NA' || $fund_requested == 'Not Allocated') {
        // Display the form
        $displayForm = true;
    } elseif ($fund_requested == 'Requested') {
        // Display requested message
        $displayForm = false;
        $message = "Funds Have Already Been Requested by an NGO for This Problem";
    } elseif ($fund_requested == 'Allocated') {
        // Display allocated message
        $displayForm = false;
        $message = "Funds Have Already Been Allocated to an NGO for This Problem";
    }
} else {
    echo '<div class="error-message">
            <span>❌</span>
            <p>Problem ID not set.</p>
        </div>';
    exit();
}

// Other PHP logic for saving the form data to the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract form data
    $fund_problem_id = $_POST['fund_problem_id'];
    $fund_user_id = $_SESSION['ngo_id'];
    $fund_user_name = $_POST['fund_user_name'];
    $fund_user_phone = $_POST['fund_user_phone']; // Provided by user
    $fund_org_name = $_POST['fund_org_name'];
    $fund_org_loc = $_POST['fund_org_loc'];
    $fund_org_phone = $_POST['fund_org_phone'];
    $fund_amount = $_POST['fund_amount'];
    $fund_prob_desc = $_POST['fund_prob_desc'];
    $fund_prob_type = $_POST['fund_prob_type'];
    $fund_prob_dept = $_POST['fund_prob_dept'];
    $fund_req_desc = $_POST['fund_req_desc'];
    $fund_req_date = $_POST['fund_req_date'];

    // Generate a unique fund_id
    $fund_id = "FUNDID" . uniqid();
    // Create a connection to the database
    $conn = new mysqli("localhost", "root", "", "urbanlink");

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL insert statement
    $insert_sql = "INSERT INTO funding_details (fund_id, fund_prob_id, fund_user_id, fund_user_name, fund_user_phone, fund_org_name, fund_org_loc, fund_org_phone, fund_amount, fund_prob_desc, fund_prob_type, fund_prob_dept, fund_req_desc, fund_req_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssssssssisssss", $fund_id, $fund_problem_id, $fund_user_id, $fund_user_name, $fund_user_phone, $fund_org_name, $fund_org_loc, $fund_org_phone, $fund_amount, $fund_prob_desc, $fund_prob_type, $fund_prob_dept, $fund_req_desc, $fund_req_date);
    if ($insert_stmt->execute()) {
        // Close the insert statement
        $insert_stmt->close();

        // Update prob_details table
        $update_sql = "UPDATE prob_details SET fund_requested = 'Requested' WHERE prob_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $fund_problem_id);

        if ($update_stmt->execute()) {
            $update_stmt->close();

            // Display success message and redirect after 3 seconds
            echo '<script>
                alert("Fund request submitted successfully.");
                setTimeout(function() {
                    window.location.href = "ngo_probpage.php";
                }, 3000); // Redirect after 3 seconds
            </script>';
        } else {
            echo '<script>alert("Error updating fund request status. Please try again later.");</script>';
            error_log("Database Update Error: " . $update_stmt->error);
        }
    } else {
        echo '<script>alert("Error submitting fund request. Please try again later.");</script>';
        error_log("Database Insert Error: " . $insert_stmt->error);
    }

    // UPDATING THE fund_requested COLUMN IN prob_details TABLE
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["prob_id"]) && isset($_POST["new_status"])) {
            $prob_id = $_POST["prob_id"];
            $new_status = $_POST["new_status"];
            $update_sql = "UPDATE prob_details SET ngo_problem_status = ? WHERE prob_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_status, $prob_id);
            if ($update_stmt->execute()) {
                // Close the update statement
                $update_stmt->close();
                // Redirect back to the same page to refresh
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                // Handle update error
                echo "Error updating status: " . $conn->error; // This line will display the error message
            }
        }
    }
    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>NGO Fund Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <style>
        body {
            font-family: 'Roboto Slab', sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .error-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: black;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin-top: 15px;
            font-size: 1.5em;
            /* Big text size */
        }

        .notice-message {
            background-color: #07edd6;
            color: black;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin-top: 15px;
            font-size: 1.5em;
        }

        .notice-message span {
            /* Emoji size */
            font-size: 2em;
            display: block;
            margin-bottom: 10px;
        }

        .error-message span {
            /* Emoji size */
            font-size: 2em;
            display: block;
            margin-bottom: 10px;
        }


        .container {
            width: 40%;
            min-width: 60%;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 10px 10px 25px rgba(0, 0, 0, 2);
            border-radius: 10px;
            position: relative;
            left: 30%;
        }

        .fields {
            position: relative;
            left: 20%;
            margin-bottom: 25px;
        }

        h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        form {
            display: grid;
            gap: 10px;
            margin-top: 15px;
        }

        label {
            font-weight: bold;
            position: relative;
            left: -3%;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 60%;
            padding: 10px;
            border: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        button[type="submit"] {
            width: 64%;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .success-message {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            margin-top: 15px;
            display: none;
        }

        /* Media query for responsive design */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Fund Request Form</h2>
        <?php if ($displayForm) { ?>
            <form method="post" class="fields">
                <label for="fund_problem_id">Problem ID:</label>
                <input type="text" class="auto" name="fund_problem_id" id="fund_problem_id" value="<?php echo $prob_id; ?>" readonly>

                <label for="fund_user_id">User ID:</label>
                <input type="text" class="auto" name="fund_user_id" id="fund_user_id" value="<?php echo $_SESSION['ngo_id']; ?>" readonly>

                <label for="fund_user_name">User Name:</label>
                <input type="text" name="fund_user_name" id="fund_user_name" required>

                <label for="fund_user_phone">User Phone:</label>
                <input type="text" name="fund_user_phone" id="fund_user_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required>

                <label for="fund_org_name">Organization Name:</label>
                <input type="text" name="fund_org_name" placeholder="Enter Organization Name Correctly.." required>

                <label for="fund_org_loc">Organization Location:</label>
                <select name="fund_org_loc" id="fund_org_loc">
                    <option value="Gobichettipalayam">Gobichettipalayam</option>
                    <option value="Sathyamangalam">Sathyamangalam</option>
                </select>

                <label for="fund_org_phone">Organization Phone:</label>
                <input type="text" name="fund_org_phone" id="fund_org_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required>

                <label for="fund_amount">Requesting Amount (Max 1,00,000):</label>
                <input type="number" name="fund_amount" id="fund_amount" max="100000" required>

                <label for="fund_prob_desc">Problem Description:</label>
                <textarea class="auto" name="fund_prob_desc" id="fund_prob_desc" rows="4" required><?php echo $prob_desc; ?></textarea>

                <label for="fund_prob_type">Problem Type:</label>
                <input type="text" name="fund_prob_type" id="fund_prob_type" value="<?php echo $prob_type; ?>" readonly>

                <label for="fund_prob_dept">Problem Department:</label>
                <input type="text" name="fund_prob_dept" id="fund_prob_dept" value="<?php echo $prob_dept; ?>" readonly>

                <label for="fund_req_desc">Fund Request Description:</label>
                <textarea name="fund_req_desc" id="fund_req_desc" rows="4" required></textarea>

                <label for="fund_req_date">Fund Requesting Date:</label>
                <input type="date" name="fund_req_date" id="fund_req_date" required>

                <button type="submit">Submit</button>
            </form>
        <?php } else { ?>
            <div class="notice-message">
                <!-- <span>❌</span> -->
                <?php
                if ($message == "Funds Have Already Been Requested by an NGO for This Problem") {
                    echo '<span><img src="../images/bookmark.gif" alt="Allocated Icon"></span>';
                } else if ($message == "Funds Have Already Been Allocated to an NGO for This Problem") {
                    echo '<span><img src="../images/tick.gif" alt="Allocated Icon"></span>';
                }
                ?>
                <p><?php echo $message; ?></p>
            </div>
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
</body>

</html>