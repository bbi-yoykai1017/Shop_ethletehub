<?php
/**
 * API Test - Kiểm tra xem API có hoạt động
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'status' => 'OK',
    'message' => 'API endpoint hoạt động bình thường',
    'php_version' => phpversion(),
    'session_id' => session_id(),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
