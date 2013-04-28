<?php

// Turns error reporting up to eleven
error_reporting(E_ALL^E_STRICT);
ini_set('display_errors', 1);

// Catches the Instagram realtime Pubsubhubub challenge flow
if (isset($_GET['hub_challenge'])) {
    echo $_GET['hub_challenge'];
    exit;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    // Instantiates Pusher PHP API
    require 'lib/Pusher.php';

    $pusher = new Pusher('867d60a8d5de3996dd25', '7709ac1336e7968d1a61', '42771');
    $pusher->trigger(
        'selfies', 
        'new-selfie', 
        array(
            'selfie' => 'New selfie!', 
            'debug' => print_r($_POST, TRUE)
        )
    );
}

?>
<!doctype html>
<html lang="en">

<head>

<meta charset="utf-8" />

<title>Realtime Workshop by Jason Lengstorf &mdash; Future Insights Live</title>

</head>

<body>

<h1>Selfies!</h1>

<ul id="selfies">
    <li class="loading">No selfies yet&hellip; #sadface</li>
</ul><!--/#selfies-->

<footer>
    <p>
        This demo was created by 
        <a href="http://www.lengstorf.com/">Jason Lengstorf</a> for use at the 
        realtime workshop at Future Insights Live 2013. It is released under the 
        MIT License.
    </p>
</footer>

<script src="http://js.pusher.com/2.0/pusher.min.js"></script>
<script>

// Enable pusher logging - don't include this in production
Pusher.log = function(message) {
    if (window.console && window.console.log) window.console.log(message);
};

// Flash fallback logging - don't include this in production
WEB_SOCKET_DEBUG = true;

var pusher  = new Pusher('867d60a8d5de3996dd25'),
    channel = pusher.subscribe('selfies'),
    selfies = document.getElementById('selfies');

channel.bind('new-selfie', function(data){

    var selfie_count = selfies.childNodes.length;

    // If the loading LI still exists, removes it
    if (selfie_count===1) {
        for (var i=0; i<selfies.childNodes.length; i++) {
            if (selfies.childNodes[i].className==="loading") {
                selfies.removeChild(selfies.childNodes[i]);
            }
        }
    }

    // Creates a new LI with the selfie
    //TODO: Add selfie markup
    var li = document.createElement("li").appendChild(data.selfie);

    ul.appendChild(li);

    return;
});

</script>

</body>

</html>

