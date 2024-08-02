<?php
session_start();

// Replace with your database connection code
$conn = new mysqli("localhost", "root", "", "urbanlink");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination configuration
$perPage = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$govnStaffLoc = $_SESSION['sv_gvn_staffloc'];
$govnStaffDept = $_SESSION['sv_gvn_staffdept'];

$queryCount = "SELECT COUNT(*) as total FROM funding_details WHERE fund_prob_dept = '$govnStaffDept' AND fund_org_loc = '$govnStaffLoc'";
$resultCount = $conn->query($queryCount);
$totalRows = $resultCount->fetch_assoc()['total'];

$query = "SELECT * FROM funding_details WHERE fund_prob_dept = '$govnStaffDept' AND fund_org_loc = '$govnStaffLoc' ORDER BY fund_req_date DESC LIMIT $offset, $perPage";
$result = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Government Fund Management</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("../images/climpek.png"), linear-gradient(to right top, #f2f2f2, #f2f2f2);
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
            margin: 20px;
            position: relative;
            top: -25px;
            left: -8px;
            border-radius: 5px;
        }

        .go-back:hover {
            background-color: #0088cc;
            transition: 0.2s linear;
        }

        .back-button {
            position: relative;
            top: 40px;
            margin-left: 10px;
        }

        .container {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 2);
            overflow-y: auto;
            overflow-x: scroll;
            height: 620px;
            position: relative;
            /* top: 10px; */
            width: 95%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 1);
            border-radius: 5px;
            cursor: pointer;
        }

        /* tr:nth-child(even) {
            background-color: #BCC6CC;
        } */

        th,
        td {
            padding: 10px;
            text-align: left;
            border: 2px solid black;
            border-radius: 5px;
        }

        td:hover {
            color: #ff6666;
            transition: 0.2s ease-in-out;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .update-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .update-form input[type="text"],
        .update-form textarea,
        .update-form select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .update-form input[type="text"]:focus,
        .update-form textarea:focus,
        .update-form select:focus {
            border-color: #007bff;
        }

        .update-button {
            align-self: flex-start;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-button:hover {
            align-self: flex-start;
            background-color: red;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* CSS for highlighting rows */
        .highlight {
            background-color: #ffcccc;
            /* Light red background */
            border: 2px solid #ff6666;
            /* Dark red border */
        }

        /* CSS for pagination */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            position: relative;
            top: 50%;
        }

        .pagination a {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #333;
        }

        .pagination a.active {
            background-color: #007bff;
            /* Blue background for active page */
            border-color: #007bff;
            color: #fff;
        }

        .pagination a:hover {
            background-color: red;
            border-color: red;
        }


        .gadgets {
            display: flex;
            position: relative;
            float: right;
            justify-content: space-between;
            margin-right: 10px;
        }

        /* Search Bar */
        .search-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .search-input {
            width: 200px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        .clear-icon {
            cursor: pointer;
            border: 1px solid black;
            background-color: greenyellow;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            position: relative;
            left: -5px;
        }

        /* Filter Dropdown */
        .filter-container {
            margin-bottom: 10px;
        }

        .filter-select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .clear-filter {
            margin-left: 10px;
            cursor: pointer;
            border: 1px solid black;
            background-color: greenyellow;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .clear-icon:hover,
        .clear-filter:hover {
            background-color: #ff6666;
            transition: 0.2s ease-in-out;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        /* ---------------MEDIA QUERIES STARTS----------------------*/


        @media (min-width: 701px) and (max-width: 900px) {
            body {
                overflow-y: scroll;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 150%;
                height: 500px;
                position: relative;
                top: 50px;
                margin: 20px;
            }

            .gadgets {
                flex-direction: row;
                align-items: center;
                position: relative;
                min-width: 100%;
                top: 20px;
            }

            .filter-section {
                display: flex;
                flex-direction: column;
                position: relative;
                align-items: flex-start;
            }

            .filter-option {
                width: 200px !important;
            }


            #clear-filters {
                width: 200px;
                margin: 10px 10px;
            }

            .count-container {
                flex-direction: row;
                align-items: center;
                gap: 30px;
                position: relative;
                left: 50px;
            }

            .problem-count {
                width: 100%;
                height: 100px;
                padding: 10px;
                font-size: 0.8rem;

            }

            table {
                width: 100%;
                font-weight: 600;
                font-size: 0.8rem;
            }

            .go-back {
                position: relative;
                top: -20px;
            }
        }

        /* Media Query for screen width 501px - 700px */
        @media (min-width: 501px) and (max-width: 700px) {

            body {
                overflow-y: scroll;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 150%;
                height: 500px;
                position: relative;
                top: 50px;
                margin: 20px;
            }

            .gadgets {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                position: relative;
                top: 30px;
                left: -210px;
                padding: 20px;
            }

            .filter-section {
                display: flex;
                flex-direction: column;
                position: relative;
                align-items: flex-start;
            }

            .filter-option {
                width: 200px !important;
            }


            #clear-filters {
                width: 200px;
                margin: 10px 10px;
            }

            .count-container {
                flex-direction: row;
                align-items: center;
                gap: 30px;
                position: relative;
                left: 50px;
            }

            .problem-count {
                width: 100px;
                height: 100px;
                padding: 10px;
                font-size: 0.8rem;

            }

            table {
                width: 100%;
                font-weight: 600;
                font-size: 0.8rem;
            }

            .go-back {
                position: relative;
                top: -20px;
            }
        }

        /* Media Query for screen width 300px - 500px */
        @media (min-width: 300px) and (max-width: 500px) {

            body {
                overflow-y: scroll;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 150%;
                position: relative;
                top: 50px;
                margin: 20px;
            }

            .gadgets {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                position: relative;
                top: 30px;
                left: -110px;
                padding: 20px;
            }

            #clear-filters {
                width: 200px;
                margin: 10px 10px;
            }


            table {
                width: 100%;
                font-weight: 600;
                font-size: 0.8rem;
                border-radius: 10px;
                border: 2px solid #aaa;
            }

            th,
            tr,
            td {
                border: 2px solid #aaa;
                border-radius: 10px;
            }

            .go-back {
                position: relative;
                top: -20px;
            }
        }

        /* ---------------MEDIA QUERIES ENDS ----------------------*/
    </style>
</head>

<body>
    <div class="back-button">
        <a href="govn_landing.php" class="go-back">⬅️ GO BACK</a>
    </div>
    <div class="gadgets">
        <div class="search-container">
            <input type="text" id="searchBar" class="search-input" placeholder="Search by Organization Name">
            <span id="clearSearch" class="clear-icon" style="cursor: pointer;">&#10006;</span>
        </div>
        <div class="filter-container">
            <select id="statusFilter" class="filter-select">
                <option value="">Filter by Fund Status</option>
                <option value="Approved">Approved</option>
                <option value="Discussing">Discussing</option>
                <option value="Pending">Pending</option>
                <option value="Not Updated">Not Updated</option>
            </select>
            <button id="clearFilter" class="clear-filter">Clear Filter</button>
        </div>

    </div>

    <div class="container">
        <h2>Government Fund Management</h2>
        <table>
            <tr>
                <th>Fund ID</th>
                <th>Funder User ID</th>
                <th>Fund Username</th>
                <th>Fund User Phone</th>
                <th>Fund Problem ID</th>
                <th>Organization Name</th>
                <th>Organization Location</th>
                <th>Organization Phone</th>
                <th>Amount</th>
                <th>Problem Description</th>
                <th>Problem Type</th>
                <th>Problem Department</th>
                <th>Fund Request Description</th>
                <th>Fund Request Date</th>
                <th>Fund Status</th>
                <th>Government Staff Phone</th>
                <th>Notice</th>
                <th>Fund Collect Place</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

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

                    $isNewerRequest = empty($row['fund_status']) || empty($row['fund_govnstaff_phone']) || empty($row['fund_collect_place']) || empty($row['fund_notice']);
                    $highlightClass = $isNewerRequest ? 'highlight' : '';

                    // echo "<tr class='$highlightClass'>";
                    echo "<tr class='$highlightClass' data-status='" . $row['fund_status'] . "' data-orgname='" . strtolower($row['fund_org_name']) . "'>";
                    echo "<td>" . $row['fund_id'] . "</td>";
                    echo "<td>" . $row['fund_user_id'] . "</td>";
                    echo "<td>" . $row['fund_user_name'] . "</td>";
                    echo "<td>" . $row['fund_user_phone'] . "</td>";
                    echo "<td>" . $row['fund_prob_id'] . "</td>";
                    echo "<td>" . $row['fund_org_name'] . "</td>";
                    echo "<td>" . $row['fund_org_loc'] . "</td>";
                    echo "<td>" . $row['fund_org_phone'] . "</td>";
                    echo "<td>" . $row['fund_amount'] . "</td>";
                    echo "<td>" . $row['fund_prob_desc'] . "</td>";
                    echo "<td>" . $row['fund_prob_type'] . "</td>";
                    echo "<td>" . $row['fund_prob_dept'] . "</td>";
                    echo "<td>" . $row['fund_req_desc'] . "</td>";
                    echo "<td>" . $row['fund_req_date'] . "</td>";
                    echo "<td>" . $row['fund_status'] . "</td>";
                    echo "<td>" . $row['fund_govnstaff_phone'] . "</td>";
                    echo "<td>" . $row['fund_notice'] . "</td>";
                    echo "<td>" . $row['fund_collect_place'] . "</td>";
                    echo "<td><a href='govn_fund_handle.php?edit=" . $row['fund_id'] . "' class='update-button'>Edit</a></td>";
                    // echo "<td><a href='govn_fund_handle.php' class='update-button'>Edit</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='16'>No fund requests found.</td></tr>";
            }
            ?>
        </table>
        <?php
        // Pagination links
        $totalPages = ceil($totalRows / $perPage);
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = $i === $page ? 'active' : '';
            echo "<a class='$activeClass' href='govn_fund_manage.php?page=$i'>$i</a>";
        }
        echo "</div>";
        ?>
    </div>

    <!-- JS FOR FILTER BELOW -->
    <script>
        const statusFilter = document.getElementById('statusFilter');
        const clearFilter = document.getElementById('clearFilter');

        statusFilter.addEventListener('change', () => {
            const selectedStatus = statusFilter.value;
            const rows = document.querySelectorAll('tr[data-status]');
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (selectedStatus === '' || status === selectedStatus) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        clearFilter.addEventListener('click', () => {
            statusFilter.value = '';
            const rows = document.querySelectorAll('tr[data-status]');
            rows.forEach(row => {
                row.style.display = 'table-row';
            });
        });
    </script>


    <!-- JS FOR SEARCH BAR BELOW -->
    <script>
        const searchBar = document.getElementById('searchBar');
        const clearSearch = document.getElementById('clearSearch');

        searchBar.addEventListener('input', () => {
            const searchTerm = searchBar.value.toLowerCase();
            const rows = document.querySelectorAll('tr[data-orgname]');
            rows.forEach(row => {
                const orgName = row.getAttribute('data-orgname').toLowerCase();
                if (orgName.includes(searchTerm)) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        clearSearch.addEventListener('click', () => {
            searchBar.value = '';
            const rows = document.querySelectorAll('tr[data-orgname]');
            rows.forEach(row => {
                row.style.display = 'table-row';
            });
        });
    </script>
</body>

</html>