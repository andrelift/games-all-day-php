
<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

echo json_encode([
    'status' => 'ok',
    'php' => PHP_VERSION,
    'curl_available' => extension_loaded('curl'),
    'json_available' => extension_loaded('json'),
]);
