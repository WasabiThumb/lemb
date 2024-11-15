<?php
require "../../lib/UsersDB.php";
// Reports user data corresponding to a user ID

header("Content-Type: application/json");
header("Allow: HEAD, GET");
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    if ($_SERVER["REQUEST_METHOD"] !== "HEAD") {
        http_response_code(405); // 405 Method Not Allowed
    }
    exit;
}

if (!isset($_REQUEST["id"])) {
    http_response_code(400); // 400 Bad Request
    echo json_encode(array("error" => "No ID given"));
    exit;
}

$uid = intval($_REQUEST["id"]);
if ($uid < 1) {
    http_response_code(204); // 204 No Content
    echo json_encode(array("valid" => false, "id" => $uid));
    exit;
}

$db = new UsersDB();
$user = $db->get_user_by_id($uid);
$db->close();

if ($user === false) {
    session_start();
    if (isset($_SESSION["lemb_uid"]) && intval($_SESSION["lemb_uid"]) === $uid) {
        unset($_SESSION["lemb_uid"]);
    }
    http_response_code(204); // 204 No Content
    echo json_encode(array("valid" => false, "id" => 0));
    exit;
}

http_response_code(200);
echo json_encode(array("valid" => true, "id" => $user->id, "name" => $user->name));
