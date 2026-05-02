<?php
// /api/routes/captcha.php
// Endpoint: /api/index.php?route=captcha

global $method;
if ($method !== 'GET') Response::error("Method not allowed.", 405);

$chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
$captchaText = '';
for ($i = 0; $i < 8; $i++) { $captchaText .= $chars[rand(0, strlen($chars) - 1)]; }

$exp = time() + 300;
$secret = $_ENV['JWT_SECRET'] ?? 'fallback_secret';
$signature = hash_hmac('sha256', strtoupper($captchaText) . $exp, $secret);

$svg = '<svg width="240" height="70" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#050507" rx="12" ry="12" stroke="#d4af37" stroke-width="1"/>';
for ($i = 0; $i < 20; $i++) { $svg .= '<line x1="'.rand(0, 240).'" y1="'.rand(0, 70).'" x2="'.rand(0, 240).'" y2="'.rand(0, 70).'" stroke="#aa8c2c" stroke-width="1" opacity="0.4"/>'; }
$xStart = 20;
for ($i = 0; $i < 8; $i++) {
    $svg .= '<text x="'.$xStart.'" y="'.rand(35, 45).'" font-family="monospace" font-size="28" font-weight="900" fill="#e2e8f0" transform="rotate('.rand(-15, 15).' '.$xStart.' '.rand(35, 45).')">'.$captchaText[$i].'</text>';
    $xStart += 26;
}
$svg .= '</svg>';

Response::success("Captcha generated.", ['image' => 'data:image/svg+xml;base64,' . base64_encode($svg), 'hash' => $signature, 'exp' => $exp]);
?>