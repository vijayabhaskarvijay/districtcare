<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FAQ-UrbanLink</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/cerulean/bootstrap.min.css'>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <style>
        .go-back {
            padding: 10px;
            text-align: center;
            background-color: orange;
            cursor: pointer;
            color: white;
            text-decoration: none;
            margin: 50px;
            position: relative;
            top: -20px;
            left: -38%;
            border-radius: 5px;
        }

        .go-back-2 {
            padding: 10px;
            text-align: center;
            background-color: orange;
            cursor: pointer;
            color: white;
            text-decoration: none;
            margin: 50px;
            position: relative;
            top: -20px;
            /* left: -53%; */
            border-radius: 5px;
        }

        .go-back:hover {
            background-color: #cc007a;
            transition: 0.2s linear;
            color: white;
            text-decoration: none;
        }

        .go-back-2:hover {
            background-color: #cc007a;
            transition: 0.2s linear;
            color: white;
            text-decoration: none;
        }

        .back-button {
            position: relative;
            top: 30px;
            margin-left: 10px;
        }

        .panel-title>a:before {
            float: right !important;
            font-family: FontAwesome;
            content: "\f068";
            padding-right: 5px;
        }

        body {
            background-image: url("../images/brushed-alum-dark.png"), linear-gradient(to right top, #004f8c, #e57373);
            margin: 0;
            padding: 0;
            overflow-y: scroll;
            height: 100vh;
        }

        .panel-title>a.collapsed:before {
            float: right !important;
            content: "\f067";
        }

        .panel-title>a:hover,
        .panel-title>a:active,
        .panel-title>a:focus {
            text-decoration: none;
            color: red;
        }

        .panel-heading {
            padding: 20px 15px;
            border-bottom: 1px solid transparent;
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
        }

        .panel {
            margin-bottom: 20px !important;
            background-color: #ffffff;
            border: 1px solid transparent;
            -webkit-box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
            box-shadow: 15px 16px 13px 8px rgb(4 4 4 / 5%);
        }

        .jumbotron {
            /* background-color: #00bcd4; */
            padding-top: 30px;
            padding-bottom: 30px;
            margin-bottom: 30px;
            color: inherit;
            text-align: center;
            color: #fff;
            background-image: linear-gradient(90deg, #4A90E2, #8E44AD, #8E44AD, #4A90E2);
            animation: gradientAnimation 4s linear infinite;
            background-size: 200% 100%;
        }

        .jumbotron-2 {
            /* background-color: #00bcd4; */
            padding-top: 30px;
            padding-bottom: 30px;
            color: inherit;
            text-align: center;
            color: #fff;
            background-image: linear-gradient(90deg, #4A90E2, #8E44AD, #8E44AD, #4A90E2);
            animation: gradientAnimation 4s linear infinite;
            background-size: 200% 100%;
        }

        h1 {
            color: white;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 100% 0;
            }

            100% {
                background-position: -100% 0;
            }
        }
    </style>
</head>

<body>
    <!-- partial:index.partial.html -->
    <div class="jumbotron jumbotron-fluid">
        <div class="back-button">
            <a href="public_user_landing.php" class="go-back">⬅️ GO BACK</a>
        </div>
        <div class="container">
            <h1 class="display-4">FAQ</h1>
            <p class="lead">"UrbanLink FAQ: Answers to Your Questions about Connecting Communities"</p>
            <div class="back-button">
                <a href="public_manage_pch.php" class="go-back-2">Your Curiosities Answered</a>
            </div>
        </div>
    </div>

    <br>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    How do I register on UrbanLink?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                Visit our Landing Page Where You can Register for your account
                            </div>
                        </div>
                    </div>
                    <!-- QUE 2 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    What can I do after logging in?
                                </a>
                            </h4>

                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="panel-body">After logging in, you will be directed to the Public User Landing Page. Here, you can access and interact with various posts including public, government, NGO, and admin posts. You can also manage your profile, create public posts, report problems, and more.
                            </div>
                        </div>
                    </div>
                    <!-- QUE 3 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    How can I create a public post?
                                </a>
                            </h4>

                        </div>
                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body">
                                To create a public post, go to the "Create Post" section in the Public User Landing Page. Provide the necessary details such as title, description, and relevant details. Click on the "Submit" button to publish your post </div>
                        </div>
                    </div>
                    <!-- QUE 4 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingFour">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    How do I report a problem in my area?
                                </a>
                            </h4>

                        </div>
                        <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                            <div class="panel-body">Navigate to the "Problem Reporting" page from the Public User Landing Page. Here, you can specify the location on a map, select the relevant department, choose the problem type, and provide a detailed description. Submit the report to notify the concerned authorities. </div>
                        </div>
                    </div>
                    <!-- QUE 5 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingFive">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    How can I track the status of a reported problem?
                                </a>
                            </h4>

                        </div>
                        <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
                            <div class="panel-body">Go to the "Track Problems" section in the Public User Landing Page. Here, you will find a list of the problems you've reported along with their current status and updates from government or NGO agencies.</div>
                        </div>
                    </div>
                    <!-- QUE 6 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingSix">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    What can I do in the Government User section?
                                </a>
                            </h4>

                        </div>
                        <div id="collapseSix" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSix">
                            <div class="panel-body">Government users can log in with their credentials and access a specialized dashboard. They can view and manage problems specific to their location and department. They can also update the status of reported problems and allocate funds for NGO projects.An Public User will not be able to Login as an Govn Staff or any other.</div>
                        </div>
                    </div>
                    <!-- QUE 7 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingSeven">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    How can NGOs use UrbanLink?
                                </a>
                            </h4>

                        </div>
                        <div id="collapseSeven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSeven">
                            <div class="panel-body">NGOs can log in with their credentials to access their dashboard. They can view problems related to their location, update the status of ongoing projects, and request funds for specific issues. NGOs can also track feedback from users regarding their work. </div>
                        </div>
                    </div>
                    <!-- QUE 8 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingEight">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                    What is the role of an Admin on UrbanLink?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseEight" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingEight">
                            <div class="panel-body">Admins have the highest level of access on UrbanLink. They oversee all activities, including user management, content moderation, and system maintenance. They ensure the platform's integrity and resolve any issues that may arise. </div>
                        </div>
                    </div>
                    <!-- QUE 9 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingNine">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                    How is inappropriate content handled on UrbanLink?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseNine" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNine">
                            <div class="panel-body">If a user reports a post as inappropriate, it is reviewed by the admin. If the content violates community guidelines, the admin may take action, which can include removing the post and, in severe cases, blocking the user account.</div>
                        </div>
                    </div>
                    <!-- QUE 10 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTen">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                    What happens if I forget my password?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTen" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTen">
                            <div class="panel-body">If you forget your password, click on the "Forgot Password" link on the login page. You will be redirected to <mark>MPIN</mark> entering page along with user details after proper authentication you can reset your password. </div>
                        </div>
                    </div>
                    <!-- QUE 11 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingEleven">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                                    Can I change my registered location after registration?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseEleven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingEleven">
                            <div class="panel-body">"No, once registered, you cannot change your location. This ensures that your account is associated with your actual residential area. For security purposes, even if you change your residence, you are instructed to use the same old location of [Gobichettipalayam or Sathyamangalam] to log in to your account."</div>
                        </div>
                    </div>
                    <!-- QUE 12 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwelve">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
                                    Is my personal information secure on UrbanLink?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwelve" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwelve">
                            <div class="panel-body">Yes, UrbanLink prioritizes user privacy and employs robust security measures to protect personal information. We do not share or disclose user data without consent. </div>
                        </div>
                    </div>
                    <!-- QUE 13 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThirteen">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThirteen" aria-expanded="false" aria-controls="collapseThirteen">
                                    Is my personal information secure on UrbanLink?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseThirteen" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThirteen">
                            <div class="panel-body">If you forget your password, click on the "Forgot Password" link on the login page. You will be redirected to <mark>MPIN</mark> entering page along with user details after proper authentication you can reset your password. </div>
                        </div>
                    </div>
                    <!-- QUE 14 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingFourteen">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFourteen" aria-expanded="false" aria-controls="collapseFourteen">
                                    Can I edit or delete a post I've created?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseFourteen" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFourteen">
                            <div class="panel-body">Yes, you can edit or delete a post you've created. Go to the "Manage Posts" section in the Public User Landing Page to find your posts and use the respective options. </div>
                        </div>
                    </div>
                    <!-- QUE 15 -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingFifteen">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFifteen" aria-expanded="false" aria-controls="collapseFifteen">
                                    Is my feedback visible to other users?
                                </a>
                            </h4>
                        </div>
                        <div id="collapseFifteen" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFifteen">
                            <div class="panel-body">No need to worry about your valuable feedback those are secured in a proper way and only respective <mark>NGO or Government Staff</mark> will be able to review your Feedback👍.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="jumbotron-2 jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">Still More Question🤔?</h1>
            <p class="lead">"UrbanLink FAQ: Answers to Your Questions about Connecting Communities <mark><a href="public_question_form.php">ASK HERE 🚩</a></mark>"</p>
        </div>
    </div>
    <!-- partial -->
    <script src='https://code.jquery.com/jquery-1.11.0.min.js'></script>
    <script src='https://netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js'></script>
</body>

</html>