<?php
// /config/env_parser.php
// Custom lightweight .env parser for Urbanix

class EnvParser {
    /**
     * Load environment variables from a .env file into $_ENV and $_SERVER
     * @param string $path Absolute path to the .env file
     */
    public static function load($path) {
        if (!file_exists($path)) {
            error_log("CRITICAL ERROR: .env file missing at {$path}");
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignore comments
            if (strpos(trim($line), '#') === 0) continue;

            // Split by the first '='
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Strip quotes if present
                if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                    $value = $matches[1];
                }

                // Inject into environment
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}
?>