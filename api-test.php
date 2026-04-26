<?php
header('Content-Type: application/json');

//$groq_api_key = 
$userMessage = $_POST['message'] ?? '';
$product_context = $_POST['product_context'] ?? '';

if (empty($userMessage)) exit;

// Kết nối database
$conn = new mysqli('localhost', 'root', '', 'athletehub');
$conn->set_charset('utf8');

// Lấy danh sách sản phẩm đang bán
$sql = "SELECT ten, mo_ta, gia, gia_goc, phan_tram_giam FROM san_pham WHERE trang_thai = 1";
$result = $conn->query($sql);

$danhSachSP = "";
while ($row = $result->fetch_assoc()) {
    $gia     = number_format($row['gia'],     0, ',', '.');
    $gia_goc = number_format($row['gia_goc'], 0, ',', '.');
    $danhSachSP .= "- {$row['ten']}: {$gia}đ (giá gốc: {$gia_goc}đ, giảm {$row['phan_tram_giam']}%) - {$row['mo_ta']}\n";
}
$conn->close();

// Context sản phẩm đang xem (nếu có)
$contextPrompt = '';
if (!empty($product_context)) {
    $product = json_decode($product_context, true);
    if ($product && isset($product['ten'])) {
        $gia_sp = number_format($product['gia'], 0, ',', '.');
        $contextPrompt = "\n\n⚠️ Khách đang xem sản phẩm: \"{$product['ten']}\" giá {$gia_sp}đ. Hãy ưu tiên tư vấn sản phẩm này trước.";
    }
}

// System prompt
$systemPrompt = "Bạn là trợ lý tư vấn bán hàng của shop thể thao AthleteHub.
Nhiệm vụ của bạn là tư vấn sản phẩm dựa trên danh sách sau:

$danhSachSP

Quy tắc:
- Chỉ tư vấn các sản phẩm có trong danh sách trên
- Trả lời bằng tiếng Việt, thân thiện, ngắn gọn
- Nếu khách hỏi sản phẩm không có trong shop thì xin lỗi và gợi ý sản phẩm tương tự
- Luôn nhắc giá và khuyến mãi nếu có
$contextPrompt";

$data = [
    "model" => "llama-3.1-8b-instant",
    "messages" => [
        ["role" => "system", "content" => $systemPrompt],
        ["role" => "user", "content" => $userMessage]
    ]
];

//$ch = 
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $groq_api_key
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['choices'][0]['message']['content'])) {
    $botMsg = $result['choices'][0]['message']['content'];
    echo json_encode(['status' => 'success', 'message' => $botMsg]);
} else {
    echo json_encode(['status' => 'error', 'message' => $response]);
}