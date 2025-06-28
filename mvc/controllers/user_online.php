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
                    return true;
                }
            }
        }
        return false;
    }
    function addOnlineStatusToUsers(array $users): array
    {
        // Lấy danh sách ID đang online từ session
        $session_dir = session_save_path();
        $session_files = glob("$session_dir/sess_*");
        $online_ids = [];

        foreach ($session_files as $file) {
            if (is_readable($file) && time() - filemtime($file) < 60) {
                $content = @file_get_contents($file);
                if (
                    $content !== false &&
                    preg_match('/user_id\|s:\d+:"([^"]+)"/', $content, $matches)
                ) {
                    $online_ids[] = $matches[1];
                }
            }
        }

        // Thêm trường 'check' vào từng user trong mảng
        foreach ($users as &$user) {
            $id = is_array($user) ? $user['id'] : $user->id;
            $user['check'] = in_array($id, $online_ids);
        }

        return $users;
    }
}
?>