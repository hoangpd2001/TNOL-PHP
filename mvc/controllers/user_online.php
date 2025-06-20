<?php  
class User_Online extends Controller {
    public function default()
    {
        $session_dir = session_save_path();
        $session_files = glob("$session_dir/sess_*");

        $online_users = 0;

        foreach ($session_files as $session_file) {
            if (time() - filemtime($session_file) < 60) {
                $online_users++;
            }
        }
        echo "Số người dùng đang trực tuyến: $online_users";
    }
    function isUserOnline($userId)
    {
        $session_dir = session_save_path(); // Thư mục lưu session (vd: /tmp hoặc C:\xampp\tmp)
        $session_files = glob("$session_dir/sess_*");

        foreach ($session_files as $file) {
            if (is_readable($file) && time() - filemtime($file) < 60) {
                $content = @file_get_contents($file);
                if ($content !== false && strpos($content, $userId) !== false) {
                    echo "Người dùng đang trực tuyến 🟢";
                    return;
                }
            }
        }
        echo "Người dùng đang không trực tuyến 🔴";
    }
    function getOnlineUserIds(): array
    {
        $session_dir = session_save_path();
        $session_files = glob("$session_dir/sess_*");
        $online_users = [];

        foreach ($session_files as $file) {
            if (is_readable($file) && time() - filemtime($file) < 60) {
                $content = @file_get_contents($file);
                if ($content !== false && preg_match('/user_id\|s:\d+:"([^"]+)"/', $content, $matches)) {
                    $online_users[] = $matches[1]; // user_id dạng CDxxxx
                }
            }
        }
        return $online_users;
    }
}
?>