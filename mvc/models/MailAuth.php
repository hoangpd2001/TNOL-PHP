<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/OAuth.php';
require 'vendor/phpmailer/phpmailer/src/POP3.php';

class MailAuth extends DB
{
    protected $mail;

    public function __construct()
    {
        parent::__construct();
        $this->mail = new PHPMailer(true);
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $this->mail->isSMTP(); // gửi mail SMTP
        $this->mail->Host = getenv('SMTP_HOST'); // SMTP server
        $this->mail->SMTPAuth = true;
        $this->mail->Username = getenv('SMTP_USERNAME'); // SMTP username
        $this->mail->Password = getenv('SMTP_PASSWORD'); // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = getenv('SMTP_PORT'); // SMTP port
        $this->mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
    }

    public function sendOpt($email,$opt)
    {
        try {
            $this->mail->addAddress($email); // Name is optional
            $this->mail->isHTML(true);   // Set email format to HTML
            $this->mail->Subject = 'Code OPT';
            $this->mail->Body = '<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
                <div style="margin:50px auto;width:70%;padding:20px 0">
                <div style="border-bottom:1px solid #eee">
                    <a href="#" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">SGU TEST</a>
                </div>
                <p style="font-size:1.1em">Hi,</p>
                <p>Thank you for choosing Your Brand. Use the following OTP to complete your Sign Up procedures. OTP is valid for 5 minutes</p>
                <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">'.$opt.'</h2>
                <p style="font-size:0.9em;">Regards,<br />SGU TEST</p>
                <hr style="border:none;border-top:1px solid #eee" />
                <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
                    <p>QA Inc</p>
                    <p>Số 273 An Dương Vương, Phường 3, Quận 5, TP. HCM</p>
                    <p>Việt Nam</p>
                </div>
                </div>
                </div>';
            $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $this->mail->send();
            echo "Success send";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
}