<?php
// /api/includes/JWT.php
// Zero-Dependency JSON Web Token Encoder/Decoder

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
        
        // Add expiration (24 hours)
        $payload['exp'] = time() + (86400); 
        $payload['iat'] = time();
        
        $payload = self::base64url_encode(json_encode($payload));
        $signature = self::base64url_encode(hash_hmac('sha256', "$header.$payload", self::getSecret(), true));
        
        return "$header.$payload.$signature";
    }

    public static function validate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error("Unauthorized. Missing or invalid Bearer token.", 401);
        }

        $token = $matches[1];
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) Response::error("Malformed Token.", 401);
        
        list($header, $payload, $signature) = $parts;
        $validSignature = self::base64url_encode(hash_hmac('sha256', "$header.$payload", self::getSecret(), true));

        if (!hash_equals($validSignature, $signature)) {
            Response::error("Signature Verification Failed.", 401);
        }

        $decodedPayload = json_decode(self::base64url_decode($payload), true);
        
        if (isset($decodedPayload['exp']) && time() > $decodedPayload['exp']) {
            Response::error("Token Expired. Re-authenticate.", 401);
        }

        return $decodedPayload;
    }
}
?>