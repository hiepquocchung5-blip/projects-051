<?php
// /api/includes/JWT.php
// Pure PHP JSON Web Token System

class JWT {
    private static function getSecret() {
        return $_ENV['JWT_SECRET'] ?? 'fallback_circuit_chaos_secret_key_99';
    }

    private static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public static function encode($payload) {
        $header = self::base64url_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload['exp'] = time() + (86400 * 7); // 7-day expiration for convenience
        $payload['iat'] = time();
        
        $payloadEnc = self::base64url_encode(json_encode($payload));
        $signature = self::base64url_encode(hash_hmac('sha256', "$header.$payloadEnc", self::getSecret(), true));
        
        return "$header.$payloadEnc.$signature";
    }

    public static function validate() {
        $headers = null;
        
        // Handle Nginx/Apache differences in header parsing
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $headers = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        } else {
            $headers = [
                'Authorization' => isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : ''
            ];
        }

        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error("Unauthorized. Secure Token Missing.", 401);
        }

        $token = $matches[1];
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) Response::error("Malformed Security Token.", 401);
        
        list($header, $payload, $signature) = $parts;
        $validSignature = self::base64url_encode(hash_hmac('sha256', "$header.$payload", self::getSecret(), true));

        if (!hash_equals($validSignature, $signature)) {
            Response::error("Token Encryption Compromised.", 401);
        }

        $decodedPayload = json_decode(self::base64url_decode($payload), true);
        
        if (isset($decodedPayload['exp']) && time() > $decodedPayload['exp']) {
            Response::error("Security Token Expired. Reboot session.", 401);
        }

        return $decodedPayload;
    }
}
?>