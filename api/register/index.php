<?php
require "../../lib/UsersDB.php";
// Create a new user, if name is not taken

header("Content-Type: application/json");
header("Allow: HEAD, POST");
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    if ($_SERVER["REQUEST_METHOD"] !== "HEAD") {
        http_response_code(405); // 405 Method Not Allowed
    }
    exit;
}

function raise(string $error) {
    echo json_encode(array("error" => $error));
    exit(400); // 400 Bad Request
}

if (!isset($_REQUEST["username"])) {
    raise("No username set!");
}
$username = strval($_REQUEST["username"]);
if (strlen($username) < 3) {
    raise("Username is too short!");
}
if (strlen($username) > 32) {
    raise("Username is too long!");
}
if (!preg_match("/^[a-zA-Z\d_]+$/", $username)) {
    raise("Username can only be comprised of letters, numbers and underscores");
}

if (!isset($_REQUEST["password"])) {
    raise("No password set!");
}
$password = strval($_REQUEST["password"]);
if (strlen($password) < 6) {
    raise("Password is too short!");
}

$db = new UsersDB();
$user = $db->add_user($username, $password);
$db->close();
if ($user === false) {
    http_response_code(409); // 409 Conflict
    echo json_encode(array("error" => "Username is taken"));
    exit;
}
session_start();
$_SESSION["lemb_uid"] = $user->id;

http_response_code(200); // 200 OK
echo json_encode(array("id" => $user->id, "name" => $user->name));
