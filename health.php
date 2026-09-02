<?php
// Lightweight health endpoint for deployment/platform checks.
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'status' => 'ok',
    'service' => 'AI Learning Assistant',
    'timestamp' => gmdate('c'),
]);
