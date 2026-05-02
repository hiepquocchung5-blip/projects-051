<?php
// /api/includes/Response.php
// Standardizes all JSON outputs

class Response {
    public static function success($message, $data = [], $code = 200) {
        http_response_code($code);
        echo json_encode([
            "status" => "success",
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    public static function error($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            "status" => "error",
            "message" => $message
        ]);
        exit;
    }
}
?>