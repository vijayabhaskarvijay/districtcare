<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['sv_admin_username']) || !isset($_SESSION['sv_admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Include database configuration
require_once 'ap_config.php';

// Handle update and delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $reportedUserId = $_POST['reported_user_id'];
        $reportedUserLocation = $_POST['reported_user_loc'];
        $newStatus = $_POST['status'];

        if ($reportedUserLocation === 'Gobichettipalayam') {
            // Update the gobi_users table
            $query = "UPDATE gobi_users SET gobi_user_acc_status = :new_status WHERE gobi_user_id = :reported_user_id";
        } elseif ($reportedUserLocation === 'Sathymangalam') {
            // Update the sathy_users table
            $query = "UPDATE sathy_users SET sathy_user_acc_status = :new_status WHERE sathy_user_id = :reported_user_id";
        } else {
            // Invalid location, handle the error appropriately
            // You may want to display an error message or take other actions here
            // For simplicity, we will exit the script.
            exit("Invalid user location");
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':new_status', $newStatus);
        $stmt->bindParam(':reported_user_id', $reportedUserId);
        $stmt->execute();
        $successMessage = "User status updated successfully!";
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } elseif (isset($_POST['delete'])) {
        $reportId = $_POST['user_report_id'];

        $query = "DELETE FROM reported_users WHERE user_report_id = :user_report_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_report_id', $reportId);
        $stmt->execute();
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
}

// Fetch reported user list from the database
$query = "SELECT * FROM reported_users";
$stmt = $conn->prepare($query);
$stmt->execute();
$reportedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Manage User Reports</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            background-image: url("../images/12427669_4962240.jpg");
            background-size: cover;
            background-position: 0 1px;
            background-repeat: no-repeat;
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
        }

        .go-back:hover {
            background-color: #0088cc;
            transition: 0.2s linear;
        }

        .back-button {
            position: relative;
            top: 20px;
            margin-left: 10px;
        }

        .container {
            width: 95%;
            margin: 20px auto;
            background-color: #BCC6CC;
            /* background-color: rgba(255, 255, 255, 0.9); */
            backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 10px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            top: 20px;
            left: -2px;
            overflow-x: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Table styles */
        .btn {
            padding: 5px;
            background-color: #007bff;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.2s ease;
            color: white;
            margin: 7px;
            display: flex;
            flex-direction: column;
        }

        .btn:hover {
            background-color: rgb(132, 0, 255);
            color: #ffff;
        }

        .btn-2 {
            padding: 5px;
            background-color: #007bff;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.2s ease;
            color: white;
            margin: 7px;
            position: relative;
            top: 5px;
        }

        .btn-2:hover {
            background-color: rgb(132, 0, 255);
            color: #ffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #989191;
        }

        td {
            font-weight: 600;
        }

        td:hover {
            cursor: pointer;
            color: white;
        }


        /* Form layout */
        .form-container {
            width: 80%;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 10px;
            height: 550px;
            position: relative;
            top: 50px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        .form-container h1 {
            text-align: center;
            color: #333;
        }

        .form-container label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }

        .form-container input {
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;

        }

        .form-container select {
            margin-bottom: 15px;
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;

        }

        /* Add background colors for the select options */
        option.blocked {
            background-color: #ff9999;
        }

        option.unblocked {
            background-color: #99ff99;
        }

        /* Add background colors for the selected option */
        select.blocked {
            background-color: #ff9999;
        }

        select.unblocked {
            background-color: #99ff99;
        }

        .form-container textarea {
            resize: vertical;
            height: 100px;
        }

        .form-container input[type="submit"] {
            background-color: #3090C7;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            padding: 10px 20px;
            width: 50%;
            position: relative;
            left: 30%;
            top: 50px;
        }

        .form-container input[type="submit"]:hover {
            background-color: #e57373;
        }

        /* Media queries */
        /* Media Query for screen width 501px - 700px */
        @media (min-width: 300px) and (max-width: 500px) {

            body {
                background-image: none;
                background-color: #657383;
                width: 100%;
                padding: 20px;
            }

            .container {
                width: 700px;
                background-color: #E6E6FA;
            }

            .form-container {
                width: 600px;
            }

            table {
                border-collapse: collapse;
                margin-top: 10px;
                position: relative;
                left: -10px;
                border: 3px solid #F75D59;
                width: 100%;
            }

            td,
            th {
                border: 3px solid #F75D59;
                text-align: center;
                padding: 5px;
            }
        }

        @media (min-width: 501px) and (max-width: 700px) {

            body {
                background-image: none;
                background-color: #657383;
                width: 100%;
                padding: 20px;
            }

            .container {
                width: 700px;
                background-color: #E6E6FA;
            }

            .form-container {
                width: 600px;
            }

            table {
                border-collapse: collapse;
                margin-top: 10px;
                position: relative;
                left: -10px;
                border: 3px solid #F75D59;
                width: 100%;
            }

            td,
            th {
                border: 3px solid #F75D59;
                text-align: center;
                padding: 5px;
            }
        }

        /* Media Query for screen width 701px - 900px */
        @media (min-width: 701px) and (max-width: 750px) {

            body {
                background-image: none;
                background-color: #657383;
                width: 100%;
                padding: 20px;
            }

            .container {
                width: 700px;
                background-color: #E6E6FA;
            }

            .form-container {
                width: 600px;
            }

            table {
                border-collapse: collapse;
                margin-top: 10px;
                position: relative;
                left: -10px;
                border: 3px solid #F75D59;
                width: 100%;
            }

            td,
            th {
                border: 3px solid #F75D59;
                text-align: center;
                padding: 5px;
            }
        }

        /* Media Query for screen width 901px - 1100px */
        @media (min-width: 751px) and (max-width: 840px) {

            body {
                background-image: none;
                background-color: #657383;
                width: 100%;
                padding: 20px;
            }

            .container {
                width: 700px;
            }

            .form-container {
                width: 600px;
            }

            table {
                border-collapse: collapse;
                margin-top: 10px;
                position: relative;
                left: -10px;
                border: 3px solid black;
                width: 100%;
                text-transform: capitalize;
            }

            td,
            th {
                border: 3px solid black;
                text-align: center;
                padding: 5px;
            }

            tr:nth-child(odd) {
                background-color: #52595D;
                color: white;
            }

            tr:nth-child(even) {
                background-color: #98AFC7;
            }
        }
    </style>
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="back-button">
        <a href="admin_landing.php" class="go-back">⬅️</a>
    </div>
    <div class="container">
        <h2>Reported Users List</h2>
        <!-- Add success message -->
        <?php if (isset($successMessage)) : ?>
            <div id="successMessage" style="background-color: #5cb85c; color: #fff; padding: 10px; text-align: center; margin-bottom: 10px;">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Reported User ID</th>
                    <th>Reported User Name</th>
                    <th>Reported User Phone</th>
                    <th>Reported User Location</th>
                    <th>Report Reason</th>
                    <th>Report Date</th>
                    <th>Report Type</th>
                    <th>Reporting User ID</th>
                    <th>Reporting User Name</th>
                    <th>Reporting User Phone</th>
                    <th>Update Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportedUsers as $user) : ?>
                    <tr>
                        <td><?php echo $user['reported_user_id']; ?></td>
                        <td><?php echo $user['reported_user_name']; ?></td>
                        <td><?php echo $user['reported_user_phone']; ?></td>
                        <td><?php echo $user['reported_user_loc']; ?></td>
                        <td><?php echo $user['report_reason']; ?></td>
                        <td><?php echo $user['report_date']; ?></td>
                        <td><?php echo $user['report_type']; ?></td>
                        <td><?php echo $user['reporting_user_id']; ?></td>
                        <td><?php echo $user['reporting_user_name']; ?></td>
                        <td><?php echo $user['reporting_user_phone']; ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="reported_user_id" value="<?php echo $user['reported_user_id']; ?>">
                                <input type="hidden" name="reported_user_loc" value="<?php echo $user['reported_user_loc']; ?>">
                                <?php
                                // Determine the appropriate table name based on the reported location
                                $tableName = ($user['reported_user_loc'] === 'Gobichettipalayam') ? 'gobi_users' : 'sathy_users';
                                $idcolumnName = ($user['reported_user_loc'] === 'Gobichettipalayam') ? 'gobi_user' : 'sathy_user';
                                $acc_status = ($user['reported_user_loc'] === 'Gobichettipalayam') ? 'gobi_user_acc_status' : 'sathy_user_acc_status';

                                // Fetch the acc_status value from the corresponding table
                                $query = "SELECT $acc_status FROM $tableName WHERE {$idcolumnName}_id = :reported_user_id";
                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':reported_user_id', $user['reported_user_id']);
                                $stmt->execute();
                                $userStatus = $stmt->fetchColumn();
                                ?>
                                <select name="status" class="<?php echo strtolower($userStatus); ?>">
                                    <option value="--SELECT--">--SELECT--</option>
                                    <option value="BLOCKED" class="blocked" <?php if (strtolower($userStatus) === 'blocked') echo 'selected'; ?>>BLOCKED</option>
                                    <option value="UNBLOCKED" class="unblocked" <?php if (strtolower($userStatus) === 'unblocked') echo 'selected'; ?>>UNBLOCKED</option>
                                </select>
                                <input type="submit" name="update" value="Save">
                            </form>
                        </td>
                        <td>
                            <!-- <form method="post">
                                <input type="hidden" name="user_report_id" value="<?php echo $user['user_report_id']; ?>">
                                <i class="fa-solid fa-trash">
                                <input type="submit" name="delete" value="Delete">
                                </i>
                            </form> -->
                            <form method="post" onsubmit="return confirmDelete()">
                                <input type="hidden" name="user_report_id" value="<?php echo $user['user_report_id']; ?>">
                                <button type="submit" name="delete" style=" border: none; cursor:pointer; height:25px; width:25px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript to hide the success message after 3 seconds -->
    <script>
        // Function to hide the success message
        function hideSuccessMessage() {
            var successMessage = document.getElementById("successMessage");
            if (successMessage) {
                successMessage.style.display = "none";
            }
        }

        // Hide the success message after 3 seconds
        var successMessage = document.getElementById("successMessage");
        if (successMessage) {
            setTimeout(hideSuccessMessage, 3000); // 3000 milliseconds = 3 seconds
        }
    </script>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this user?");
        }
    </script>

</body>

</html>