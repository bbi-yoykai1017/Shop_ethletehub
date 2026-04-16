<?php

// Lấy tất cả tin tức (có phân trang)
function getAllNews($conn, $page = 1, $limit = 10, $loai_tin = null, $trang_thai = 1) {
    $offset = ($page - 1) * $limit;
    
    $sql = "SELECT * FROM tin_tuc WHERE trang_thai = :trang_thai";
    
    if ($loai_tin) {
        $sql .= " AND loai_tin = :loai_tin";
    }
    
    $sql .= " ORDER BY ngay_tao DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':trang_thai', $trang_thai, PDO::PARAM_INT);
    
    if ($loai_tin) {
        $stmt->bindValue(':loai_tin', $loai_tin, PDO::PARAM_STR);
    }
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy tổng số tin tức
function countNews($conn, $loai_tin = null, $trang_thai = 1) {
    $sql = "SELECT COUNT(*) as total FROM tin_tuc WHERE trang_thai = :trang_thai";
    
    if ($loai_tin) {
        $sql .= " AND loai_tin = :loai_tin";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':trang_thai', $trang_thai, PDO::PARAM_INT);
    
    if ($loai_tin) {
        $stmt->bindValue(':loai_tin', $loai_tin, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Lấy tin tức theo ID
function getNewsById($conn, $id) {
    $sql = "UPDATE tin_tuc SET luot_xem = luot_xem + 1 WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $sql = "SELECT * FROM tin_tuc WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy tin tức mới nhất
function getLatestNews($conn, $limit = 5) {
    $sql = "SELECT * FROM tin_tuc WHERE trang_thai = 1 ORDER BY ngay_tao DESC LIMIT :limit";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Thêm tin tức mới (chỉ admin)
function addNews($conn, $tieu_de, $noi_dung, $loai_tin = 'san-pham-moi', $hinh_anh = null, $admin_id = null, $trang_thai = 1) {
    $sql = "INSERT INTO tin_tuc (tieu_de, noi_dung, loai_tin, hinh_anh, admin_id, trang_thai) 
            VALUES (:tieu_de, :noi_dung, :loai_tin, :hinh_anh, :admin_id, :trang_thai)";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindValue(':tieu_de', $tieu_de, PDO::PARAM_STR);
    $stmt->bindValue(':noi_dung', $noi_dung, PDO::PARAM_STR);
    $stmt->bindValue(':loai_tin', $loai_tin, PDO::PARAM_STR);
    $stmt->bindValue(':hinh_anh', $hinh_anh, PDO::PARAM_STR);
    $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindValue(':trang_thai', $trang_thai, PDO::PARAM_INT);
    
    return $stmt->execute();
}

// Cập nhật tin tức (chỉ admin)
function updateNews($conn, $id, $tieu_de, $noi_dung, $loai_tin, $hinh_anh = "", $trang_thai) {
    $sql = "UPDATE tin_tuc SET tieu_de = :tieu_de, noi_dung = :noi_dung, 
            loai_tin = :loai_tin, trang_thai = :trang_thai";
    
    if ($hinh_anh) {
        $sql .= ", hinh_anh = :hinh_anh";
    }
    
    $sql .= " WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':tieu_de', $tieu_de, PDO::PARAM_STR);
    $stmt->bindValue(':noi_dung', $noi_dung, PDO::PARAM_STR);
    $stmt->bindValue(':loai_tin', $loai_tin, PDO::PARAM_STR);
    $stmt->bindValue(':trang_thai', $trang_thai, PDO::PARAM_INT);
    
    if ($hinh_anh) {
        $stmt->bindValue(':hinh_anh', $hinh_anh, PDO::PARAM_STR);
    }
    
    return $stmt->execute();
}

// Xóa tin tức (chỉ admin)
function deleteNews($conn, $id) {
    $sql = "DELETE FROM tin_tuc WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Lấy tất cả tin tức cho admin (bao gồm cả draft)
function getAllNewsForAdmin($conn, $page = 1, $limit = 10) {
    $offset = ($page - 1) * $limit;
    
    $sql = "SELECT * FROM tin_tuc ORDER BY ngay_tao DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy tổng số tin tức cho admin
function countAllNews($conn) {
    $sql = "SELECT COUNT(*) as total FROM tin_tuc";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Hàm format loại tin
function getNewsTypeLabel($loai_tin) {
    $labels = [
        'san-pham-moi' => 'Sản phẩm mới',
        'khuyen-mai' => 'Khuyến mãi',
        'su-kien' => 'Sự kiện',
        'other' => 'Khác'
    ];
    return $labels[$loai_tin] ?? 'Khác';
}

// Hàm format email
function truncateText($text, $length = 150) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

?>
