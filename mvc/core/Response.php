<?php

namespace Core;

class Response
{
    private bool   $status;
    private string $message;
    private $data;

    public function __construct(bool $status, string $message = '', $data = null)
    {
        $this->status  = $status;
        $this->message = $message;
        $this->data    = $data;
    }

    public function send(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status'  => $this->status,
            'message' => $this->message,
            'data'    => $this->data,
        ]);
        exit;
    }

    // ✅ Thêm hàm static để có thể dùng Response::json()
    public static function json(bool $status, string $message = '', $data = null, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ]);
        exit;
    }
}
