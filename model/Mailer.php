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
}
?>