<?php
// ======================================================
// API CHATBOT TƯ VẤN SẢN PHẨM - ATHLETEHUB
// Chỉ đưa cho AI dữ liệu công khai cần thiết cho khách mua hàng.
// ======================================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$groq_api_key = $_ENV['GROQ_API_KEY'] ?? '';

if (empty($groq_api_key)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu GROQ_API_KEY trong file .env'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$userMessage = trim($_POST['message'] ?? '');
$product_context = $_POST['product_context'] ?? '';

if ($userMessage === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng nhập nội dung cần tư vấn.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function moneyFormat($amount)
{
    return number_format((float) $amount, 0, ',', '.') . 'đ';
}

function textLimit($text, $limit = 90)
{
    $text = trim(strip_tags((string) $text));

    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }

    return mb_substr($text, 0, $limit, 'UTF-8') . '...';
}

function rowsToPrompt(array $rows, callable $formatter)
{
    $lines = [];

    foreach ($rows as $row) {
        $line = trim($formatter($row));
        if ($line !== '') {
            $lines[] = $line;
        }
    }

    return implode("\n", $lines);
}

function containsAny($text, array $keywords)
{
    $text = mb_strtolower($text, 'UTF-8');

    foreach ($keywords as $keyword) {
        if (mb_strpos($text, mb_strtolower($keyword, 'UTF-8'), 0, 'UTF-8') !== false) {
            return true;
        }
    }

    return false;
}

function outputJson(array $payload)
{
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

$messageLower = mb_strtolower($userMessage, 'UTF-8');

// Các câu hỏi chính sách công khai không cần kết nối database hay gọi AI.
if (containsAny($messageLower, ['liên hệ', 'lien he', 'hotline', 'số điện thoại', 'so dien thoai', 'sdt', 'gọi shop', 'goi shop', 'gặp shop', 'gap shop'])) {
    outputJson([
        'status' => 'success',
        'message' => "📞 Bạn có thể liên hệ trực tiếp AthleteHub qua số 0764567781.\n\nShop sẵn sàng hỗ trợ tư vấn sản phẩm, đơn hàng và đổi trả nhé."
    ]);
}

if (containsAny($messageLower, ['thanh toán', 'thanh toan', 'cách trả tiền', 'cach tra tien', 'cách thanh toán', 'cach thanh toan', 'chuyển khoản', 'chuyen khoan', 'cod', 'trả tiền', 'tra tien'])) {
    outputJson([
        'status' => 'success',
        'message' => "💳 Cách thanh toán trên website:\n\n1. Chọn sản phẩm, size/màu và thêm vào giỏ hàng.\n2. Vào giỏ hàng, bấm Thanh toán ngay.\n3. Điền họ tên, số điện thoại và địa chỉ giao hàng.\n4. Chọn COD hoặc Chuyển khoản ngân hàng.\n5. Nếu chuyển khoản, quét QR trên trang thanh toán và ghi nội dung: Tên + Số điện thoại."
    ]);
}

if (containsAny($messageLower, ['giao hàng', 'giao hang', 'ship', 'vận chuyển', 'van chuyen', 'bao lâu nhận', 'bao lau nhan'])) {
    outputJson([
        'status' => 'success',
        'message' => "🚚 AthleteHub xác nhận đơn trong 24 giờ.\n\nNội thành thường 1-2 ngày, tỉnh/thành khác 3-5 ngày, vùng xa 5-7 ngày.\n\nĐơn từ 500.000đ được miễn phí vận chuyển; phí ship tiêu chuẩn khoảng 20.000đ - 40.000đ tùy khu vực."
    ]);
}

if (containsAny($messageLower, ['đổi trả', 'doi tra', 'hoàn trả', 'hoan tra', 'trả hàng', 'tra hang', 'đổi size', 'doi size', 'hoàn tiền', 'hoan tien'])) {
    outputJson([
        'status' => 'success',
        'message' => "🔄 Shop hỗ trợ đổi/trả trong 7 ngày nếu sản phẩm lỗi, giao sai, chưa sử dụng và còn tem mác.\n\nBạn cần cung cấp mã đơn hàng và hình ảnh sản phẩm.\n\nNếu lỗi từ shop, AthleteHub hỗ trợ miễn phí đổi trả."
    ]);
}

try {
    $database = new Database();
    $pdo = $database->connect();

    if (!$pdo) {
        throw new Exception('Không kết nối được database');
    }

    if (containsAny($messageLower, ['giảm giá nhiều nhất', 'giam gia nhieu nhat', 'sale mạnh nhất', 'sale manh nhat', 'đang giảm nhiều nhất', 'dang giam nhieu nhat', 'khuyến mãi nhiều nhất', 'khuyen mai nhieu nhat'])) {
        $stmtTopDiscount = $pdo->prepare("
            SELECT id, ten, gia, gia_goc, phan_tram_giam, hinh_anh_chinh, mo_ta
            FROM san_pham
            WHERE trang_thai = 1
            ORDER BY phan_tram_giam DESC, gia ASC
            LIMIT 3
        ");
        $stmtTopDiscount->execute();
        $products = $stmtTopDiscount->fetchAll(PDO::FETCH_ASSOC);

        outputJson([
            'status' => 'success',
            'message' => '🔥 Đây là một số sản phẩm đang có mức giảm nổi bật nhất:',
            'products' => $products,
            'products_label' => 'Giảm giá nổi bật',
        ]);
    }

    // ======================================================
    // SẢN PHẨM ĐỒNG GIÁ / TẦM GIÁ
    // ======================================================
    if (containsAny($messageLower, ['đồng giá', 'dong gia', 'cùng giá', 'cung gia', 'giá bằng', 'gia bang', 'cùng mức giá', 'cung muc gia', 'tầm giá', 'tam gia', 'khoảng giá', 'khoang gia'])) {
        preg_match('/(\d[\d\.\,]*)(\s*k|nghìn|ngàn|đồng|đ)?/iu', $userMessage, $matches);

        if (!empty($matches[1])) {
            $soTien = str_replace(['.', ','], '', $matches[1]);

            if (!empty($matches[2]) && preg_match('/k|nghìn|ngàn/iu', $matches[2])) {
                $soTien *= 1000;
            }

            $soTien = (int) $soTien;

            if ($soTien > 0) {
                $stmtPrice = $pdo->prepare("
                    SELECT id, ten, gia, gia_goc, phan_tram_giam, hinh_anh_chinh, mo_ta
                    FROM san_pham
                    WHERE trang_thai = 1
                    AND gia BETWEEN ? AND ?
                    ORDER BY ABS(gia - ?), phan_tram_giam DESC
                    LIMIT 3
                ");
                $stmtPrice->execute([$soTien * 0.9, $soTien * 1.1, $soTien]);
                $samePriceSP = $stmtPrice->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($samePriceSP)) {
                    outputJson([
                        'status' => 'success',
                        'message' => '💰 Dưới đây là một số sản phẩm có giá gần ' . moneyFormat($soTien) . ':',
                        'products' => $samePriceSP,
                        'products_label' => 'Sản phẩm cùng tầm giá',
                    ]);
                }
            }
        }
    }

    // ======================================================
    // DỮ LIỆU CÔNG KHAI ĐƯA VÀO PROMPT AI
    // Không đưa bảng người dùng, đơn hàng, giỏ hàng, lịch sử điểm, yêu cầu trả hàng.
    // ======================================================
    $stmtProducts = $pdo->prepare("
        SELECT
            sp.id,
            sp.ten,
            sp.mo_ta,
            sp.gia,
            sp.gia_goc,
            sp.phan_tram_giam,
            sp.trung_binh_sao,
            dm.ten_danh_muc,
            GROUP_CONCAT(DISTINCT kt.ten ORDER BY kt.id SEPARATOR ', ') AS sizes,
            GROUP_CONCAT(DISTINCT ms.ten ORDER BY ms.id SEPARATOR ', ') AS colors
        FROM san_pham sp
        LEFT JOIN danh_muc dm ON dm.id = sp.danh_muc_id
        LEFT JOIN bien_the_san_pham btsp ON btsp.san_pham_id = sp.id AND btsp.trang_thai = 1 AND btsp.so_luong_ton > 0
        LEFT JOIN kich_thuoc kt ON kt.id = btsp.kich_thuoc_id
        LEFT JOIN mau_sac ms ON ms.id = btsp.mau_sac_id
        WHERE sp.trang_thai = 1
        GROUP BY sp.id, sp.ten, sp.mo_ta, sp.gia, sp.gia_goc, sp.phan_tram_giam, sp.trung_binh_sao, dm.ten_danh_muc
        ORDER BY sp.la_noi_bat DESC, sp.phan_tram_giam DESC, sp.trung_binh_sao DESC
        LIMIT 14
    ");
    $stmtProducts->execute();
    $products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

    $productPrompt = rowsToPrompt($products, function ($row) {
        $sizes = $row['sizes'] ?: 'chưa cập nhật';
        $colors = $row['colors'] ?: 'chưa cập nhật';

        return "- {$row['ten']} | {$row['ten_danh_muc']} | " . moneyFormat($row['gia']) .
            " | giảm {$row['phan_tram_giam']}% | size: {$sizes} | màu: {$colors} | " .
            textLimit($row['mo_ta']);
    });

    $stmtCategories = $pdo->prepare("
        SELECT ten_danh_muc
        FROM danh_muc
        WHERE trang_thai = 1
        ORDER BY thu_tu ASC
    ");
    $stmtCategories->execute();
    $categoryNames = array_column($stmtCategories->fetchAll(PDO::FETCH_ASSOC), 'ten_danh_muc');
    $categoryPrompt = implode(', ', $categoryNames);

    $stmtCoupons = $pdo->prepare("
        SELECT ma_code, mo_ta, phan_tram_giam, so_tien_giam, don_hang_toi_thieu
        FROM ma_giam_gia
        WHERE trang_thai = 1
        AND NOW() BETWEEN ngay_bat_dau AND ngay_ket_thuc
        AND (gioi_han_su_dung IS NULL OR gioi_han_su_dung = 0 OR da_su_dung < gioi_han_su_dung)
        ORDER BY ngay_ket_thuc ASC
        LIMIT 5
    ");
    $stmtCoupons->execute();
    $coupons = $stmtCoupons->fetchAll(PDO::FETCH_ASSOC);

    $couponPrompt = rowsToPrompt($coupons, function ($row) {
        $discount = $row['phan_tram_giam']
            ? "giảm {$row['phan_tram_giam']}%"
            : "giảm " . moneyFormat($row['so_tien_giam']);

        return "- {$row['ma_code']}: {$discount}, đơn tối thiểu " . moneyFormat($row['don_hang_toi_thieu']);
    });

    $stmtFlashSales = $pdo->prepare("
        SELECT fs.ten_chuong_trinh, sp.ten AS ten_san_pham, fsp.gia_giam_gia
        FROM flash_sale fs
        INNER JOIN flash_sale_products fsp ON fsp.flash_sale_id = fs.id AND fsp.trang_thai = 1
        INNER JOIN san_pham sp ON sp.id = fsp.san_pham_id AND sp.trang_thai = 1
        INNER JOIN (
            SELECT san_pham_id, SUM(so_luong_ton) AS tong_ton_kho
            FROM bien_the_san_pham
            WHERE trang_thai = 1
            GROUP BY san_pham_id
        ) stock ON stock.san_pham_id = sp.id AND stock.tong_ton_kho > 0
        WHERE fs.trang_thai = 1
        AND fs.ngay_bat_dau <= NOW()
        AND fs.ngay_ket_thuc >= NOW()
        AND (fsp.so_luong_gioi_han IS NULL OR fsp.so_luong_gioi_han = 0 OR fsp.so_luong_da_ban < fsp.so_luong_gioi_han)
        ORDER BY fsp.gia_giam_gia ASC
        LIMIT 5
    ");
    $stmtFlashSales->execute();
    $flashSales = $stmtFlashSales->fetchAll(PDO::FETCH_ASSOC);

    $flashSalePrompt = rowsToPrompt($flashSales, function ($row) {
        return "- {$row['ten_chuong_trinh']}: {$row['ten_san_pham']} còn " . moneyFormat($row['gia_giam_gia']);
    });

    $pdo = null;
} catch (Throwable $e) {
    outputJson([
        'status' => 'error',
        'message' => 'Hiện chatbot chưa lấy được dữ liệu sản phẩm. Bạn vui lòng thử lại sau hoặc liên hệ 0764567781.'
    ]);
}

// ======================================================
// CONTEXT SẢN PHẨM ĐANG XEM
// ======================================================
$contextPrompt = '';

if (!empty($product_context)) {
    $p = json_decode($product_context, true);

    if ($p && isset($p['ten'])) {
        $sizeStr = !empty($p['sizes']) ? implode(', ', $p['sizes']) : 'chưa cập nhật';
        $colorStr = !empty($p['colors']) ? implode(', ', $p['colors']) : 'chưa cập nhật';

        $contextPrompt = "
Sản phẩm khách đang xem:
- Tên: {$p['ten']}
- Giá: " . moneyFormat($p['gia'] ?? 0) . "
- Giảm: " . ($p['phan_tram_giam'] ?? 0) . "%
- Size: {$sizeStr}
- Màu: {$colorStr}";
    }
}

$systemPrompt = "
Bạn là trợ lý bán hàng của AthleteHub.

Dữ liệu công khai được phép dùng:
- Danh mục: {$categoryPrompt}
- Sản phẩm nổi bật/đang khuyến mãi:
{$productPrompt}
- Mã giảm giá đang dùng:
" . ($couponPrompt ?: '- Chưa có mã giảm giá phù hợp.') . "
- Flash sale đang diễn ra:
" . ($flashSalePrompt ?: '- Chưa có flash sale đang diễn ra.') . "
{$contextPrompt}

Quy tắc bảo mật:
- Chỉ trả lời thông tin bán hàng công khai ở trên.
- Không nhắc tới bảng dữ liệu, database, người dùng, đơn hàng, giỏ hàng hay thông tin nội bộ.
- Không bịa size, màu, mã giảm giá, số tài khoản hoặc sản phẩm.

Quy tắc trả lời:
- Trả lời tiếng Việt, thân thiện, tối đa 5-6 câu.
- Ưu tiên 2-3 câu ngắn nếu đủ ý.
- Nếu tư vấn sản phẩm, luôn nói giá và khuyến mãi.
- Nếu không chắc, mời khách liên hệ hotline 0764567781.
- Không dùng ## hoặc ###.
";

$data = [
    'model' => 'llama-3.1-8b-instant',
    'messages' => [
        [
            'role' => 'system',
            'content' => $systemPrompt
        ],
        [
            'role' => 'user',
            'content' => $userMessage
        ]
    ],
    'temperature' => 0.4,
    'max_tokens' => 260
];

$ch = curl_init('https://api.groq.com/openai/v1/chat/completions');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $groq_api_key
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    outputJson([
        'status' => 'error',
        'message' => 'Hiện chatbot chưa kết nối được AI. Bạn vui lòng thử lại sau hoặc gọi 0764567781 để được hỗ trợ.'
    ]);
}

$result = json_decode($response, true);

if (isset($result['choices'][0]['message']['content'])) {
    outputJson([
        'status' => 'success',
        'message' => trim($result['choices'][0]['message']['content'])
    ]);
}

$groqMessage = $result['error']['message'] ?? '';

if (stripos($groqMessage, 'rate limit') !== false || stripos($groqMessage, 'tokens per minute') !== false) {
    outputJson([
        'status' => 'error',
        'message' => 'Chatbot đang quá tải trong giây lát. Bạn thử lại sau một chút hoặc liên hệ 0764567781 để được hỗ trợ nhanh nhé.'
    ]);
}

outputJson([
    'status' => 'error',
    'message' => 'Chatbot chưa phản hồi được lúc này. Bạn vui lòng thử lại sau hoặc liên hệ 0764567781.'
]);
