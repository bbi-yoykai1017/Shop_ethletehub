<?php
session_start();
require_once '../Database.php';
require_once '../model/news.php';

$db = new Database();
$conn = $db->connect();

$action = $_GET['action'] ?? '';

if ($action === 'get_latest_news') {
    $limit = $_GET['limit'] ?? 5;
    $news = getLatestNews($conn, $limit);
    
    echo json_encode([
        'success' => true,
        'data' => $news,
        'count' => count($news)
    ]);
} 
elseif ($action === 'get_news_count') {
    $total = countNews($conn, null, 1);
    
    echo json_encode([
        'success' => true,
        'count' => $total
    ]);
}
else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
?>
