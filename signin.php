<!DOCTYPE html>
<html lang="zxx" dir="ltr">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8">
    <meta name="description" content="Sing In ATB">
    <meta name="keywords" content="Highest Return on Your Investment">
    <meta name="author" content="Algo Trading Bot (ATB)">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f2f3f5" />
    <!-- Site Properties -->
    <title>Sign in</title>
    <!-- Critical preload -->
    <link rel="preload" href="js/vendors/uikit.min.js" as="script">
    <link rel="preload" href="css/vendors/uikit.min.css" as="style">
    <link rel="preload" href="css/style.css" as="style">
    <!-- Icon preload -->
    <link rel="preload" href="fonts/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="fonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
    <!-- Font preload -->
    <link rel="preload" href="fonts/inter-v2-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="fonts/inter-v2-latin-500.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="fonts/inter-v2-latin-700.woff2" as="font" type="font/woff2" crossorigin>
    <!-- Favicon and apple icon -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon-precomposed" href="apple-touch-icon.png">
    <!-- CSS -->
    <link rel="stylesheet" href="css/vendors/uikit.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- preloader begin -->
    <div class="in-loader">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <!-- preloader end -->
    <main>
        <!-- section content begin -->
        <div class="uk-section uk-padding-remove-vertical">
            <div class="uk-container uk-container-expand">
                <div class="uk-grid" data-uk-height-viewport="expand: true">
                    <div class="uk-width-3-5@m uk-background-cover uk-background-center-right uk-visible@m uk-box-shadow-xlarge" style="background-image: url(img/in-signin-image.jpg);"></div>
                    <div class="uk-width-expand@m uk-flex uk-flex-middle">
                        <div class="uk-grid uk-flex-center">
                            <div class="uk-width-3-5@m">
                                <div class="in-padding-horizontal@s">
                                    <!-- logo begin -->
                                    <a class="uk-logo" href="index-2.html">
                                        <img src="img/in-lazy.gif" data-src="img/in-logo-2.svg" alt="logo" width="160" height="34" data-uk-img>
                                    </a>
                                    <!-- logo end -->
                                    <p class="uk-text-lead uk-margin-top uk-margin-remove-bottom">Log into your account</p>
                                    <p class="uk-text-small uk-margin-remove-top uk-margin-medium-bottom">Don't have an account? <a href="#">Register here</a></p>
                                    <!-- login form begin -->
                                    <form class="uk-grid uk-form">
                                        <div class="uk-margin-small uk-width-1-1 uk-inline">
                                            <span class="uk-form-icon uk-form-icon-flip fas fa-user fa-sm"></span>
                                            <input class="uk-input uk-border-rounded" id="username" value="" type="text" placeholder="Username">
                                        </div>
                                        <div class="uk-margin-small uk-width-1-1 uk-inline">
                                            <span class="uk-form-icon uk-form-icon-flip fas fa-lock fa-sm"></span>
                                            <input class="uk-input uk-border-rounded" id="password" value="" type="password" placeholder="Password">
                                        </div>
                                        <div class="uk-margin-small uk-width-auto uk-text-small">
                                            <label><input class="uk-checkbox uk-border-rounded" type="checkbox"> Remember me</label>
                                        </div>
                                        <div class="uk-margin-small uk-width-expand uk-text-small">
                                            <label class="uk-align-right"><a class="uk-link-reset" href="#">Forgot password?</a></label>
                                        </div>

                                        <?php
                                            require 'db_connection.php';

                                            if(isset($_SESSION['login_id'])){
                                                header('Location: home.php');
                                                exit;
                                            }

                                            require 'google-api/vendor/autoload.php';

                                            // Creating new google client instance
                                            $client = new Google_Client();

                                            // Enter your Client ID
                                            $client->setClientId('18300631632-28c3cij3sma0l2ed8r4qlsk901kts475.apps.googleusercontent.com');
                                            // Enter your Client Secrect
                                            $client->setClientSecret('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');
                                            // Enter the Redirect URL
                                            $client->setRedirectUri('http://localhost:84/google_login/login.php');

                                            // Adding those scopes which we want to get (email & profile Information)
                                            $client->addScope("email");
                                            $client->addScope("profile");


                                            if(isset($_GET['code'])):

                                                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

                                                if(!isset($token["error"])){

                                                    $client->setAccessToken($token['access_token']);

                                                    // getting profile information
                                                    $google_oauth = new Google_Service_Oauth2($client);
                                                    $google_account_info = $google_oauth->userinfo->get();
                                                
                                                    // Storing data into database
                                                    $id = mysqli_real_escape_string($db_connection, $google_account_info->id);
                                                    $full_name = mysqli_real_escape_string($db_connection, trim($google_account_info->name));
                                                    $email = mysqli_real_escape_string($db_connection, $google_account_info->email);
                                                    $profile_pic = mysqli_real_escape_string($db_connection, $google_account_info->picture);

                                                    // checking user already exists or not
                                                    $get_user = mysqli_query($db_connection, "SELECT `google_id` FROM `users` WHERE `google_id`='$id'");
                                                    if(mysqli_num_rows($get_user) > 0){

                                                        $_SESSION['login_id'] = $id; 
                                                        header('Location: home.php');
                                                        exit;

                                                    }
                                                    else{

                                                        // if user not exists we will insert the user
                                                        $insert = mysqli_query($db_connection, "INSERT INTO `users`(`google_id`,`name`,`email`,`profile_image`) VALUES('$id','$full_name','$email','$profile_pic')");

                                                        if($insert){
                                                            $_SESSION['login_id'] = $id; 
                                                            header('Location: home.php');
                                                            exit;
                                                        }
                                                        else{
                                                            echo "Sign up failed!(Something went wrong).";
                                                        }

                                                    }

                                                }
                                                else{
                                                    header('Location: login.php');
                                                    exit;
                                                }
                                                
                                            else: 
                                                // Google Login Url = $client->createAuthUrl(); 
                                            ?>

                                        <div class="uk-margin-small uk-width-1-1">
                                            <a href="<?php echo $client->createAuthUrl(); ?>" class="uk-button uk-width-1-1 uk-button-primary uk-border-rounded uk-float-left" type="submit" name="submit">Sign in</a>
                                            <?php endif; ?>
                                        </div>


                                    </form>
                                    <!-- login form end -->
                                    <p class="uk-heading-line uk-text-center"><span>Or sign in with</span></p>
                                    <div class="uk-margin-medium-bottom uk-text-center">
                                        <a class="uk-button uk-button-small uk-border-rounded in-brand-google" href="#"><i class="fab fa-google uk-margin-small-right"></i>Google</a>
                                        <a class="uk-button uk-button-small uk-border-rounded in-brand-facebook" href="#"><i class="fab fa-facebook-f uk-margin-small-right"></i>Facebook</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- section content end -->
    </main>
    <!-- Javascript -->
    <script src="js/vendors/uikit.min.js"></script>
    <script src="js/vendors/blockit.min.js"></script>
    <script src="js/config-theme.js"></script>
</body>

</html>