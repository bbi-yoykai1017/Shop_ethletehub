<?php
// ======================================================
// API CHATBOT TƯ VẤN SẢN PHẨM - ATHLETEHUB
// Chức năng:
// 1. Nhận câu hỏi từ khách hàng
// 2. Lấy dữ liệu sản phẩm từ database
// 3. Nếu khách hỏi sản phẩm đồng giá -> xử lý trực tiếp DB
// 4. Nếu câu hỏi chung -> gửi sang Groq AI để tư vấn
// ======================================================
 ini_set('display_errors', 1);
error_reporting(E_ALL);

// Trả về dữ liệu JSON UTF-8
header('Content-Type: application/json; charset=utf-8');

// Import class Database
require_once __DIR__ . '/../Database.php';

// ======================================================
// CẤU HÌNH BAN ĐẦU
// ======================================================
// Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Load biến môi trường từ file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
// API Key Groq (NÊN chuyển sang .env để bảo mật)
$groq_api_key = $_ENV['GROQ_API_KEY'] ?? '';

// Tin nhắn khách gửi lên
$userMessage = $_POST['message'] ?? '';

// Context sản phẩm khách đang xem
$product_context = $_POST['product_context'] ?? '';


// Nếu không có tin nhắn -> dừng
if (empty($userMessage)) exit;


// ======================================================
// KẾT NỐI DATABASE
// ======================================================

$database = new Database();
$pdo = $database->connect();

// Nếu lỗi DB
if (!$pdo) {
    die(json_encode([
        'status'  => 'error',
        'message' => 'Không kết nối được database'
    ]));
}


// ======================================================
// LẤY TOÀN BỘ DANH SÁCH SẢN PHẨM ĐANG HOẠT ĐỘNG
// ======================================================

$stmt = $pdo->prepare("
    SELECT ten, mo_ta, gia, gia_goc, phan_tram_giam
    FROM san_pham
    WHERE trang_thai = 1
");

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đóng kết nối
$pdo = null;


// ======================================================
// BUILD DANH SÁCH SẢN PHẨM CHO AI
// ======================================================

$danhSachSP = "";

foreach ($rows as $row) {

    // Format giá
    $gia     = number_format($row['gia'], 0, ',', '.');
    $gia_goc = number_format($row['gia_goc'], 0, ',', '.');

    // Tạo từng dòng mô tả sản phẩm
    $danhSachSP .= "- {$row['ten']}: {$gia}đ "
        . "(giá gốc: {$gia_goc}đ, giảm {$row['phan_tram_giam']}%) "
        . "- {$row['mo_ta']}\n";
}


// ======================================================
// PHÁT HIỆN KHÁCH HỎI VỀ SẢN PHẨM ĐỒNG GIÁ
// ======================================================

$priceKeywords = [
    'đồng giá',
    'cùng giá',
    'giá bằng',
    'cùng mức giá'
];

$isAskingPrice = false;

// Kiểm tra từng keyword
foreach ($priceKeywords as $kw) {
    if (mb_strpos(mb_strtolower($userMessage), $kw) !== false) {
        $isAskingPrice = true;
        break;
    }
}


// ======================================================
// NẾU KHÁCH HỎI VỀ GIÁ CỤ THỂ
// ======================================================

if ($isAskingPrice) {

    // Regex lấy số tiền từ câu hỏi
    // Hỗ trợ:
    // 149k
    // 149.000
    // 149000
    preg_match(
        '/(\d[\d\.\,]*)(\s*k|nghìn|đồng|đ)?/i',
        $userMessage,
        $matches
    );

    // Nếu tìm thấy giá
    if (!empty($matches[1])) {

        // Xóa dấu . ,
        $soTien = str_replace(['.', ','], '', $matches[1]);

        // Nếu có k/nghìn => nhân 1000
        if (
            !empty($matches[2]) &&
            preg_match('/k|nghìn/i', $matches[2])
        ) {
            $soTien = $soTien * 1000;
        }

        $soTien = (int)$soTien;

        // Giá hợp lệ
        if ($soTien > 0) {

            // Tìm khoảng giá ±10%
            $min = $soTien * 0.9;
            $max = $soTien * 1.1;


            // ======================================================
            // QUERY SẢN PHẨM TƯƠNG ĐƯƠNG
            // ======================================================

            $database2 = new Database();
            $pdo2 = $database2->connect();

            $stmtPrice = $pdo2->prepare("
                SELECT 
                    id,
                    ten,
                    gia,
                    gia_goc,
                    phan_tram_giam,
                    hinh_anh_chinh,
                    mo_ta
                FROM san_pham
                WHERE trang_thai = 1
                AND gia BETWEEN ? AND ?
                ORDER BY RAND()
                LIMIT 3
            ");

            $stmtPrice->execute([$min, $max]);

            $samePriceSP = $stmtPrice->fetchAll(PDO::FETCH_ASSOC);

            $pdo2 = null;


            // ======================================================
            // NẾU CÓ SẢN PHẨM -> TRẢ VỀ LUÔN
            // ======================================================

            if (!empty($samePriceSP)) {

                $spList = array_map(function ($sp) {
                    return [
                        'id'             => $sp['id'],
                        'ten'            => $sp['ten'],
                        'gia'            => $sp['gia'],
                        'gia_goc'        => $sp['gia_goc'],
                        'phan_tram_giam' => $sp['phan_tram_giam'],
                        'hinh_anh_chinh' => $sp['hinh_anh_chinh'],
                        'mo_ta'          => $sp['mo_ta'],
                    ];
                }, $samePriceSP);

                echo json_encode([
                    'status'   => 'success',
                    'message'  =>
                        '💰 Dưới đây là một số sản phẩm có giá tương đương '
                        . number_format($soTien, 0, ',', '.')
                        . 'đ:',
                    'products'       => $spList,
                    'products_label' => 'Sản phẩm đồng giá',
                ]);

                // Không cần AI nữa
                exit;
            }
        }
    }
}


// ======================================================
// XỬ LÝ CONTEXT SẢN PHẨM KHÁCH ĐANG XEM
// ======================================================

$contextPrompt = '';

if (!empty($product_context)) {

    $p = json_decode($product_context, true);

    if ($p && isset($p['ten'])) {

        $gia_fmt = number_format($p['gia'], 0, ',', '.');

        // Danh sách size
        $sizeStr = !empty($p['sizes'])
            ? implode(', ', $p['sizes'])
            : 'Chưa cập nhật';

        // Danh sách màu
        $colorStr = !empty($p['colors'])
            ? implode(', ', $p['colors'])
            : 'Chưa cập nhật';


        // Prompt context
        $contextPrompt = "

THÔNG TIN SẢN PHẨM KHÁCH ĐANG XEM:
Tên: {$p['ten']}
Giá: {$gia_fmt}đ (giảm {$p['phan_tram_giam']}%)
Size có sẵn: {$sizeStr}
Màu có sẵn: {$colorStr}

QUY TẮC BẮT BUỘC:
Chỉ được nói đúng các size và màu ở trên.
Tuyệt đối không được thêm bất kỳ size hoặc màu nào khác.";
    }
}


// ======================================================
// TẠO SYSTEM PROMPT CHO AI
// ======================================================

$systemPrompt = "
Bạn là trợ lý tư vấn bán hàng của shop thể thao AthleteHub.

Nhiệm vụ:
Tư vấn sản phẩm dựa trên danh sách sau:

$danhSachSP

Quy tắc:
- Chỉ tư vấn sản phẩm có trong danh sách
- Trả lời bằng tiếng Việt
- Thân thiện
- Súc tích
- Tối đa 3-4 câu mỗi ý
- Nếu không có sản phẩm -> xin lỗi + gợi ý tương tự
- Luôn nhắc giá và khuyến mãi

Định dạng:
- Dùng emoji
- Mỗi ý xuống dòng riêng
- Có khoảng trắng giữa các ý
- Nếu nhiều sản phẩm -> dùng dấu -
- Kết thúc bằng câu hỏi ngắn
- Không dùng ## hoặc ###

$contextPrompt
";


// ======================================================
// DATA GỬI GROQ API
// ======================================================

$data = [
    "model" => "llama-3.1-8b-instant",
    "messages" => [
        [
            "role"    => "system",
            "content" => $systemPrompt
        ],
        [
            "role"    => "user",
            "content" => $userMessage
        ]
    ]
];


// ======================================================
// GỌI API GROQ
// ======================================================

$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $groq_api_key
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// LƯU Ý: Nên bật SSL verify trong production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

curl_close($ch);


// ======================================================
// XỬ LÝ RESPONSE TỪ AI
// ======================================================

$result = json_decode($response, true);


// Nếu thành công
if (isset($result['choices'][0]['message']['content'])) {

    echo json_encode([
        'status'  => 'success',
        'message' => $result['choices'][0]['message']['content']
    ]);

} else {

    // Nếu lỗi
    echo json_encode([
        'status'  => 'error',
        'message' => $response
    ]);
}