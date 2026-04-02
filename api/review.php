<?php
/**
 * API cho đánh giá/bình luận sản phẩm
 * Endpoint: /api/review.php?action=add|get|getAll
 */

ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Database.php';

ob_clean();

try {
    $db = new Database();
    $conn = $db->connect();

    if (!$conn) {
        throw new Exception('Không thể kết nối database');
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $input = file_get_contents('php://input');
    $data = json_decode($input, true) ?? [];

    // Debug output
    if ($action === 'debug') {
        echo json_encode([
            'session' => $_SESSION,
            'action' => $action,
            'data' => $data
        ]);
        exit;
    }

    switch ($action) {

        // ========== THÊM ĐÁNH GIÁ MỚI ==========
        case 'add':
            // Kiểm tra user đã đăng nhập
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để bình luận',
                    'debug' => 'Session user_id not set'
                ]);
                exit;
            }

            $userId = (int) $_SESSION['user_id'];
            $sanPhamId = (int) ($data['san_pham_id'] ?? 0);
            $soSao = (int) ($data['so_sao'] ?? 0);
            $binhLuan = trim($data['binh_luan'] ?? '');

            // Logging for debug
            error_log("Review add request - User: $userId, Product: $sanPhamId, Rating: $soSao, Content length: " . strlen($binhLuan));

            // Validate
            if ($sanPhamId <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID sản phẩm không hợp lệ',
                    'debug' => 'Invalid san_pham_id: ' . ($data['san_pham_id'] ?? 'null')
                ]);
                exit;
            }

            if ($soSao < 1 || $soSao > 5) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Số sao phải từ 1 đến 5',
                    'debug' => 'Invalid rating: ' . $soSao
                ]);
                exit;
            }

            if (empty($binhLuan)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng nhập nội dung bình luận',
                    'debug' => 'Empty content'
                ]);
                exit;
            }

            if (strlen($binhLuan) < 10) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Bình luận phải tối thiểu 10 ký tự',
                    'debug' => 'Content too short: ' . strlen($binhLuan)
                ]);
                exit;
            }

            // Kiểm tra sản phẩm có tồn tại
            $stmt = $conn->prepare("SELECT id FROM san_pham WHERE id = :id AND trang_thai = 1");
            $stmt->bindParam(':id', $sanPhamId, PDO::PARAM_INT);
            $stmt->execute();
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
                exit;
            }

            // Kiểm tra user đã bình luận sản phẩm này chưa (có thể update)
            $stmt = $conn->prepare("
                SELECT id FROM danh_gia 
                WHERE san_pham_id = :san_pham_id AND nguoi_dung_id = :user_id
            ");
            $stmt->bindParam(':san_pham_id', $sanPhamId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $existingReview = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingReview) {
                // Update bình luận cũ
                try {
                    $stmt = $conn->prepare("
                        UPDATE danh_gia 
                        SET so_sao = :so_sao, 
                            binh_luan = :binh_luan,
                            ngay_danh_gia = NOW()
                        WHERE id = :id
                    ");
                    $stmt->bindParam(':id', $existingReview['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':so_sao', $soSao, PDO::PARAM_INT);
                    $stmt->bindParam(':binh_luan', $binhLuan);
                    $stmt->execute();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Bình luận đã cập nhật thành công',
                        'id' => $existingReview['id']
                    ]);
                } catch (PDOException $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Lỗi cập nhật bình luận: ' . $e->getMessage()
                    ]);
                }
            } else {
                // Thêm bình luận mới
                try {
                    $stmt = $conn->prepare("
                        INSERT INTO danh_gia (san_pham_id, nguoi_dung_id, so_sao, binh_luan, trang_thai, ngay_danh_gia)
                        VALUES (:san_pham_id, :nguoi_dung_id, :so_sao, :binh_luan, 1, NOW())
                    ");
                    $stmt->bindParam(':san_pham_id', $sanPhamId, PDO::PARAM_INT);
                    $stmt->bindParam(':nguoi_dung_id', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':so_sao', $soSao, PDO::PARAM_INT);
                    $stmt->bindParam(':binh_luan', $binhLuan);
                    $stmt->execute();

                    $reviewId = $conn->lastInsertId();

                    error_log("Review added successfully - ID: $reviewId");

                    echo json_encode([
                        'success' => true,
                        'message' => 'Bình luận đã được thêm thành công',
                        'id' => $reviewId
                    ]);
                } catch (PDOException $e) {
                    error_log("Review insert error: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Lỗi thêm bình luận: ' . $e->getMessage()
                    ]);
                }
            }
            exit;

        // ========== LẤY DANH SÁCH BÌNH LUẬN CỦA SẢN PHẨM ==========
        case 'getAll':
            $sanPhamId = (int) ($_GET['san_pham_id'] ?? 0);
            $limit = (int) ($_GET['limit'] ?? 10);
            $offset = (int) ($_GET['offset'] ?? 0);

            if ($sanPhamId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ', 'san_pham_id' => $sanPhamId]);
                exit;
            }

            // Debug: Check if reviews exist
            $stmtCount = $conn->prepare("
                SELECT COUNT(*) as cnt FROM danh_gia 
                WHERE san_pham_id = :san_pham_id AND trang_thai = 1
            ");
            $stmtCount->bindParam(':san_pham_id', $sanPhamId, PDO::PARAM_INT);
            $stmtCount->execute();
            $countResult = $stmtCount->fetch(PDO::FETCH_ASSOC);

            // Get user ID if logged in
            $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

            try {
                $stmt = $conn->prepare("
                    SELECT 
                        dg.id,
                        dg.so_sao,
                        dg.binh_luan,
                        dg.ngay_danh_gia,
                        dg.nguoi_dung_id,
                        nd.ten as ten_nguoi_dung,
                        nd.anh_dai_dien,
                        COALESCE((SELECT COUNT(*) FROM like_danh_gia WHERE danh_gia_id = dg.id AND trang_thai = 1), 0) as so_like,
                        COALESCE((SELECT COUNT(*) FROM like_danh_gia WHERE danh_gia_id = dg.id AND nguoi_dung_id = ? AND trang_thai = 1), 0) as liked_by_user
                    FROM danh_gia dg
                    JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                    WHERE dg.san_pham_id = :san_pham_id AND dg.trang_thai = 1
                    ORDER BY dg.ngay_danh_gia DESC
                    LIMIT :limit OFFSET :offset
                ");
            } catch (PDOException $e) {
                // Fallback if like_danh_gia table doesn't exist
                $stmt = $conn->prepare("
                    SELECT 
                        dg.id,
                        dg.so_sao,
                        dg.binh_luan,
                        dg.ngay_danh_gia,
                        dg.nguoi_dung_id,
                        nd.ten as ten_nguoi_dung,
                        nd.anh_dai_dien,
                        0 as so_like,
                        0 as liked_by_user
                    FROM danh_gia dg
                    JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                    WHERE dg.san_pham_id = :san_pham_id AND dg.trang_thai = 1
                    ORDER BY dg.ngay_danh_gia DESC
                    LIMIT :limit OFFSET :offset
                ");
            }
            $stmt->bindParam(':san_pham_id', $sanPhamId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->execute();
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convert like counts to integers
            foreach ($reviews as &$review) {
                $review['so_like'] = (int) $review['so_like'];
                $review['liked_by_user'] = (int) $review['liked_by_user'] > 0;
            }

            // Đếm tổng số bình luận
            $stmt = $conn->prepare("
                SELECT COUNT(*) as total FROM danh_gia 
                WHERE san_pham_id = :san_pham_id AND trang_thai = 1
            ");
            $stmt->bindParam(':san_pham_id', $sanPhamId, PDO::PARAM_INT);
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            echo json_encode([
                'success' => true,
                'reviews' => $reviews,
                'total' => (int) $total,
                'limit' => $limit,
                'offset' => $offset,
                'debug' => [
                    'san_pham_id' => $sanPhamId,
                    'count_check' => (int) $countResult['cnt'],
                    'reviews_count' => count($reviews)
                ]
            ]);
            exit;

        // ========== XÓA BÌNH LUẬN (CHỈ USER HOẶC ADMIN) ==========
        case 'delete':
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                exit;
            }

            $userId = (int) $_SESSION['user_id'];
            $reviewId = (int) ($data['review_id'] ?? 0);
            $isAdmin = (isset($_SESSION['user_role']) && (strtolower($_SESSION['user_role']) === 'admin' || $_SESSION['user_role'] === '1'));

            if ($reviewId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID bình luận không hợp lệ']);
                exit;
            }

            // Kiểm tra quyền xóa (user hoặc admin)
            $stmt = $conn->prepare("SELECT nguoi_dung_id FROM danh_gia WHERE id = :id");
            $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
            $stmt->execute();
            $review = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$review) {
                echo json_encode(['success' => false, 'message' => 'Bình luận không tồn tại']);
                exit;
            }

            // Cho phép xóa nếu là chủ bình luận hoặc admin
            if ($review['nguoi_dung_id'] != $userId && !$isAdmin) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa bình luận này']);
                exit;
            }

            // Xóa bình luận (SET status instead of DELETE to keep data integrity)
            $stmt = $conn->prepare("UPDATE danh_gia SET trang_thai = 0 WHERE id = :id");
            $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Bình luận đã được xóa'
            ]);
            exit;

        // ========== LẤY SUMMARY ĐÁNH GIÁ ==========
        case 'summary':
            $sanPhamId = (int) ($_GET['san_pham_id'] ?? 0);

            if ($sanPhamId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
                exit;
            }

            $stmt = $conn->prepare("
                SELECT 
                    COUNT(*) as total_reviews,
                    AVG(so_sao) as average_rating,
                    SUM(CASE WHEN so_sao = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN so_sao = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN so_sao = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN so_sao = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN so_sao = 1 THEN 1 ELSE 0 END) as one_star
                FROM danh_gia
                WHERE san_pham_id = :san_pham_id AND trang_thai = 1
            ");
            $stmt->bindParam(':san_pham_id', $sanPhamId, PDO::PARAM_INT);
            $stmt->execute();
            $summary = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'summary' => $summary
            ]);
            exit;

        // ========== THÊM TRẢ LỜI BÌNH LUẬN ==========
        case 'add_reply':
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                exit;
            }

            $userId = (int) $_SESSION['user_id'];
            $danhGiaId = (int) ($data['danh_gia_id'] ?? 0);
            $noiDung = trim($data['noi_dung'] ?? '');

            if ($danhGiaId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID bình luận không hợp lệ']);
                exit;
            }

            if (strlen($noiDung) < 3) {
                echo json_encode(['success' => false, 'message' => 'Trả lời phải tối thiểu 3 ký tự']);
                exit;
            }

            // Kiểm tra bình luận tồn tại
            $stmt = $conn->prepare("SELECT id FROM danh_gia WHERE id = ? AND trang_thai = 1");
            $stmt->execute([$danhGiaId]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                echo json_encode(['success' => false, 'message' => 'Bình luận không tồn tại']);
                exit;
            }

            $stmt = $conn->prepare("
                INSERT INTO phan_hoi_danh_gia (danh_gia_id, nguoi_dung_id, noi_dung, trang_thai, ngay_tao)
                VALUES (?, ?, ?, 1, NOW())
            ");
            $stmt->execute([$danhGiaId, $userId, $noiDung]);

            echo json_encode([
                'success' => true,
                'message' => 'Trả lời đã được thêm',
                'reply_id' => $conn->lastInsertId()
            ]);
            exit;

        // ========== LẤY TRẢ LỜI BÌNH LUẬN ==========
        case 'get_replies':
            $danhGiaId = (int) ($_GET['danh_gia_id'] ?? 0);

            if ($danhGiaId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID bình luận không hợp lệ']);
                exit;
            }

            $stmt = $conn->prepare("
                SELECT 
                    ph.id,
                    ph.noi_dung,
                    ph.ngay_tao,
                    ph.nguoi_dung_id,
                    nd.ten as ten_nguoi_dung,
                    nd.anh_dai_dien,
                    (SELECT COUNT(*) FROM like_phan_hoi WHERE phan_hoi_id = ph.id AND trang_thai = 1) as so_like,
                    (SELECT COUNT(*) FROM like_phan_hoi WHERE phan_hoi_id = ph.id AND nguoi_dung_id = ? AND trang_thai = 1) as liked_by_user
                FROM phan_hoi_danh_gia ph
                JOIN nguoi_dung nd ON ph.nguoi_dung_id = nd.id
                WHERE ph.danh_gia_id = ? AND ph.trang_thai = 1
                ORDER BY ph.ngay_tao ASC
            ");
            $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
            $stmt->execute([$userId, $danhGiaId]);
            $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'replies' => $replies
            ]);
            exit;

        // ========== LIKE BÌNH LUẬN ==========
        case 'like_review':
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                exit;
            }

            $userId = (int) $_SESSION['user_id'];
            $danhGiaId = (int) ($data['danh_gia_id'] ?? 0);

            if ($danhGiaId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID bình luận không hợp lệ']);
                exit;
            }

            // Kiểm tra đã like hay chưa
            $stmt = $conn->prepare("
                SELECT id FROM like_danh_gia 
                WHERE danh_gia_id = ? AND nguoi_dung_id = ? AND trang_thai = 1
            ");
            $stmt->execute([$danhGiaId, $userId]);
            $liked = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($liked) {
                // Unlike
                $stmt = $conn->prepare("
                    UPDATE like_danh_gia SET trang_thai = 0 
                    WHERE danh_gia_id = ? AND nguoi_dung_id = ?
                ");
                $stmt->execute([$danhGiaId, $userId]);
                $action = 'unlike';
            } else {
                // Like
                $stmt = $conn->prepare("
                    INSERT INTO like_danh_gia (danh_gia_id, nguoi_dung_id, trang_thai, ngay_tao)
                    VALUES (?, ?, 1, NOW())
                    ON DUPLICATE KEY UPDATE trang_thai = 1
                ");
                $stmt->execute([$danhGiaId, $userId]);
                $action = 'like';
            }

            // Lấy số like hiện tại
            $stmt = $conn->prepare("
                SELECT COUNT(*) as so_like FROM like_danh_gia 
                WHERE danh_gia_id = ? AND trang_thai = 1
            ");
            $stmt->execute([$danhGiaId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'action' => $action,
                'so_like' => (int) $result['so_like']
            ]);
            exit;

        // ========== LIKE TRẢ LỜI ==========
        case 'like_reply':
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                exit;
            }

            $userId = (int) $_SESSION['user_id'];
            $phanHoiId = (int) ($data['phan_hoi_id'] ?? 0);

            if ($phanHoiId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID trả lời không hợp lệ']);
                exit;
            }

            $stmt = $conn->prepare("
                SELECT id FROM like_phan_hoi 
                WHERE phan_hoi_id = ? AND nguoi_dung_id = ? AND trang_thai = 1
            ");
            $stmt->execute([$phanHoiId, $userId]);
            $liked = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($liked) {
                $stmt = $conn->prepare("
                    UPDATE like_phan_hoi SET trang_thai = 0 
                    WHERE phan_hoi_id = ? AND nguoi_dung_id = ?
                ");
                $stmt->execute([$phanHoiId, $userId]);
                $action = 'unlike';
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO like_phan_hoi (phan_hoi_id, nguoi_dung_id, trang_thai, ngay_tao)
                    VALUES (?, ?, 1, NOW())
                    ON DUPLICATE KEY UPDATE trang_thai = 1
                ");
                $stmt->execute([$phanHoiId, $userId]);
                $action = 'like';
            }

            $stmt = $conn->prepare("
                SELECT COUNT(*) as so_like FROM like_phan_hoi 
                WHERE phan_hoi_id = ? AND trang_thai = 1
            ");
            $stmt->execute([$phanHoiId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'action' => $action,
                'so_like' => (int) $result['so_like']
            ]);
            exit;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
?>