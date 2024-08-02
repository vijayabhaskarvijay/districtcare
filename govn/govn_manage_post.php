    <?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['sv_gvn_staffname']) || !isset($_SESSION['sv_gvn_staffid'])) {
        header('Location: govn_login.php');
        exit();
    }

    // Include database configuration
    require_once 'gp_config.php';

    // Get the user ID from the session
    $user_id = $_SESSION['sv_gvn_staffid'];

    // Get posts for the logged-in user
    $query = "SELECT * FROM govn_posts WHERE gp_staff_id = :user_id ORDER BY gp_time DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_post'])) {
            $post_id = $_POST['delete_post'];

            // Delete the post from the database
            $query = "DELETE FROM govn_posts WHERE gp_id = :gp_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':gp_id', $post_id);
            $stmt->execute();

            // Redirect to the same page to refresh the post list
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Calculate total number of posts
    $totalPosts = count($posts);
    // Define posts per page
    $postsPerPage = 7;
    // Calculate total number of pages
    $totalPages = ceil($totalPosts / $postsPerPage);
    // Get the current page number from URL query parameter
    $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    // Calculate offset
    $offset = ($current_page - 1) * $postsPerPage;
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Govn Post Management</title>
        <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                background-image: url("../images/5042209_810.jpg");
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                margin: 0;
                padding: 0;
            }

            .pagination {
                margin-top: 20px;
                text-align: center;
            }

            .pagination a {
                display: inline-block;
                padding: 5px 10px;
                margin: 0 5px;
                border: 1px solid #ccc;
                background-color: #f5f5f5;
                text-decoration: none;
                color: #333;
            }

            .pagination a.active {
                background-color: #007bff;
                color: #fff;
                border: 1px solid #007bff;
            }

            .pagination a:hover {
                background-color: #ddd;
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
                top: 30px;
                margin-left: 10px;
            }

            .container {
                width: 60%;
                margin: 50px auto;
                background-color: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            }

            h2 {
                text-align: center;
                color: #333333;
                margin-bottom: 20px;
            }

            .post-list {
                list-style: none;
                padding: 0;
            }

            .post-item {
                border: 1px solid #cccccc;
                border-radius: 5px;
                padding: 10px;
                margin-bottom: 10px;
                background-color: #ffffff;
            }

            .post-item .post-info {
                margin-bottom: 10px;
            }

            .post-item .tags {
                display: flex;
                flex-wrap: wrap;
                margin-bottom: 10px;
            }

            .post-item .tag {
                padding: 5px 10px;
                font-size: 12px;
                color: #ffffff;
                border-radius: 3px;
                margin-right: 5px;
            }

            .post-item .username-tag {
                background-color: #3498db;
            }

            .post-item .phone-tag {
                background-color: #2ecc71;
            }

            .post-item .location-tag {
                background-color: #033e3e;
            }

            /* .post-item .area-tag {
                background-color: #ff00ff;
            } */

            .post-item .date-tag {
                background-color: #000000;
            }

            .post-item .description {
                padding: 10px;
                background-color: #f2f2f2;
                border-radius: 3px;
                margin-bottom: 10px;
            }

            .post-item .post-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }

            .post-actions button {
                margin-left: 5px;
                padding: 5px 10px;
                background-color: #0088cc;
                color: #ffffff;
                border: none;
                cursor: pointer;
                border-radius: 3px;
                transition: transform 0.3s;
            }

            .post-actions button:hover {
                transform: scale(1.1);
            }

            .modal {
                display: none;
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.4);
            }

            .modal-content {
                background-color: #fefefe;
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 90%;
                max-width: 400px;
                border-radius: 5px;
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
            select {
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

            input[type="submit"] {
                width: 100%;
                padding: 10px;
                background-color: #0088cc;
                color: #ffffff;
                border: none;
                cursor: pointer;
                border-radius: 3px;
            }

            .success-message {
                color: #008000;
                margin-bottom: 10px;
            }

            .error-message {
                color: #FF0000;
                margin-bottom: 10px;
            }

            @media (max-width: 600px) {
                .container {
                    width: 90%;
                    padding: 5px;
                }

                .post-item .tag {
                    padding: 10px;
                    font-size: 12px;
                    color: #ffffff;
                    border-radius: 3px;
                    margin-right: 5px;
                }

                .post-item .tags {
                    display: flex;
                    flex-wrap: wrap;
                    margin-bottom: 10px;
                    padding: 10px;
                }


            }

            .post-actions button {
                margin-left: 5px;
                padding: 5px 10px;
            }
        </style>
    </head>

    <body>
        <div class="back-button">
            <a href="govn_landing.php" class="go-back">⬅️ GO BACK</a>
        </div>
        <div class="container">
            <h2>Govn Post Management</h2>

            <?php if (isset($_SESSION['success_message'])) : ?>
                <div class="success-message"><?php echo $_SESSION['success_message']; ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])) : ?>
                <div class="error-message"><?php echo $_SESSION['error_message']; ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            <ul class="post-list">
                <?php if (!empty($posts)) : ?>
                    <?php foreach ($posts as $post) : ?>
                        <li class="post-item">
                            <div class="post-info">
                                <div class="tags">
                                    <span class="tag username-tag"><?php echo $post['gp_staff_name']; ?></span>
                                    <span class="tag phone-tag"><?php echo $post['gp_staff_phone']; ?></span>
                                    <span class="tag location-tag"><?php echo $post['gp_loc']; ?></span>
                                    <span class="tag date-tag"><?php echo $post['gp_date']; ?></span>
                                </div>
                                <div class="description">
                                    <?php echo $post['gp_desc']; ?>
                                </div>
                            </div>
                            <div class="post-actions">
                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                    <button type="submit" name="delete_post" value="<?php echo $post['gp_id']; ?>">Delete</button>
                                </form>
                                <form method="post" action="gp_update_sep.php">
                                    <input type="hidden" name="post_id" value="<?php echo $post['gp_id']; ?>">
                                    <button type="submit">Update</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li class="post-item">No Govn posts Created.👍</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="pagination">
            <?php for ($page = 1; $page <= $totalPages; $page++) : ?>
                <a href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
            <?php endfor; ?>
        </div>

    </body>

    </html>