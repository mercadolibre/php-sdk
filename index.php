<?php
session_start();
require 'Meli/meli.php';
require 'configApp.php';

$domain = $_SERVER['HTTP_HOST'];
$appName = explode('.', $domain)[0];
?>

<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Official PHP SDK for Mercado Libre's API.">
    <meta name="keywords" content="API, PHP, Mercado Libre, SDK, meli, integration, e-commerce">
    <title>Mercado Libre PHP SDK</title>
    <link rel="stylesheet" href="/getting-started/style.css" />
    <script src="script.js"></script>
</head>

<body>
    <header class="navbar">
        <a class="logo" href="#"><img src="/getting-started/logo-developers.png" alt=""></a>
        <nav>
            <ul class="nav navbar-nav navbar-right">
                <li><a target="_blank" href="http://developers.mercadolibre.com/getting-started/">Getting Started</a></li>
                <li><a target="_blank" href="http://developers.mercadolibre.com/api-docs/">API Docs</a></li>
                <li><a target="_blank" href="http://developers.mercadolibre.com/community/">Community</a></li>
            </ul>
        </nav>
    </header>

    <div class="header">
        <div>
            <h1>Getting Started with Mercado Libre's PHP SDK</h1>
            <h2>Official PHP SDK for Mercado Libre's API.</h2>
        </div>
    </div>

    <main class="container">
        <h3>Hi, Developer!</h3>
        <p>This is a sample app, deployed to Heroku with Mercado Libre's PHP SDK. Feel free to use it as a base, to start building your awesome app!</p>

        <div class="row">
            <div class="col-md-6">
                <h3>How it works?</h3>
                <ul>
                    <li>This app was deployed to Heroku, either using Git or by using <a href="https://github.com/heroku/go-getting-started">Heroku Button</a> on the repository.</li>
                    <li>When Heroku received the source code it used the go tool chain to compile the application along with any vendored dependencies and created a deployable slug.</li>
                    <li>The platform then spins up a dyno, a lightweight container that provides an isolated environment in which the slug can be mounted and executed.</li>
                    <li>You can scale your app, manage it, and deploy over <a href="https://addons.heroku.com/">150 add-on services</a>, from the Dashboard or CLI.</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Next steps</h3>
                <p>To start, <a href="https://developers.mercadolibre.com.ar/apps/home">go to your My Apps dashboard</a> and update your application's <b>redirect URI</b> to <br />
                <code><?php echo 'https://'.$domain; ?></code>, to match the one Heroku is running.
                    <br />
                    <br /> If you deployed this app by deploying the Heroku Button, you need to clone the aplication in your computer to develop, then in a command line shell, run:
                    <br />
                    <code>heroku git:clone -a <?php echo $appName; ?></code>
                    <br />
                    (This will create a local copy of the source and associate the Heroku app with your local repository)</p>
                    <p>You can edit this lines and deploy with heroku-cli, you can use the offical Heroku's guide <a target="_blank" href="https://devcenter.heroku.com/articles/git">https://devcenter.heroku.com/articles/git</a></p>
                    <p>Remember you need to change your country in the config file called <b>configApp.php</b></p>
                    <p>You'll now be set up to run the app locally, or deploy changes to Heroku.</p>
            </div>
        </div>
        <div class="row">
            <h3>Examples</h3>
            <p>Check the following examples, they will help you to start working with our API!
            </p>
            <p>Note that these examples work for MLA(Argentina) by default. If you'd like to try them in your own country, please, <a href="https://github.com/fsolari/php-sdk/blob/master/configApp.php#L16">update this line</a> in your project, with your
                own <b>$site_id</b> before executing them.
            </p>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6 col-md-6">
                <h3>oAuth</h3>
                <p>First authenticate yourself. Authentication is the key to get the most ouf Mercado Libre's API.</p>                

                <?php
                $meli = new Meli($appId, $secretKey);

                if($_GET['code'] || $_SESSION['access_token']) {

                    // If code exist and session is empty
                    if($_GET['code'] && !($_SESSION['access_token'])) {
                        // If the code was in get parameter we authorize
                        $user = $meli->authorize($_GET['code'], $redirectURI);

                        // Now we create the sessions with the authenticated user
                        $_SESSION['access_token'] = $user['body']->access_token;
                        $_SESSION['expires_in'] = time() + $user['body']->expires_in;
                        $_SESSION['refresh_token'] = $user['body']->refresh_token;
                    } else {
                        // We can check if the access token in invalid checking the time
                        if($_SESSION['expires_in'] < time()) {
                            try {
                                // Make the refresh proccess
                                $refresh = $meli->refreshAccessToken();

                                // Now we create the sessions with the new parameters
                                $_SESSION['access_token'] = $refresh['body']->access_token;
                                $_SESSION['expires_in'] = time() + $refresh['body']->expires_in;
                                $_SESSION['refresh_token'] = $refresh['body']->refresh_token;
                            } catch (Exception $e) {
                                echo "Exception: ",  $e->getMessage(), "\n";
                            }
                        }
                    }

                    echo '<pre>';
                        print_r($_SESSION);
                    echo '</pre>';

                } else {
                    echo '<p><a alt="Login using MercadoLibre oAuth 2.0" class="btn" href="' . $meli->getAuthUrl($redirectURI, Meli::$AUTH_URL[$siteId]) . '">Authenticate</a></p>';
                }
                ?>

            </div>
            <div class="col-sm-6 col-md-6">
                <h3>Get site data</h3>
                <p>Make a simple GET to <a href="https://api.mercadolibre.com/sites">sites resource</a> with your <b>$site_id</b> to obtain information about a a site. Like id, name, currencies, categories, and other settings.</p>
                <p><a class="btn" href="../examples/example_get.php">GET</a></p>
            </div>
        </div>
        <hr>
        <div class="row">
            <h3>Get started!</h3>
            <p>Now you know how easy it is to get information from our API. Check the rest of the examples on the SDK, and modify them as you like in order to List an item, update it, and other actions.</p>
            <p><a class="btn" href="https://github.com/mercadolibre/php-sdk/tree/master/examples">More examples</a></p>
        </div>
        <hr>

        <div class="row">
            <h3>Your Credentials</h3>
            <div class="row-info col-sm-3 col-md-3">
                <b>App_Id: </b><?php echo $appId; ?>
            </div>
            <div class="row-info col-sm-3 col-md-3">
                <b>Secret_Key: </b><?php echo $secretKey; ?>
            </div>
            <div class="row-info col-sm-3 col-md-3">
                <b>Redirect_URI: </b><?php echo $redirectURI; ?>
            </div>
            <div class="row-info col-sm-3 col-md-3">
                <b>Site_Id: </b><?php echo $siteId; ?>
            </div>
        </div>
        <hr>
    </main>
</body>

</html>





