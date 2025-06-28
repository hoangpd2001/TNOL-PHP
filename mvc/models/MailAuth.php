<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php';
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
       // thư mục chứa file .env
        parent::__construct();
        $this->mail = new PHPMailer(true);
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $this->mail->isSMTP(); // gửi mail SMTP
        $this->mail->SMTPAuth = true;
        $this->mail->Host = $_ENV['SMTP_HOST'];
        $this->mail->Username = $_ENV['SMTP_USERNAME'];
        $this->mail->Password = $_ENV['SMTP_PASSWORD'];
        $this->mail->Port = $_ENV['SMTP_PORT'];

        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
    }

    public function sendOpt($email,$opt)
    {
        try {
            $this->mail->addAddress($email); // Name is optional
            $this->mail->isHTML(true);   // Set email format to HTML
            $this->mail->Subject = 'Ma Xac Thuc OTP';

            $this->mail->Body = '
            <div style="font-family: Helvetica, Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; background-color: #f4f4f4; border-radius: 8px;">
                <div style="background-color: #0b3d91; padding: 20px; border-radius: 8px 8px 0 0; color: white;">
                    <h2 style="margin: 0; font-size: 24px;">Trường Cao đẳng nghề Bách khoa Hà Nội</h2>
                    <p style="margin: 0; font-size: 14px;">Hactech - Hệ thống xác thực người dùng</p>
                </div>
                <div style="background-color: white; padding: 30px; border-radius: 0 0 8px 8px;">
                    <p style="font-size: 16px;">Chào bạn,</p>
                    <p style="font-size: 15px; line-height: 1.6;">
                        Cảm ơn bạn đã sử dụng hệ thống của Hactech. Mã xác thực (OTP) của bạn là:
                    </p>
                    <div style="text-align: center; margin: 20px 0;">
                        <span style="display: inline-block; background-color: #0b3d91; color: white; font-size: 24px; padding: 10px 20px; border-radius: 6px; letter-spacing: 2px;">
                            ' . $opt . '
                        </span>
                    </div>
                    <p style="font-size: 14px; color: #555;">
                        Mã OTP có hiệu lực trong vòng <strong>5 phút</strong>. Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email.
                    </p>
                    <p style="font-size: 14px; color: #999; margin-top: 30px;">Trân trọng,<br><strong>Hactech</strong></p>
                </div>
                <div style="text-align: center; font-size: 12px; color: #aaa; margin-top: 10px;">
                    Trường Cao đẳng nghề Bách khoa Hà Nội - Số 169 Trương Định, Hai Bà Trưng, Hà Nội
                </div>
            </div>';

            $this->mail->AltBody = 'Mã xác thực OTP của bạn là: ' . $opt;
            $this->mail->send();
            echo "Success send";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
    public function sendAddTest($tende, $tenmonhoc, $thoigianthi, $thoigianbatdau, $thoigianketthuc, $hinhthuc, $group, $users)
    {
        foreach ($users as $user) {
            try {
                $this->mail->clearAddresses();
                $this->mail->addAddress($user['email'], $user['hoten']);
                $this->mail->isHTML(true);
                $this->mail->Subject = "Thong Bao Giao Bai Tap";

                // Format thời gian
                $start = date("H:i d/m/Y", strtotime($thoigianbatdau));
                $end = $thoigianketthuc ? date("H:i d/m/Y", strtotime($thoigianketthuc)) : "Không giới hạn";

                // Tên giao theo
                $giaotheo = ($hinhthuc == "hocphan") ? "Lớp: ". $group["tenlop"] ." - ". $group["tenkhoahoc"] : "Nhóm: ". $group["tennhom"];
                $giaovien = $group["hoten"];

                $this->mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width:600px; margin:auto; padding:20px; background:#f9f9f9; border-radius:8px;">
                <h2 style="color:#0b3d91;">Trường Cao đẳng nghề Bách khoa Hà Nội - HACTECH</h2>
                <p>Xin chào <strong>' . htmlspecialchars($user['hoten']) . '</strong>,</p>
                <p>Bạn đã được giao một đề thi mới. Thông tin chi tiết như sau:</p>
                <ul>
                    <li><strong>Tên đề thi:</strong> ' . htmlspecialchars($tende) . '</li>
                    <li><strong>Môn học:</strong> ' . htmlspecialchars($tenmonhoc) . '</li>
                    <li><strong>Thời gian làm bài:</strong> ' . htmlspecialchars($thoigianthi) . ' phút</li>
                    <li><strong>Bắt đầu lúc:</strong> ' . $start . '</li>
                    <li><strong>Kết thúc lúc:</strong> ' . $end . '</li>
                    <li><strong>Giao theo:</strong> ' . $giaotheo . '</li>
                    <li><strong>Giảng viên phụ trách:</strong> ' . htmlspecialchars($giaovien) . '</li>
                </ul>
                <p style="margin-top:20px;">Vui lòng đăng nhập hệ thống để làm bài thi đúng thời gian quy định.</p>
                <p style="color:#999; font-size:12px;">Email này được gửi tự động từ hệ thống của HACTECH. Vui lòng không phản hồi.</p>
            </div>';

                $this->mail->AltBody = "Bạn đã được giao đề thi '$tende' môn $tenmonhoc. Bắt đầu: $start. Kết thúc: $end.";

                $this->mail->send();
            } catch (Exception $e) {
                // Log nếu cần: error_log("Lỗi gửi email tới {$user['email']}: " . $e->getMessage());
                continue;
            }
        }
    }
    public function sendAnnouncement($content, $thoigiantao, $loaigiao, $group, $users, $giaovien)
    {
        foreach ($users as $user) {
            try {
                $this->mail->clearAddresses();
                $this->mail->addAddress($user['email'], $user['hoten']);
                $this->mail->isHTML(true);
                $this->mail->Subject = "HACTECH - Thong Bao Moi";

                // Format thời gian tạo
                $thoigian = date("H:i d/m/Y", strtotime($thoigiantao));

                $giaotheo = ($loaigiao == "hocphan")
                    ? "Lớp: " . $group["tenlop"] . " - Khóa: " . $group["tenkhoahoc"]
                    : "Nhóm: " . $group["tennhom"];

                $this->mail->Body = '
                    <div style="font-family: Arial, sans-serif; max-width:600px; margin:auto; padding:20px; background:#f9f9f9; border-radius:8px;">
                        <h2 style="color:#0b3d91;">Trường Cao đẳng nghề Bách khoa Hà Nội - HACTECH</h2>
                        <p>Xin chào <strong>' . htmlspecialchars($user['hoten']) . '</strong>,</p>
                        <p>Bạn vừa nhận được một thông báo mới:</p>
                        <ul>
                            <li><strong>Giao theo:</strong> ' . $giaotheo . '</li>
                            <li><strong>Thời gian gửi:</strong> ' . $thoigian . '</li>
                            <li><strong>Người thông báo:</strong> ' . htmlspecialchars($giaovien['hoten']) . '</li>
                            <li><strong>Nội dung:</strong><br>' . nl2br(htmlspecialchars($content)) . '</li>
                        </ul>
                        <p style="margin-top:20px;">Vui lòng đăng nhập vào hệ thống để xem chi tiết.</p>
                        <p style="color:#999; font-size:12px;">Email này được gửi tự động từ hệ thống của HACTECH. Vui lòng không phản hồi.</p>
                    </div>';

                $this->mail->AltBody = "Bạn có thông báo mới từ giáo viên " . $giaovien['hoten'] . ": " . $content;

                $this->mail->send();
            } catch (Exception $e) {
                // Ghi log lỗi nếu cần
                // error_log("Lỗi gửi email tới {$user['email']}: " . $e->getMessage());
                continue;
            }
        }
    }
}