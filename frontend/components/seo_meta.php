<?php
// /frontend/components/seo_meta.php
// Production Metadata & SEO for Real World Deployment

function renderMeta($title = null, $desc = null) {
    $title = $title ? $title . " | " . APP_NAME : APP_NAME . " | Premium Gaming Ecosystem";
    $desc = $desc ?? "Simulate, Convert, and Extract assets within the Urbanix network. Myanmar's premier digital asset gaming portal.";
    
    echo '
    <meta name="description" content="' . $desc . '">
    <meta property="og:title" content="' . $title . '">
    <meta property="og:description" content="' . $desc . '">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#0a0a0c">
    <link rel="icon" type="image/png" href="' . BASE_URL . '/assets/favicon.png">
    ';
}
?>