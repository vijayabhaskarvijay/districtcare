<?php
session_start();


// Replace with your database connection code
$conn = new mysqli("localhost", "root", "", "urbanlink");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Retrieve the fund_id from the query parameter
$editFundId = $_GET['edit'];

$query = "SELECT * FROM funding_details WHERE fund_id = '$editFundId'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    //storiing in session variables
    $_SESSION['edit_fund_id'] = $row['fund_id'];
    $_SESSION['edit_fund_user_id'] = $row['fund_user_id'];
    $_SESSION['edit_fund_username'] = $row['fund_user_name'];
    $_SESSION['edit_fund_user_phone'] = $row['fund_user_phone'];
    $_SESSION['edit_fund_prob_id'] = $row['fund_prob_id'];
    $_SESSION['edit_fund_org_name'] = $row['fund_org_name'];
    $_SESSION['edit_fund_org_loc'] = $row['fund_org_loc'];
    $_SESSION['edit_fund_org_phone'] = $row['fund_org_phone'];
    $_SESSION['edit_fund_amount'] = $row['fund_amount'];
    $_SESSION['edit_fund_prob_desc'] = $row['fund_prob_desc'];
    $_SESSION['edit_fund_prob_type'] = $row['fund_prob_type'];
    $_SESSION['edit_fund_prob_dept'] = $row['fund_prob_dept'];
    $_SESSION['edit_fund_req_desc'] = $row['fund_req_desc'];
    $_SESSION['edit_fund_req_date'] = $row['fund_req_date'];
    $_SESSION['edit_fund_status'] = $row['fund_status'];
    $_SESSION['edit_fund_govnstaff_phone'] = $row['fund_govnstaff_phone'];
    $_SESSION['edit_fund_collect_place'] = $row['fund_collect_place'];
    $_SESSION['edit_fund_notice'] = $row['fund_notice'];
} else {
    $_SESSION['error_message'] = "Fund not found.";
    header("Location: govn_fund_manage.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $fundId = $_POST['fund_id'];
    $fundStatus = $_POST['fund_status'];
    $govnStaffPhone = $_POST['govn_staff_phone'];
    $fundNotice = $_POST['fund_notice'];
    $fundCollectPlace = $_POST['fund_collect_place'];
    $fundAllocated = $_POST['fund_allocated'];
    $fundProblemId = $_POST['fundProblemId'];
    // Update the database with the new values
    $updateQuery = "UPDATE funding_details SET fund_status = '$fundStatus', fund_govnstaff_phone = '$govnStaffPhone', fund_notice = '$fundNotice', fund_collect_place = '$fundCollectPlace' WHERE fund_id = '$fundId'";

    // Update the database with the new fund_allocated value
    $updateFundRequestedQuery = "UPDATE prob_details SET fund_requested = '$fundAllocated' WHERE prob_id = '$fundProblemId'";

    if ($conn->query($updateQuery) === TRUE) {
        if ($conn->query($updateFundRequestedQuery) === TRUE) {
            $_SESSION['success_message'] = "Fund details updated successfully!";

            unset($_SESSION['edit_fund_id']);
            unset($_SESSION['edit_fund_user_id']);
            unset($_SESSION['edit_fund_username']);
            unset($_SESSION['edit_fund_user_phone']);
            unset($_SESSION['edit_fund_prob_id']);
            unset($_SESSION['edit_fund_org_name']);
            unset($_SESSION['edit_fund_org_phone']);
            unset($_SESSION['edit_fund_org_loc']);
            unset($_SESSION['edit_fund_amount']);
            unset($_SESSION['edit_fund_prob_desc']);
            unset($_SESSION['edit_fund_prob_type']);
            unset($_SESSION['edit_fund_prob_dept']);
            unset($_SESSION['edit_fund_req_desc']);
            unset($_SESSION['edit_fund_status']);
            unset($_SESSION['edit_fund_govnstaff_phone']);
            unset($_SESSION['edit_fund_collect_place']);
            header("Location: govn_fund_manage.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating fund details: " . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "Error updating fund details: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fund Details</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background-image: url("../images/crisp-paper-ruffles.png"), linear-gradient(to right top, #98AFC7, #001F3F,#98AFC7);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .design-label {
            font-weight: 600;
            border: 2px dashed black;
            padding: 5px 10px;
            /* background-color: #B0E0E6; */
            background-color: #001F3F;
            /* background-color: #98AFC7; */
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }

        .design-label:hover {
            transform: translateY(-5px);
            transition: 0.2s ease-out;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 2);
            color: green;
        }


        .spl-high {
            color: #009688;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: #007bff;
        }

        button[type="submit"] {
            align-self: flex-start;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Adjust this media query for responsiveness */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Fund Details</h2>
        <form method="post">
            <?php if (isset($_SESSION['edit_fund_id'])) : ?>
                <label class="design-label">Problem ID: <?php echo $_SESSION['edit_fund_prob_id']; ?></label>
                <input type="hidden" name="fundProblemId" value="<?php echo $_SESSION['edit_fund_prob_id']; ?>">
                <label class="design-label">Fund ID: <?php echo $_SESSION['edit_fund_id']; ?></label>
                <input type="hidden" name="fund_id" value="<?php echo $_SESSION['edit_fund_id']; ?>">
                <label class="design-label">Funder User ID: <?php echo $_SESSION['edit_fund_user_id']; ?></label>
                <label class="design-label spl-high">Fund Username: <?php echo $_SESSION['edit_fund_username']; ?></label>
                <label class="design-label spl-high">Fund User Phone: <?php echo $_SESSION['edit_fund_user_phone']; ?></label>
                <label class="design-label">Fund Problem ID: <?php echo $_SESSION['edit_fund_prob_id']; ?></label>
                <label class="design-label spl-high">Organization Name: <?php echo $_SESSION['edit_fund_org_name']; ?></label>
                <label class="design-label spl-high">Organization Phone: <?php echo $_SESSION['edit_fund_org_phone']; ?></label>
                <label class="design-label">Organization Location: <?php echo $_SESSION['edit_fund_org_loc']; ?></label>
                <label class="design-label spl-high">Fund Requested Amount [₹] :<?php echo $_SESSION['edit_fund_amount']; ?></label>
                <label class="design-label">Problem Description: <?php echo $_SESSION['edit_fund_prob_desc']; ?></label>
                <label class="design-label">Problem Type: <?php echo $_SESSION['edit_fund_prob_type']; ?></label>
                <label class="design-label">Problem Department: <?php echo $_SESSION['edit_fund_prob_dept']; ?></label>
                <label class="design-label spl-high">Fund Requested Description: <?php echo $_SESSION['edit_fund_req_desc']; ?></label>
                <label class="design-label">Fund Requested Date: <?php echo $_SESSION['edit_fund_req_date']; ?></label>
                <label>Fund Status:</label>
                <select name="fund_status">
                    <option value="--Select Option--" <?php if ($_SESSION['edit_fund_status'] === '--Select Option--') echo 'selected'; ?>>--Select Option--</option>
                    <option value="Approved" <?php if ($_SESSION['edit_fund_status'] === 'Approved') echo 'selected'; ?>>Approved</option>
                    <option value="Discussing" <?php if ($_SESSION['edit_fund_status'] === 'Discussing') echo 'selected'; ?>>Discussing</option>
                    <option value="Reject" <?php if ($_SESSION['edit_fund_status'] === 'Reject') echo 'selected'; ?>>Reject</option>
                </select>
                <label>Government Staff Phone:</label>
                <input type="text" name="govn_staff_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" value="<?php echo $_SESSION['edit_fund_govnstaff_phone']; ?>">
                <label>Notice:</label>
                <textarea name="fund_notice"><?php echo $_SESSION['edit_fund_notice']; ?></textarea>
                <label>Fund Collecting Place:</label>
                <input type="text" name="fund_collect_place" value="<?php echo $_SESSION['edit_fund_collect_place']; ?>">
                <label for="fund_allocated">Fund Allocated:</label>
                <select name="fund_allocated" id="fund_allocated">
                    <option value="-- Select Option --">-- Select Option --</option>
                    <option value="Allocated">Allocated</option>
                    <option value="Not Allocated">Not Allocated</option>
                </select>
                <button type="submit" name="update">Update</button>
            <?php else : ?>
                <p>No fund data available.</p>
            <?php endif; ?>
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