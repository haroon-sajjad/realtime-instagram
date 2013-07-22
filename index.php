<?php

require_once 'inc/config.inc.php';

// Catches the Instagram realtime Pubsubhubub challenge flow
if (isset($_GET['hub_challenge'])) {
    echo $_GET['hub_challenge'];
    exit;
}

// Catches realtime updates from Instagram
if ($_SERVER['REQUEST_METHOD']==='POST') {

    // Instantiates Pusher PHP API
    require 'lib/Pusher.php';

    // Retrieves the POST data from Instagram
    $update = file_get_contents('php://input');
    $photos = json_decode($update);

    // If one or more photos has been posted, notify all clients
    if (is_array($photos) && ($length=count($photos))>0) {
        $pusher = new Pusher($pusher_key, $pusher_secret, $pusher_app_id);
        $pusher->trigger(
            'photos', 
            'new-photo', 
            array(
                'newcount' => $length,
            )
        );
    }

}

// Retrieves the access token from the query string
if (isset($_GET['access_token'])) {
    $token = htmlentities($_GET['access_token']);
} else {
    $token = NULL;
}

// Generates the login URL to authorize the app
$login_url = 'https://api.instagram.com/oauth/authorize/'
           . '?client_id=' . $ig_client_id
           . '&amp;redirect_uri=' . $ig_login_uri
           . '&amp;response_type=code';

?>
<!doctype html>
<html lang="en">

<head>

<meta charset="utf-8" />

<link rel="stylesheet" href="css/master.css" />

<title>Realtime Instagram Photo Demo by Jason Lengstorf</title>

</head>

<body>

<?php if ($token===NULL): ?>

    <header>
        <h1>Log in to start playing with realtime!</h1>
    </header>

    <article>
        <a href="<?=$login_url?>"
           class="login button">Login &rarr;</a>
    </article>

<?php else: ?>

    <header>
        <h1>Photos tagged with #<?=$tag?></h1>
    </header>

    <article>

        <div id="count-bar" class="message hidden">
            <p>
                <strong id="count">0 new photos posted.</strong>
                <a href="/"
                   class="button"
                   id="image-loader">&#8635; Load the new images </a>
            </p>
        </div>

        <ul id="photos">
            <li class="loading">Loading&hellip;</li>
        </ul><!--/#photos-->

    </article>

<?php endif; ?>

<footer>
    <p>
        This demo was created by 
        <a href="http://www.lengstorf.com/">Jason Lengstorf</a>. 
        The source code is available on 
        <a href="https://github.com/jlengstorf/realtime-instagram">GitHub</a>.
        All photos belong to their respective owners.
    </p>
</footer>

<script src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
<script src="http://js.pusher.com/2.0/pusher.min.js"></script>
<script>
    // For PHP-powered tag selection
    var tag = '<?=$tag?>';

    // Enables pusher logging - don't include this in production
    Pusher.log = function(message) {
        if (window.console && window.console.log) window.console.log(message);
    };

    // Flash fallback logging - don't include this in production
    WEB_SOCKET_DEBUG = true;

    // Initializes Pusher (done inline to use PHP for config variables)
    var pusher   = new Pusher('<?=$pusher_key?>'),
        channel  = pusher.subscribe('photos');

</script>
<script src="js/main.js"></script>

</body>

</html>

