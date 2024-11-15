<?php
require "../../lib/MessagesDB.php";
// Get the message history

header("Content-Type: application/json");
header("Allow: HEAD, POST");
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    if ($_SERVER["REQUEST_METHOD"] !== "HEAD") {
        http_response_code(405); // 405 Method Not Allowed
    }
    exit;
}

session_start();
$uid = isset($_SESSION["lemb_uid"]) ? intval($_SESSION["lemb_uid"]) : 0;
if ($uid < 1) {
    http_response_code(401); // 401 Unauthorized
    exit;
}

$msg = file_get_contents('php://input');
if (strlen($msg) < 1 || strlen($msg) > 250) {
    http_response_code(400);
    exit;
}

$db = new MessagesDB();
$res = $db->add_message($uid, $msg);
$db->close();

http_response_code($res ? 200 : 500);
