<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailSender
{
    private $config;

    public function __construct($configPath = 'config2.json')
    {
        // JSON dosyasından konfigürasyon bilgilerini oku
        $this->config = json_decode(file_get_contents($configPath), true);
    }

    public function sendMail($pdfData)
    {
        $mail = new PHPMailer();

        try {
            $mail->isSMTP();

            $mail->SMTPKeepAlive = true;
            $mail->SMTPAuth = true;

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->Host = $this->config['smtpInfo']['smtp'];

            $mail->Username = $this->config['smtpInfo']['from'];
            $mail->Password = $this->config['smtpInfo']['password'];
            
            // Diğer mail ayarlarını yapabilirsiniz
            $mail->setFrom($this->config['smtpInfo']['from'], $this->config['smtpInfo']['from']);
            $mail->addAddress(address:$this->config['smtpInfo']['to']);
            $mail->isHTML(true);
            $mail->Subject = $this->config['smtpInfo']['subject'];
            $mail->Body = '<html><head><meta charset="UTF-8"></head><body></body></html>';
            
            $mail->addStringAttachment($pdfData, 'attachment.pdf');


            // Maili gönder
            $mail->send();

            echo 'Mail başarıyla gönderildi.';
        } catch (Exception $e) {
            echo 'Mail gönderirken bir hata oluştu: ' . $mail->ErrorInfo;
        }
    }
}

?>