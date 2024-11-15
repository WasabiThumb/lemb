<?php
require "../../lib/MessagesDB.php";
// Get the message history

header("Content-Type: application/json");
header("Allow: HEAD, GET");
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    if ($_SERVER["REQUEST_METHOD"] !== "HEAD") {
        http_response_code(405); // 405 Method Not Allowed
    }
    exit;
}

$db = new MessagesDB();
$history = $db->get_message_history();
$db->close();

http_response_code(200);
echo json_encode($history);
