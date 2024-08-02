    <?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }

    // Include database configuration
    require_once 'config.php';

    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Get posts for the logged-in user
    $query = "SELECT * FROM public_contact_help WHERE pch_user_id = :user_id ORDER BY pch_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_post'])) {
            $post_id = $_POST['delete_post'];

            // Delete the post from the database
            $query = "DELETE FROM public_contact_help WHERE pch_id = :pch_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':pch_id', $post_id);
            $stmt->execute();

            // Redirect to the same page to refresh the post list
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Calculate total number of posts
    $totalPosts = count($posts);
    // Define posts per page
    $postsPerPage = 6;
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
        <title>Public Questions Manage</title>
        <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                background-color: #556;
                background-image: linear-gradient(30deg, #445 12%, transparent 12.5%, transparent 87%, #445 87.5%, #445),
                    linear-gradient(150deg, #445 12%, transparent 12.5%, transparent 87%, #445 87.5%, #445),
                    linear-gradient(30deg, #445 12%, transparent 12.5%, transparent 87%, #445 87.5%, #445),
                    linear-gradient(150deg, #445 12%, transparent 12.5%, transparent 87%, #445 87.5%, #445),
                    linear-gradient(60deg, #99a 25%, transparent 25.5%, transparent 75%, #99a 75%, #99a),
                    linear-gradient(60deg, #99a 25%, transparent 25.5%, transparent 75%, #99a 75%, #99a);
                background-size: 80px 140px;
                background-position: 0 0, 0 0, 40px 70px, 40px 70px, 0 0, 40px 70px;
                margin: 0;
                padding: 0;
            }

            .ask-q {
                padding: 5px;
                text-align: center;
                background-color: orange;
                cursor: pointer;
                color: white;
                text-decoration: none;
                position: relative;
                border-radius: 5px;
            }

            .ask-q:hover {
                background-color: #0088cc;
                transition: 0.2s linear;
            }

            .pagination {
                margin-top: 20px;
                text-align: center;
            }

            .pagination a {
                display: inline-block;
                border-radius: 5px;
                box-shadow: 0px 0px 15px rgb(236, 129, 7);
                padding: 10px 15px;
                margin: 0 5px;
                background-color: #11ecaa;
                text-decoration: none;
                color: #fff;
            }
            
            .pagination a.active {
                background-color: #007bff;
                color: #fff;
                border: 1px solid #007bff;
            }
            
            .pagination a:hover {
                color: #333;
                background-color: #ddd;
            }


            .go-back {
                padding: 10px;
                text-align: center;
                border-radius: 5px;
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
                width: 80%;
                margin: 50px auto;
                background-color: rgba(255, 255, 255, 0.43);
                backdrop-filter: blur(10px);
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0px 0px 15px rgba(0, 0, 0, 1);
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
                border: 2px solid black;
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

            .post-item .area-tag {
                background-color: #ff00ff;
            }

            .post-item .date-tag {
                background-color: #000000;
            }

            .post-item .public-q-description {
                padding: 5px;
                background-color: #f2f2f2;
                border-radius: 5px;
                margin-bottom: 10px;
                border: 1px solid black;
            }

            .post-item .admin-ans-description {
                padding: 5px;
                background-color: #f2f2f2;
                border: 1px solid black;
                border-radius: 5px;
                margin-bottom: 10px;
            }

            .ur-q,
            .ur-ans {
                color: black;
                text-transform: capitalize;
                font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
                font-size: 20px;
            }

            .pch-desc {
                text-transform: capitalize;
                color: crimson;
            }

            .pch-admin-reply {
                text-transform: capitalize;
                color: teal
            }

            post-item .post-actions {
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

            .post-actions button {
                margin-left: 5px;
                padding: 5px 10px;
            }

            @media (max-width: 900px) {
                .container {
                    width: 90%;
                    padding: 5px;
                    background-color: 0px 0px 10px rgba(0, 0, 0, 1);
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

            /* 300 to 500 BELOW */

            @media only screen and (min-width:300px) and (max-width:500px) {
                body {
                    background-image: none;
                    background-color: #aaa;
                }

                .container {
                    width: 90%;
                    padding: 5px;
                    box-shadow: 0px 0px 20px rgba(0, 0, 0, 1);
                }

                .post-item .tag {
                    padding: 5px;
                    font-size: 12px;
                    color: #ffffff;
                    border-radius: 3px;
                    margin-right: 5px;
                    margin-top: 2px;
                }

                .post-item .tags {
                    display: flex;
                    flex-wrap: wrap;
                    margin-bottom: 10px;
                    padding: 5px;
                    margin-top: 2px;
                }

                .post-list {
                    width: 95%;
                    position: relative;
                    left: 10px;
                }

                .pagination {
                    position: relative;
                    top: -10px;
                }

                .back-button {
                    position: relative;
                    top: 20px;
                    left: -5px;
                }
            }
        </style>
    </head>

    <body>
        <div class="back-button">
            <a href="public_help_faq.php" class="go-back">⬅️ GO BACK</a>
        </div>
        <div class="container">
            <h2>Public Questions Management</h2>

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
                                    <span class="tag username-tag"><?php echo $post['pch_user_name']; ?></span>
                                    <span class="tag phone-tag"><?php echo $post['pch_user_phone']; ?></span>
                                    <span class="tag location-tag"><?php echo $post['pch_user_location']; ?></span>
                                    <span class="tag date-tag"><?php echo $post['pch_date']; ?></span>
                                </div>
                                <div class="public-q-description">
                                    <p class="ur-q">Your Question:</p>
                                    <p class="pch-desc"> <?php echo $post['pch_desc']; ?></p>
                                </div>
                                <div class="admin-ans-description">
                                    <p class="ur-ans">Admin Answer:</p>
                                    <p class="pch-admin-reply"><?php echo $post['pch_admin_reply']; ?></p>
                                </div>
                            </div>
                            <div class="post-actions">
                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this Question?')">
                                    <button type="submit" name="delete_post" value="<?php echo $post['pch_id']; ?>">Delete</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li class="post-item">No Questions Asked👍. <a href="public_question_form.php" class="ask-q">Feel Free to Clear Your Doubts by clicking here!!!</a> </li>
                <?php endif; ?>
            </ul>
        </div>
        <!-- Add these lines to display pagination links -->
        <div class="pagination">
            <?php for ($page = 1; $page <= $totalPages; $page++) : ?>
                <a href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
            <?php endfor; ?>
        </div>

    </body>

    </html>