<?php
// /frontend/includes/header_minimal.php
// Clean header for Auth pages - Injects ENV variables securely
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Authentication | <?= defined('APP_NAME') ? APP_NAME : 'URBANIX' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <script>
        // CRITICAL: Tell Javascript exactly where the API Gateway is
        window.URBANIX_CONFIG = {
            googleClientId: "<?= $_ENV['GOOGLE_CLIENT_ID'] ?? '' ?>",
            apiUrl: "<?= defined('API_URL') ? API_URL : '' ?>"
        };
        
        tailwind.config = { theme: { extend: { colors: { premium: { dark: '#0a0a0c', panel: '#151518', silver: '#e2e8f0', gold: '#d4af37', goldDark: '#aa8c2c' } } } } }
    </script>

    <script src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/js/api_client.js"></script>

    <style>
        body { margin: 0; overflow: hidden; background-color: #0a0a0c; color: #e2e8f0; }
        #canvas-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; opacity: 0.4; }
        .glass-panel { background: rgba(21, 21, 24, 0.7); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5); }
        
        /* Captcha Specific Styles */
        .captcha-box { filter: drop-shadow(0 0 10px rgba(212,175,55,0.2)); transition: all 0.3s ease; }
        .captcha-box:hover { filter: drop-shadow(0 0 15px rgba(212,175,55,0.4)); }
    </style>
</head>
<body>
<div id="canvas-container"></div>
<div id="ui-layer" class="absolute inset-0 z-10 overflow-y-auto pointer-events-auto flex items-center justify-center">