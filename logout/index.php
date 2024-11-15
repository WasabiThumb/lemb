<?php
session_start();
unset($_SESSION["lemb_uid"]);
http_response_code(307);
header("Location: ../");
