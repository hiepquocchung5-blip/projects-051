<?php
// /api/routes/captcha.php
// Endpoint: /api/index.php?route=captcha
// Stateless 8-Character Alphanumeric Captcha Generator

global $method;

if ($method !== 'GET') Response::error("Method not allowed.", 405);

// 1. Generate 8-character alphanumeric string (Exclude confusing chars like O, 0, I, 1, l)
$chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
$captchaText = '';
for ($i = 0; $i < 8; $i++) {
    $captchaText .= $chars[rand(0, strlen($chars) - 1)];
}

// 2. Cryptographic Signature (Valid for 5 minutes)
$exp = time() + 300;
$secret = $_ENV['JWT_SECRET'] ?? 'fallback_secret';
$signature = hash_hmac('sha256', strtoupper($captchaText) . $exp, $secret);

// 3. Generate "Circuit Chaos" SVG Image to defeat OCR bots
$svg = '<svg width="240" height="70" xmlns="http://www.w3.org/2000/svg">';
$svg .= '<rect width="100%" height="100%" fill="#050507" rx="12" ry="12" stroke="#d4af37" stroke-width="1"/>';

// Add visual noise lines
for ($i = 0; $i < 20; $i++) {
    $x1 = rand(0, 240); $y1 = rand(0, 70);
    $x2 = rand(0, 240); $y2 = rand(0, 70);
    $svg .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="#aa8c2c" stroke-width="1" opacity="0.4"/>';
}

// Add random dots
for ($i = 0; $i < 50; $i++) {
    $svg .= '<circle cx="'.rand(0, 240).'" cy="'.rand(0, 70).'" r="1" fill="#e2e8f0" opacity="0.5"/>';
}

// Render the 8 characters with slight random rotations and positions
$xStart = 20;
for ($i = 0; $i < 8; $i++) {
    $char = $captchaText[$i];
    $rot = rand(-15, 15);
    $y = rand(35, 45);
    $svg .= '<text x="'.$xStart.'" y="'.$y.'" font-family="monospace" font-size="28" font-weight="900" fill="#e2e8f0" transform="rotate('.$rot.' '.$xStart.' '.$y.')" filter="drop-shadow(0px 0px 2px #d4af37)">'.$char.'</text>';
    $xStart += 26;
}
$svg .= '</svg>';

$base64Image = 'data:image/svg+xml;base64,' . base64_encode($svg);

Response::success("Captcha generated.", [
    'image' => $base64Image,
    'hash' => $signature,
    'exp' => $exp
]);
?>