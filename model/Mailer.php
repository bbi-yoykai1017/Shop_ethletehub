<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
class Mailer
{
    // cau hinh smtp 
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUser;
    private string $smtpPass;
    private string $fromEmail = 'shopathletehub@gmail.com';
    private string $fromName = 'AthleteHub';
    private string $shopUrl = 'https://shopethletehub.kesug.com/';

    public function __construct()
    {
        $this->smtpHost = $_ENV['SMTP_HOST'];
        $this->smtpPort = (int) $_ENV['SMTP_PORT'];
        $this->smtpUser = $_ENV['SMTP_USER'];
        $this->smtpPass = $_ENV['SMTP_PASS'];
    }

    /**
     * gui email xac nhan don hang cho khach hang
     *
     * @param array $order  thong tin don hang
     * @param array $items  danh sach san pham trong don hang
     * @param array $user   thong tin khach hang
     * @return array        
     */
    public function sendOrderConfirmation(array $order, array $items, array $user): array
    {
        $toEmail = trim($user['email'] ?? '');
        $toName = $order['ten_nguoi_nhan'] ?? $user['ten'] ?? 'Quý khách';

        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Email khách hàng không hợp lệ: ' . $toEmail;
            error_log('[Mailer] ' . $msg);
            return ['sent' => false, 'error' => $msg];
        }

        $subject = 'AthleteHub - Xác nhận đơn hàng #' . $order['ma_don_hang'];
        $html = $this->buildOrderEmailHTML($order, $items, $toName);
        $plain = $this->buildPlainText($order, $items, $toName);

        return $this->sendViaSMTP($toEmail, $toName, $subject, $html, $plain);
    }
    /**
     * gui email xac nhan dat hang thanh cong cho khach hang
     * @param string $toEmail
     * @param string $toName
     * @param string $subject
     * @param string $html
     * @param string $plain
     * @return array{error: null, sent: bool|array{error: string, sent: bool}}
     */
    private function sendViaSMTP(
        string $toEmail,
        string $toName,
        string $subject,
        string $html,
        string $plain
    ): array {
        try {
            $mail = new PHPMailer(true); // true = bat exception

            // Server
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUser;
            $mail->Password = $this->smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->smtpPort;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;

            // nguoi gui nguoi nhan
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo($this->fromEmail, $this->fromName);

            //  noi dung
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = $plain; // noi dung thuong de hien thi khi mail client khong ho tro HTML

            $mail->send();

            error_log('[Mailer]  Gửi thành công → ' . $toEmail);
            return ['sent' => true, 'error' => null];

        } catch (PHPMailerException $e) {
            $msg = 'PHPMailer error: ' . $e->getMessage();
            error_log('[Mailer] ❌ ' . $msg);
            return ['sent' => false, 'error' => $msg];

        } catch (\Exception $e) {
            $msg = 'Unexpected error: ' . $e->getMessage();
            error_log('[Mailer] ❌ ' . $msg);
            return ['sent' => false, 'error' => $msg];
        }
    }
    /**
     *  fallback cho cac email client cu 
     * @param array $order
     * @param array $items 
     * @param string $toName
     * @return string
     */
    private function buildPlainText(array $order, array $items, string $toName): string
    {
        $lines = [];
        $lines[] = "AthleteHub - Xác nhận đơn hàng";
        $lines[] = str_repeat('=', 40);
        $lines[] = "Xin chào $toName,";
        $lines[] = "Đơn hàng của bạn đã được đặt thành công!";
        $lines[] = "";
        $lines[] = "Mã đơn:    #" . $order['ma_don_hang'];
        $lines[] = "Ngày đặt:  " . date('d/m/Y H:i', strtotime($order['ngay_dat'] ?? 'now'));
        $lines[] = "Địa chỉ:   " . $order['dia_chi_giao_hang'];
        $lines[] = "SĐT:       " . $order['so_dien_thoai_nhan'];
        $lines[] = "";
        $lines[] = "SẢN PHẨM:";
        foreach ($items as $item) {
            $lines[] = "- " . $item['ten'] . " x" . $item['so_luong']
                . " = " . number_format($item['gia'] * $item['so_luong'], 0, ',', '.') . "đ";
        }
        $lines[] = "";
        $lines[] = "Tổng tiền:  " . number_format($order['tong_tien'], 0, ',', '.') . "đ";
        $lines[] = "Giảm giá:  -" . number_format($order['tien_giam'], 0, ',', '.') . "đ";
        $lines[] = "Thành tiền: " . number_format($order['thanh_tien'], 0, ',', '.') . "đ";
        $lines[] = "";
        $lines[] = "Xem đơn hàng: " . $this->shopUrl . "/orders.php";
        $lines[] = "";
        $lines[] = "Cảm ơn bạn đã mua sắm tại AthleteHub!";
        return implode("\n", $lines);
    }
   
    /**
     * Summary of buildOrderEmailHTML
     * @param array $order
     * @param array $items
     * @param string $toName
     * @return string
     */
    private function buildOrderEmailHTML(array $order, array $items, string $toName): string
    {
        $maDonHang = htmlspecialchars($order['ma_don_hang']); // 
        $ngayDat = date('d/m/Y H:i', strtotime($order['ngay_dat'] ?? 'now'));
        $diaChiGiao = htmlspecialchars($order['dia_chi_giao_hang']);
        $soDienThoai = htmlspecialchars($order['so_dien_thoai_nhan']);

        $phuongThuc = match ($order['phuong_thuc_thanh_toan'] ?? 'tien_mat') {
            'tien_mat' => '💵 Thanh toán khi nhận hàng (COD)',
            'bank_transfer' => '🏦 Chuyển khoản ngân hàng',
            default => htmlspecialchars($order['phuong_thuc_thanh_toan'])
        };

        $tongTien = number_format($order['tong_tien'] ?? 0, 0, ',', '.');
        $tienGiam = number_format($order['tien_giam'] ?? 0, 0, ',', '.');
        $thanhTien = number_format($order['thanh_tien'] ?? 0, 0, ',', '.');

        $phiShip = ($order['tong_tien'] ?? 0) >= 500000 ? 0 : 25000;
        $phiShipStr = $phiShip === 0
            ? '<span style="color:#28a745">Miễn phí</span>'
            : number_format($phiShip, 0, ',', '.') . 'đ';

        // Render từng sản phẩm
        $itemsHTML = '';
        foreach ($items as $item) {
            $tenSP = htmlspecialchars($item['ten'] ?? '');
            $soLuong = (int) ($item['so_luong'] ?? 1);
            $gia = number_format($item['gia'] ?? 0, 0, ',', '.');
            $thanhTienItem = number_format(($item['gia'] ?? 0) * $soLuong, 0, ',', '.');

            $variant = '';
            if (!empty($item['kich_thuoc']))
                $variant .= 'Size: ' . htmlspecialchars($item['kich_thuoc']);
            if (!empty($item['mau_sac']))
                $variant .= ($variant ? ' | ' : '') . 'Màu: ' . htmlspecialchars($item['mau_sac']);
            $variantHTML = $variant ? "<br><small style='color:#888;'>$variant</small>" : '';

            $itemsHTML .= "
            <tr>
                <td style='padding:10px 8px;border-bottom:1px solid #eee;'>
                    <strong style='color:#222;'>$tenSP</strong>$variantHTML
                </td>
                <td style='padding:10px 8px;border-bottom:1px solid #eee;text-align:center;color:#555;'>x$soLuong</td>
                <td style='padding:10px 8px;border-bottom:1px solid #eee;text-align:right;color:#555;'>{$gia}đ</td>
                <td style='padding:10px 8px;border-bottom:1px solid #eee;text-align:right;font-weight:bold;color:#ff6b35;'>{$thanhTienItem}đ</td>
            </tr>";
        }

        $shopUrl = $this->shopUrl;
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Xác nhận đơn hàng - AthleteHub</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
<tr><td align="center">
  <table width="620" cellpadding="0" cellspacing="0"
         style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
 
    <!-- HEADER -->
    <tr>
      <td style="background:linear-gradient(135deg,#ff6b35,#e55a24);padding:36px 40px;text-align:center;">
        <h1 style="margin:0;color:#fff;font-size:28px;font-weight:800;letter-spacing:1px;">🏋️ AthleteHub</h1>
        <p style="margin:10px 0 0;color:rgba(255,255,255,0.9);font-size:15px;">Sản phẩm thể thao chất lượng cao</p>
      </td>
    </tr>
 
    <!-- SUCCESS BANNER -->
    <tr>
      <td style="background:#28a745;padding:18px 40px;text-align:center;">
        <p style="margin:0;color:#fff;font-size:18px;font-weight:bold;">✅ Đặt hàng thành công!</p>
      </td>
    </tr>
 
    <!-- BODY -->
    <tr>
      <td style="padding:36px 40px;">
        <p style="color:#444;font-size:16px;margin-top:0;">
          Xin chào <strong style="color:#ff6b35;">$toName</strong>,
        </p>
        <p style="color:#555;font-size:15px;line-height:1.7;">
          Cảm ơn bạn đã tin tưởng mua sắm tại <strong>AthleteHub</strong>! 🎉<br>
          Đơn hàng của bạn đã được tiếp nhận và đang chờ xác nhận.
        </p>
 
        <!-- INFO BOX -->
        <table width="100%" cellpadding="0" cellspacing="0"
               style="background:#fff8f5;border:2px solid #ff6b35;border-radius:10px;margin:24px 0;">
          <tr><td style="padding:20px 24px;">
            <table width="100%" cellpadding="6" cellspacing="0">
              <tr>
                <td style="color:#888;font-size:13px;width:50%;">📦 Mã đơn hàng:</td>
                <td style="font-weight:bold;font-size:15px;color:#ff6b35;text-align:right;">#$maDonHang</td>
              </tr>
              <tr>
                <td style="color:#888;font-size:13px;">📅 Ngày đặt:</td>
                <td style="color:#333;font-size:14px;text-align:right;">$ngayDat</td>
              </tr>
              <tr>
                <td style="color:#888;font-size:13px;">📞 SĐT nhận hàng:</td>
                <td style="color:#333;font-size:14px;text-align:right;">$soDienThoai</td>
              </tr>
              <tr>
                <td style="color:#888;font-size:13px;">📍 Địa chỉ giao:</td>
                <td style="color:#333;font-size:14px;text-align:right;">$diaChiGiao</td>
              </tr>
              <tr>
                <td style="color:#888;font-size:13px;">💳 Thanh toán:</td>
                <td style="color:#333;font-size:14px;text-align:right;">$phuongThuc</td>
              </tr>
            </table>
          </td></tr>
        </table>
 
        <!-- ITEMS TABLE -->
        <h3 style="color:#333;font-size:16px;margin:28px 0 12px;border-bottom:2px solid #ff6b35;padding-bottom:8px;">
          🛍️ Sản phẩm đã đặt
        </h3>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
          <thead>
            <tr style="background:#f8f8f8;">
              <th style="padding:10px 8px;text-align:left;color:#555;font-weight:600;border-bottom:2px solid #eee;">Sản phẩm</th>
              <th style="padding:10px 8px;text-align:center;color:#555;font-weight:600;border-bottom:2px solid #eee;">SL</th>
              <th style="padding:10px 8px;text-align:right;color:#555;font-weight:600;border-bottom:2px solid #eee;">Đơn giá</th>
              <th style="padding:10px 8px;text-align:right;color:#555;font-weight:600;border-bottom:2px solid #eee;">Thành tiền</th>
            </tr>
          </thead>
          <tbody>$itemsHTML</tbody>
        </table>
 
        <!-- TOTALS -->
        <table width="100%" cellpadding="6" cellspacing="0" style="margin-top:20px;font-size:14px;">
          <tr>
            <td style="color:#666;">Tổng tiền hàng:</td>
            <td style="text-align:right;color:#333;">{$tongTien}đ</td>
          </tr>
          <tr>
            <td style="color:#666;">Phí vận chuyển:</td>
            <td style="text-align:right;">$phiShipStr</td>
          </tr>
          <tr>
            <td style="color:#28a745;">🎟️ Giảm giá:</td>
            <td style="text-align:right;color:#28a745;">-{$tienGiam}đ</td>
          </tr>
          <tr>
            <td colspan="2"><hr style="border:none;border-top:2px solid #eee;margin:8px 0;"></td>
          </tr>
          <tr>
            <td style="font-size:17px;font-weight:bold;color:#222;">💰 Tổng cộng:</td>
            <td style="text-align:right;font-size:20px;font-weight:bold;color:#ff6b35;">{$thanhTien}đ</td>
          </tr>
        </table>
 
        <!-- CTA BUTTON -->
        <div style="text-align:center;margin:32px 0;">
          <a href="{$shopUrl}/orders.php"
             style="background:#ff6b35;color:#fff;padding:14px 36px;border-radius:8px;
                    text-decoration:none;font-weight:bold;font-size:16px;display:inline-block;">
            📋 Xem đơn hàng của bạn
          </a>
        </div>
 
        <!-- BENEFITS -->
        <table width="100%" cellpadding="0" cellspacing="0"
               style="background:#f8f9fa;border-radius:10px;margin-bottom:20px;">
          <tr>
            <td style="padding:16px 12px;text-align:center;color:#555;font-size:13px;">
              🚚 <strong>Giao hàng 2-5 ngày</strong>
            </td>
            <td style="padding:16px 12px;text-align:center;color:#555;font-size:13px;">
              🔒 <strong>Thanh toán an toàn</strong>
            </td>
            <td style="padding:16px 12px;text-align:center;color:#555;font-size:13px;">
              🔄 <strong>Hoàn hàng 30 ngày</strong>
            </td>
          </tr>
        </table>
 
        <p style="color:#888;font-size:13px;line-height:1.7;border-top:1px solid #eee;padding-top:20px;margin-bottom:0;">
          Thắc mắc? Liên hệ:<br>
          📧 <a href="mailto:support@athletehub.vn" style="color:#ff6b35;">support@athletehub.vn</a>
          &nbsp;|&nbsp; 📞 <strong>+84 (0) 123 456 789</strong>
        </p>
      </td>
    </tr>
 
    <!-- FOOTER -->
    <tr>
      <td style="background:#222;padding:20px 40px;text-align:center;">
        <p style="margin:0;color:#aaa;font-size:13px;">
          © $year <strong style="color:#ff6b35;">AthleteHub</strong>. Bảo lưu mọi quyền.<br>
          <a href="{$shopUrl}" style="color:#ff6b35;text-decoration:none;">athletehub.vn</a>
        </p>
      </td>
    </tr>
 
  </table>
</td></tr>
</table>
</body>
</html>
HTML;
    }
}

?>