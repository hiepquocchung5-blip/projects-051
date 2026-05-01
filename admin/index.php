<?php
// /admin/index.php
// Secure Front Controller for the CMS

require_once '../config/globals.php';
require_once '../config/database.php';

// Security Check: Ensure user is Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to frontend or show error
    die("<div style='background:#0a0a0f;color:#ef4444;height:100vh;display:flex;align-items:center;justify-content:center;font-family:monospace;font-size:2rem;font-weight:bold;'>ACCESS DENIED: ADMIN PRIVILEGES REQUIRED.</div>");
}

$route = isset($_GET['route']) ? $_GET['route'] : 'dashboard';

include_once 'includes/header.php';

switch ($route) {
    case 'dashboard':
        include_once 'pages/dashboard.php';
        break;
    default:
        echo "<h2 class='text-center mt-10 text-white font-mono'>Module Not Found</h2>";
        break;
}
?>
<!-- Footer simple closure -->
        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>