<?php
session_start();
$loggedIn = isset($_SESSION["lemb_uid"]);
$uid = 0;
if ($loggedIn) {
    $uid = intval($_SESSION["lemb_uid"]);
    $loggedIn = $uid > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Low-Effort Message Board</title>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" href="./assets/stylesheets/main.css"/>
        <script id="me" type="text/plain"><?php echo $uid; ?></script>
        <script src="./assets/scripts/main.js" type="text/javascript"></script>
    </head>
    <body>
        <header>
            <h1>Welcome to the Low-Effort Message Board!</h1>
            <p>&quot;Uhh, I don't know, come on in I guess.&quot;</p>
        </header>
        <hr>
        <div class="auth">
            <?php
            if ($loggedIn) {
                goto identity;
            }
            ?>
            <a href="./login">Login</a>
            <a href="./register">Register</a>
            <?php
            goto noIdentity;
            identity:
            ?>
            <span id="identity">Logged in</span>
            <a href="./logout">Log Out</a>
            <?php
            noIdentity:
            ?>
        </div>
        <hr>
        <h2>Messages</h2>
        <div id="messages">
            <div class="message system">
                <h3>SYSTEM</h3>
                <p>* Start of message history *</p>
            </div>
        </div>
        <form id="send" aria-disabled="true" action="javascript:void(0)">
            <input type="text" maxlength="250" aria-label="Message" placeholder="Message" id="sendText">
            <input type="submit" value="Send">
        </form>
    </body>
</html>
