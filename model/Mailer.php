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

}
?>