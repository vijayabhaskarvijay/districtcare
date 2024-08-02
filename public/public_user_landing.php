<?php
// Logout PHP code
if (isset($_POST['logout'])) {
    // Destroy session
    session_start();
    session_destroy();
    // Redirect to index.php
    header("Location: ../index.php");
    exit();
}

// Start session
session_start();

// Check if logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page
    header("Location: main_login.php");
    exit();
}

if (isset($_SESSION['user_phone'])) {
    $userPhone = $_SESSION['user_phone'];
} else {
    // Handle case when user_phone is not set in the session
    $userPhone = "N/A";
}

// Set session cookie with expiration time
$expire = time() + (60 * 60); // 1 hour
setcookie(session_name(), session_id(), $expire);

// Replace the database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanlink";

// Retrieve posts based on the selected option
$selectedOption = isset($_GET['option']) ? $_GET['option'] : '';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the default value for selectedOption
$selectedOption = 'public_posts';

// Check if selectedOption exists in the $_GET array
if (isset($_GET['option'])) {
    // Assign the value of $_GET['option'] to selectedOption
    $selectedOption = $_GET['option'];
}

// Function to retrieve and display posts
function displayPostsPagination($tableName, $title, $postIDColumn, $postColumns, $creationTimeColumn, $currentPage, $postsPerPage)
{
    global $conn, $selectedOption;
    // Calculate the starting row for the current page
    $startRow = ($currentPage - 1) * $postsPerPage;

    // Display user ID based on the selected option
    $userIdColumn = '';
    // Display user ID based on the selected option
    switch ($selectedOption) {
        case 'public_posts':
            $userIdColumn = 'pp_user_id';
            break;
        case 'ngo_post_details':
            $userIdColumn = 'ngo_user_id';
            break;
        case 'admin_posts':
            $userIdColumn = 'ap_admin_id';
            break;
        case 'govn_posts':
            $userIdColumn = 'gp_staff_id';
            break;
        default:
            // Set a default value for $userIdColumn in case none of the options match
            $userIdColumn = '';
    }

    $query = "SELECT * FROM $tableName ORDER BY $creationTimeColumn DESC LIMIT $startRow, $postsPerPage";
    $result = $conn->query($query);

    echo '<div id="' . $tableName . '" class="post-container">';
    echo '<h2>' . $title . '</h2>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $postID = $row[$postIDColumn];
            echo '<div class="post">';
            echo '<div class="details">';
            echo '<span class="tag-username">' . $row[$postColumns[0]] . '</span>';
            echo '<span class="tag-phone">' . $row[$postColumns[1]] . '</span>';
            echo '<span class="tag-location">' . $row[$postColumns[2]] . '</span>';
            echo '<span class="tag-date">' . $row[$postColumns[3]] . '</span>';
            echo '<span class="tag-type">' . $row[$postColumns[4]] . '</span>';
            // echo '<span class="tag-userid">' . $row[$userIdColumn] . '</span>';
            echo '</div>';
            echo '<div class="description">' . $row[$postColumns[5]] . '</div>';

            // Add the "Report" button with necessary details
            echo '<form action="../report_user.php" method="post">';
            echo '<input type="hidden" name="reported_user_id" value="' . $row[$userIdColumn] . '">';
            echo '<input type="hidden" name="reported_user_name" value="' . $row[$postColumns[0]] . '">';
            echo '<input type="hidden" name="reported_user_location" value="' . $row[$postColumns[2]] . '">';
            echo '<input type="hidden" name="reported_user_phone" value="' . $row[$postColumns[1]] . '">';
            echo '<button type="submit" name="report" class="report-button">Report</button>';  //report button added here
            echo '</form>';

            echo '</div>';
        }
    } else {
        echo '<p>No posts to Check👍.</p>';
    }

    echo '</div>';

    echo '<script>
        function expandPost(postId) {
            // Hide post descriptions
            $(".post-description").hide();
            // Show the full post details for the clicked post
            $("#" + postId).show();
            // Create and display a back button
            $("#back-button").show();
        }

        function goBack() {
            // Hide full post details
            $(".post-details").hide();
            // Show post descriptions again
            $(".post-description").show();
            // Hide the back button
            $("#back-button").hide();
        }

        $(document).ready(function() {
            // Attach click event handlers to post descriptions
            $(".post-description").click(function() {
                var postId = $(this).attr("id");
                expandPost(postId);
            });

            // Attach click event handler to the back button
            $("#back-button").click(function() {
                goBack();
            });
        });
    </script>';
}

// Array of post options and their details
$postOptions = [
    [
        'tableName' => 'public_posts',
        'title' => 'Public Posts',
        'postIDColumn' => 'pp_id',
        'postColumns' => ['pp_username', 'pp_userphone', 'pp_userloc', 'pp_date', 'pp_type', 'pp_userpost_description'],
        'creationTimeColumn' => 'pp_time'
    ],
    [
        'tableName' => 'ngo_post_details',
        'title' => 'NGO Posts',
        'postIDColumn' => 'ngo_post_id',
        'postColumns' => ['ngo_user_name', 'ngo_user_email', 'ngo_user_phone', 'ngo_post_date', 'ngo_post_type', 'ngo_post_desc'],
        'creationTimeColumn' => 'ngo_post_time'
    ],
    [
        'tableName' => 'admin_posts',
        'title' => 'Admin Posts',
        'postIDColumn' => 'ap_id',
        'postColumns' => ['ap_admin_name', 'ap_admin_phone', 'ap_place', 'ap_date', 'ap_type', 'ap_desc'],
        'creationTimeColumn' => 'ap_time'
    ],
    [
        'tableName' => 'govn_posts',
        'title' => 'Government Announcements and Updates',
        'postIDColumn' => 'gp_id',
        'postColumns' => ['gp_staff_name', 'gp_staff_phone', 'gp_loc', 'gp_date', 'gp_type', 'gp_desc'],
        'creationTimeColumn' => 'gp_time'
    ]
];

// Function to get the total number of posts for a given table
function getTotalPostsCount($tableName)
{
    global $conn;
    $query = "SELECT COUNT(*) AS total FROM $tableName";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    return 0;
}


?>


<!DOCTYPE html>
<html>

<head>
    <title>Public Landing</title>
    <meta charset="UTF-8">
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <link rel="stylesheet" href="public_landing_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            let hamburger = $('#hamburger_menu');
            let list = $('#list');
            hamburger.click(function() {
                list.toggleClass('collapsed');
            });
        });
    </script>
    <!-- <script>
        $(window).on('unload', function() {
            $.ajax({
                url: 'logout.php',
                method: 'POST',
                async: false
            });
        });
    </script> -->
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- FILTER DROPDOWN MENU STARTS -->
    <div class="gadgets">
        <div class="filter-bar">
            <label for="filter-select">Filter Type:</label>
            <select id="filter-select" name="filter">
                <option value="Select Options">Select Options</option>
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
            </select>
            <button id="clear-filter">Clear Filter</button>
        </div>
        <!-- FILTER DROPDOWN MENU ENDS -->
        <!-- SEARCH BAR COMPLETE CODE STARTS -->
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Search...">
            <button id="search-button">Search</button>
        </div>
        <script>
            $(document).ready(function() {
                // Filter posts based on problem type
                $('#filter-select').change(function() {
                    var selectedType = $(this).val().toLowerCase();
                    if (selectedType === '') {
                        $('.post').show();
                    } else {
                        $('.post').each(function() {
                            var type = $(this).find('.tag-type').text().toLowerCase();
                            if (type !== selectedType) {
                                $(this).hide();
                            } else {
                                $(this).show();
                            }
                        });
                    }
                });
                // Clear Filter button click event
                $('#clear-filter').click(function() {
                    // Reset the filter dropdown to the default option
                    $('#filter-select').prop('selectedIndex', 0);
                    // Show all posts
                    $('.post').show();
                });
                // Function to filter and display posts based on search input
                function filterPosts(searchTerm) {
                    $('.post').each(function() {
                        var description = $(this).find('.description').text().toLowerCase();
                        if (description.indexOf(searchTerm) === -1) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });
                }
                // Search button click event
                $('#search-button').click(function() {
                    var searchTerm = $('#search-input').val().toLowerCase();
                    filterPosts(searchTerm);
                });
                // Clear search input and show all posts
                $('#search-input').on('input', function() {
                    var searchTerm = $(this).val().toLowerCase();
                    if (searchTerm === '') {
                        $('.post').show();
                    }
                });
            });
        </script>
    </div>

    <!-- SEARCH BAR COMPLETE CODE ENDS -->
    <!-- SIDEBAR STARTS-->
    <div class="s-layout">
        <div class="s-layout__sidebar">
            <a class="s-sidebar__trigger" href="#0">
                <i class="fa fa-bars"></i>
            </a>
            <nav class="s-sidebar__nav">
                <ul>
                    <li>
                        <div class="logo">
                            <img src="../images/urbanlink-low-resolution-logo-color-on-transparent-background.png" alt="Logo" class="logo-image">
                        </div>
                    </li>
                    <li>
                        <a class="s-sidebar__nav-link" href="public_user_profile.php">
                            <i class="fa fa-user"></i><em> Profile</em>
                        </a>
                    </li>
                    <hr class="hr-1">
                    <li>
                        <a class="s-sidebar__nav-link" href="public_post_creation.php">
                            <i class="fa-solid fa-plus"></i><em>Create Post</em>
                        </a>
                    </li>
                    <hr class="hr-1">
                    <li>
                        <a class="s-sidebar__nav-link" href="public_post_manage.php">
                            <i class="fa fa-camera"></i><em>Manage Post</em>
                        </a>
                    </li>
                    <hr class="hr-1">
                    <li>
                        <a class="s-sidebar__nav-link" href="public_prob_creation.php">
                            <i class="fa-solid fa-users"></i><em>Report Area Problems</em>
                        </a>
                    </li>
                    <hr class="hr-1">
                    <li>
                        <a class="s-sidebar__nav-link" href="public_prob_manage.php">
                            <i class="fa fa-area-chart" aria-hidden="true"></i><em>Manage Reported Problems</em>
                        </a>
                    </li>
                    <hr class="hr-1">
                    <li>
                        <a class="s-sidebar__nav-link" href="public_help_faq.php">
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i><em>FAQ - HELP</em>
                        </a>
                    </li>
                    <form method="POST" action="">
                        <button type="submit" class="log" name="logout"><i class="fa-solid fa-arrow-right-from-bracket"> Logout</i></button>
                    </form>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- SIDEBAR END-->
    <!-- Main Container -->
    <div class="main-container">
        <div class="options-container box">
            <?php
            // Display options
            foreach ($postOptions as $option) {
                $tableName = $option['tableName'];
                $title = $option['title'];
                $activeClass = ($selectedOption === $tableName) ? 'active' : '';
                echo '<a href="?option=' . $tableName . '" class="' . $activeClass . '">' . $title . '</a>';
            }
            ?>
        </div>

        <div id="posts-container" class="post-container">

            <?php
            $selectedOption = isset($_GET['option']) ? $_GET['option'] : 'public_posts';
            // Calculate the total number of posts
            $totalPosts = getTotalPostsCount($selectedOption);

            // Define the number of posts to display per page
            $postsPerPage = 5;

            // Calculate the total number of pages
            $totalPages = ceil($totalPosts / $postsPerPage);

            // Get the current page number
            $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

            // Calculate the offset for the SQL query
            $offset = ($currentPage - 1) * $postsPerPage;

            // Display posts based on the selected option and current page
            foreach ($postOptions as $option) {
                $tableName = $option['tableName'];
                $title = '<h2 class="post-title">' . $option['title'] . '</h2>';
                $postIDColumn = $option['postIDColumn'];
                $postColumns = $option['postColumns'];
                $creationTimeColumn = $option['creationTimeColumn'];

                if ($selectedOption === $tableName) {
                    // Get the current page from the query parameter, default to 1
                    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    // Number of posts to display per page
                    $postsPerPage = 3;

                    // Call the displayPostsPagination function
                    displayPostsPagination($tableName, $title, $postIDColumn, $postColumns, $creationTimeColumn, $currentPage, $postsPerPage);

                    // Add pagination links
                    $totalPosts = getTotalPostsCount($tableName);
                    $totalPages = ceil($totalPosts / $postsPerPage);
                }
            }
            ?>
        </div>
    </div>

    <?php
    $startTime = microtime(true); // Get the current timestamp1
    $endTime = microtime(true); // Get the timestamp after executing the PHP code
    $loadTime = $endTime - $startTime; // Calculate the total loading time in seconds
    // Display the loader container if the loading time exceeds a threshold (e.g., 2 seconds)
    if ($loadTime > 2) {
        echo '<div class="loader-container">';
        echo '<div class="loader"></div>';
        echo '</div>';
    }
    ?>
    <script>
        // Remove the loader container once the page is fully loaded
        window.addEventListener("load", function() {
            var loaderContainer = document.querySelector(".loader-container");
            if (loaderContainer) {
                loaderContainer.style.display = "none";
            }
        });
    </script>


    <div class="pagination">
        <?php
        for ($page = 1; $page <= $totalPages; $page++) {
            echo '<a href="?option=' . $selectedOption . '&page=' . $page . '"';
            if ($page === $currentPage) {
                echo ' class="active"';
            }
            echo '>' . $page . '</a>';
        }
        ?>
    </div>
</body>

</html>
<?php
// Close database connection
$conn->close();
?>