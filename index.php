<!DOCTYPE html>
<html>

<head>
    <title>UrbanLink - Be Connected!</title>
    <link rel="icon" href="./images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&family=Lobster&family=Lobster+Two&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Croissant+One&display=swap" rel="stylesheet">

</head>

<body>
    <div class="slider-thumb">
        <!-- NAVBAR CONTENT STARTS -->
        <section class="navigation">
            <div class="nav-container">
                <div class="brand">
                    <h1 class="from-left-and-back">UrbanLink</h1>
                </div>
                <nav class="nav-head">
                    <div class="nav-mobile">
                        <a id="nav-toggle" href="#!"><span></span></a>
                    </div>
                    <ul class="nav-list">
                        <li><a href="#!">Login</a>
                            <ul class="nav-dropdown">
                                <li><a href="./public/main_login.php">Public User Login</a></li>
                                <li><a href="./govn/govn_login.php">Govn Staff Login</a></li>
                                <li><a href="./admin/admin_login.php">Admin Login</a></li>
                                <li><a href="./ngo/ngo_login.php">NGO Login</a></li>
                            </ul>
                        </li>
                        <li><a href="#!">Register</a>
                            <ul class="nav-dropdown">
                                <li><a href="./public/main_register.php">Public User Register</a></li>
                                <li><a href="./ngo/ngo_register.php">NGO's Register</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>
        <!-- NAVBAR CONTENT ENDS -->
        <div class="content">
            <div class="left-section">
                <div class="circle-container">
                    <div class="circle-logo-1">
                        <p class="logo-1-text">Innovation</p>
                    </div>
                </div>
                <div class="profile-picture">
                    <img src="images/urbanlink-logo.png" alt="Hero Image" class="hero-image">
                </div>
            </div>
            <div class="circle-logo-2">
                <p class="logo-2-text">Empowerment</p>
            </div>
            <div class="right-section">
                <div class="description">
                    <h2 class="typing-demo">Welcome to UrbanLink</h2>
                    <p> A web portal designed to facilitate citizen engagement and problem-solving in your district. UrbanLink allows you to report and track various issues and concerns in your area. Whether it's road damage, electrical problems, or any other issue, you can submit your problems and have the relevant government departments address them. Stay connected with your district, participate in the community, and contribute to its development.</p>
                </div>
                <div class="circle-logo-3">
                    <p class="logo-3-text">Connect</p>
                </div>
            </div>
        </div>
        <div class="circle-logo-4">
            <p class="logo-4-text">Develop</p>
        </div>
        <div class="circle-logo-5">
            <p class="logo-5-text">Collaborate</p>
        </div>
    </div>
    <section class="middle">
        <hr class="hr-1">
        <div id="video-container">
            <video src="./images/video-1.mp4" autoplay='true' loop muted class="video-video"></video>
        </div>
        <div>
            <p class="moto"> <i> # Be Connected </i> <i class="fa-solid fa-users-rays"></i></p>
        </div>
        <!-- <p class="moto"> <i> # Be Connected </i><i class="fa-brands fa-connectdevelop"></i></p> -->
    </section>
    <footer>
        <p class="footer">© <span id="currentYear"></span> UrbanLink. All rights reserved.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Automatic year update
        document.getElementById("currentYear").innerHTML = new Date().getFullYear();
    </script>

    <script>
        (function($) { // Begin jQuery
            $(function() { // DOM ready
                // If a link has a dropdown, add sub menu toggle.
                $('nav ul li a:not(:only-child)').click(function(e) {
                    $(this).siblings('.nav-dropdown').toggle();
                    // Close one dropdown when selecting another
                    $('.nav-dropdown').not($(this).siblings()).hide();
                    e.stopPropagation();
                });
                // Clicking away from dropdown will remove the dropdown class
                $('html').click(function() {
                    $('.nav-dropdown').hide();
                });
                // Toggle open and close nav styles on click
                $('#nav-toggle').click(function() {
                    $('nav ul').slideToggle();
                });
                // Hamburger to X toggle
                $('#nav-toggle').on('click', function() {
                    this.classList.toggle('active');
                });
            }); // end DOM ready
        })(jQuery); // end jQuery
    </script>

    <script>
        const text = document.querySelector('.holographic');

        text.addEventListener('mousemove', (e) => {
            gsap.to(text, {
                '--x': `${(e.offsetX/window.innerWidth)*100}%`,
                '--y': `${(e.offsetY / window.innerHeight)*100}%`,
            });
        });
    </script>

</body>

</html>