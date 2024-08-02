<?php
// Replace with your actual database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanlink";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$serialNumber = 1;
$sathyserialNumber = 1;

// Check if the "delete" and "table" parameters are present in the URL
if (isset($_GET['delete']) && isset($_GET['table'])) {
    $deleteID = $_GET['delete'];
    $table = $_GET['table'];
    $deleteResult = deleteUser($deleteID, $table);
    if ($deleteResult) {
        // Refresh the page to update the user list after deletion
        echo "<script>window.location.href = 'admin_user_management.php';</script>";
    }
}
// Function to delete a user based on their ID and table name
function deleteUser($userID, $table)
{
    global $conn;
    $column_name = ($table == 'gobi_users') ? 'gobi_user_id' : 'sathy_user_id';
    $query = "DELETE FROM $table WHERE $column_name = '$userID'";
    $result = $conn->query($query);
    return $result;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <title>User Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url("../images/climpek.png"), linear-gradient(to right top, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.8));
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            width: 100%;
            flex-direction: column;
            margin-bottom: 20px;
            position: relative;
            left: -100px;
        }

        .user-table-container {
            width: 115%;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 1);
            backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 10px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 1);
            position: relative;
            top: 20px;
            left: -2px;
            overflow: scroll;
            margin-bottom: 50px;
            max-height: 800px !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            margin-top: 20px;
        }

        thead {
            background-image: url("../images/diamond-upholstery.png"), linear-gradient(to right top, #007bff, #007bff);
            text-align: center;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            /* background-color: #007bff; */
            color: white;
        }

        tr:hover {
            background-color: #989191;
            background-image: url("../images/cutcube.png"), linear-gradient(to right top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0.18));
        }

        td {
            font-weight: 600;
        }

        td:hover {
            cursor: pointer;
            color: white;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }


        /* Add this CSS to style the search bar and buttons */
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
            text-decoration: none;
        }

        .back-button {
            position: relative;
            top: 20px;
            margin-left: 10px;
        }

        .search-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            margin: 20px auto;
            width: 50%;
        }

        .searchInput {
            padding: 10px;
            border: 2px solid black;
            border-radius: 5px;
            margin-right: 10px;
            flex-grow: 1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            transition: background-color 0.3s, box-shadow 0.3s;
            transition: 0.3s ease-in;
        }

        .search-button,
        .clear-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        .search-button {
            border-radius: 5px;
            color: white;
            cursor: pointer;
            border: none !important;
            margin-right: 20px;
            transition: 0.3s ease-in;
            background-image: linear-gradient(to right, #0cc91c 45%, #007bff 55%);
            background-size: 220% 100%;
            background-position: 100% 50%;

            &:hover {
                background-position: 0% 50%;
            }
        }


        .clear-button {
            border-radius: 5px;
            color: white;
            cursor: pointer;
            border: none !important;
            margin-right: 20px;
            transition: 0.3s ease-in;
            background-image: linear-gradient(to right, #0cc91c 45%, #dc3545 55%);
            background-size: 220% 100%;
            background-position: 100% 50%;

            &:hover {
                background-position: 0% 50%;
            }
        }


        .searchInput:focus {
            background-color: #f2f2f2;
            box-shadow: 0 0 10px rgba(0, 123, 255, 1);
            color: #d72323;
            outline: none;
        }

        .searchInput::placeholder {
            color: #ccc;
        }

        .search-button:focus,
        .clear-button:focus {
            outline: none;
        }

        .search-button:active,
        .clear-button:active {
            transform: scale(0.95);
        }

        /* ------------------------------------------------------------------------- */
        .action-buttons {
            width: 100px;
        }

        .btn {
            padding: 5px;
            background-color: #007bff;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.2s ease;
            color: white;
            margin: 7px;
        }

        .btn:hover {
            background-color: rgb(132, 0, 255);
            color: #ffff;
        }

        .btn-2 {
            padding: 5px;
            background-color: red;
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

        /* Pagination Styles */
        .pagination-gobi {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
            position: relative;
        }

        .pagination-gobi a {
            padding: 8px 12px;
            border: 1px solid #007bff;
            border-radius: 4px;
            color: #007bff;
            text-decoration: none;
        }

        .pagination-gobi a.gobi_active {
            background-color: #007bff;
            color: #fff;
        }

        .pagination-sathy {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
            position: relative;
        }

        .pagination-sathy a {
            padding: 8px 12px;
            border: 1px solid #007bff;
            border-radius: 4px;
            color: #007bff;
            text-decoration: none;
        }

        .pagination-sathy a.sathy_active {
            background-color: #007bff;
            color: #fff;
        }
    </style>

</head>

<body>
    <div class="back-button">
        <a href="admin_landing.php" class="go-back">⬅️</a>
    </div>
    <div class="search-container">
        <input type="text" id="searchInput" class="searchInput" placeholder="Search for names..">
        <button onclick="searchTable()" class="search-button">Search</button>
        <button onclick="clearSearch()" class="clear-button">Clear</button>
    </div>

    <div class="container mt-5">
        <h1 class="mb-4">User Management</h1>

        <!-- Display gobi_users data -->
        <div class="user-table-container">
            <h2>Gobi Users</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Phone Number</th>
                        <th>Place</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Address</th>
                        <th>Main Area</th>
                        <th>Account Status</th>
                        <th>MPIN</th>
                        <th>Actions</th> <!-- Added Actions column -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli("localhost", "root", "", "urbanlink");

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $result_gobi = $conn->query("SELECT * FROM gobi_users");

                    if ($result_gobi->num_rows > 0) {
                        while ($row = $result_gobi->fetch_assoc()) {
                            echo "<tr>";
                            echo '<td>' . $serialNumber . '</td>';
                            $serialNumber++;
                            echo "<td>{$row['gobi_user_id']}</td>";
                            echo "<td>{$row['gobi_user_name']}</td>";
                            echo "<td>{$row['gobi_user_dob']}</td>";
                            echo "<td>{$row['gobi_user_phone_number']}</td>";
                            echo "<td>{$row['gobi_user_place']}</td>";
                            echo "<td>{$row['gobi_user_email']}</td>";
                            echo "<td>{$row['gobi_user_password']}</td>";
                            echo "<td>{$row['gobi_user_address']}</td>";
                            echo "<td>{$row['gobi_user_main_area']}</td>";
                            echo "<td>{$row['gobi_user_acc_status']}</td>";
                            echo "<td>{$row['gobi_user_mpin']}</td>";
                            echo '<td class="two-btn">';
                            echo '<div><a href="admin_userprof_update_sep.php?id=' . $row['gobi_user_id'] . '&table=gobi_users" class="btn">Update</a> </div>';
                            echo '<div><a href="?delete=' . $row['gobi_user_id'] . '&table=gobi_users" onclick="return confirmDelete();" class="btn-2">Delete</a></div>';
                            echo '</td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Display sathy_users data -->
        <div class="user-table-container">
            <h2>Sathy Users</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Phone Number</th>
                        <th>Place</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Address</th>
                        <th>Main Area</th>
                        <th>Account Status</th>
                        <th>MPIN</th>
                        <th>Actions</th> <!-- Added Actions column -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result_sathy = $conn->query("SELECT * FROM sathy_users");

                    if ($result_sathy->num_rows > 0) {
                        while ($row = $result_sathy->fetch_assoc()) {
                            echo "<tr>";
                            echo '<td>' . $sathyserialNumber . '</td>';
                            $sathyserialNumber++;
                            echo "<td>{$row['sathy_user_id']}</td>";
                            echo "<td>{$row['sathy_user_name']}</td>";
                            echo "<td>{$row['sathy_user_dob']}</td>";
                            echo "<td>{$row['sathy_user_phone_number']}</td>";
                            echo "<td>{$row['sathy_user_place']}</td>";
                            echo "<td>{$row['sathy_user_email']}</td>";
                            echo "<td>{$row['sathy_user_password']}</td>";
                            echo "<td>{$row['sathy_user_address']}</td>";
                            echo "<td>{$row['sathy_user_main_area']}</td>";
                            echo "<td>{$row['sathy_user_acc_status']}</td>";
                            echo "<td>{$row['sathy_user_mpin']}</td>";
                            echo '<td class="two-btn">';
                            echo '<div><a href="admin_userprof_update_sep.php?id=' . $row['sathy_user_id'] . '&table=sathy_users" class="btn">Update</a> </div>';
                            echo '<div><a href="?delete=' . $row['sathy_user_id'] . '&table=sathy_users" onclick="return confirmDelete();" class="btn-2">Delete</a></div>';
                            echo '</td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13'>No records found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Update -->
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this Profile?");
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Add this code after the search input field -->
    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();

            // Search in both tables
            var tables = document.getElementsByClassName('user-table-container');
            for (j = 0; j < tables.length; j++) {
                table = tables[j].getElementsByTagName('table')[0];
                tr = table.getElementsByTagName('tr');
                for (i = 1; i < tr.length; i++) {
                    var found = false; // Flag to check if any column contains the search term
                    for (var col = 1; col < tr[i].cells.length; col++) { // Start from 1 to skip User ID
                        td = tr[i].getElementsByTagName('td')[col];
                        if (td) {
                            txtValue = td.textContent || td.innerText;
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                found = true;
                                break; // Exit inner loop if found in this row
                            }
                        }
                    }
                    if (found) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }


        function clearSearch() {
            var input = document.getElementById('searchInput');
            input.value = '';

            // Show all rows in the first table (Gobi Users)
            var table1Rows = document.querySelectorAll('.user-table-container')[0].getElementsByTagName('table')[0].getElementsByTagName('tr');
            for (var i = 1; i < table1Rows.length; i++) {
                table1Rows[i].style.display = '';
            }

            // Show all rows in the second table (Sathy Users)
            var table2Rows = document.querySelectorAll('.user-table-container')[1].getElementsByTagName('table')[0].getElementsByTagName('tr');
            for (var i = 1; i < table2Rows.length; i++) {
                table2Rows[i].style.display = '';
            }
        }
    </script>
</body>

</html>