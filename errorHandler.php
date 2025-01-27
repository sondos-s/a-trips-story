<?php
session_start();

$response = [
    'error' => false,
    'message' => ''
];

if (isset($_SESSION["error_message"])) {
    $response['error'] = true;
    $response['message'] = $_SESSION["error_message"];
    unset($_SESSION["error_message"]);
}

echo json_encode($response);
exit();