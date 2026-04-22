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
    private int    $smtpPort;
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
}
?>