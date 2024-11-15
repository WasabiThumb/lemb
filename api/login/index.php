<?php
require "../../lib/UsersDB.php";
// Login

header("Content-Type: application/json");
header("Allow: HEAD, POST");
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    if ($_SERVER["REQUEST_METHOD"] !== "HEAD") {
        http_response_code(405); // 405 Method Not Allowed
    }
    exit;
}

if (isset($_SESSION["lemb_uid"]) && intval($_SESSION["lemb_uid"]) > 0) {
    echo "{}";
    exit(204); // 204 No Content (already logged in)
}

function raise(string $error) {
    echo json_encode(array("error" => $error));
    exit(400); // 400 Bad Request
}

if (!isset($_REQUEST["username"])) {
    raise("Missing username");
}
$username = strval($_REQUEST["username"]);

if (!isset($_REQUEST["password"])) {
    raise("Missing password");
}
$password = strval($_REQUEST["password"]);

$db = new UsersDB();
$user = $db->get_user_by_name($username);
$db->close();
$valid = false;
if ($user !== false) {
    $valid = $user->check_password($password);
}

if ($valid) {
    session_start();
    $_SESSION["lemb_uid"] = $user->id;
    echo "{}";
    exit(200);
} else {
    raise("Incorrect username or password");
}
