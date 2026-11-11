<?php
$backend = 'https://109.123.252.43';
$path = $_SERVER['REQUEST_URI'] ?? '/';
$ch = curl_init($backend . $path);
$headers = ['Host: haircolor.nexarankai.com', 'Accept-Encoding: identity'];
if (!empty($_SERVER['HTTP_COOKIE'])) {
    $headers[] = 'Cookie: ' . $_SERVER['HTTP_COOKIE'];
}
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $headers[] = 'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'];
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_TIMEOUT => 45,
]);
if (!in_array($method, ['GET', 'HEAD'], true)) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}
$r = curl_exec($ch);
if ($r === false) {
    http_response_code(502);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><title>Hair Colors</title><p>Store loading. Please retry.</p>';
    exit;
}
$hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$st = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
http_response_code($st ?: 200);
foreach (explode("\r\n", substr($r, 0, $hs)) as $line) {
    if ($line === '' || stripos($line, 'HTTP/') === 0) continue;
    $l = strtolower($line);
    if (strpos($l, 'transfer-encoding:') === 0 || strpos($l, 'content-encoding:') === 0 || strpos($l, 'connection:') === 0) continue;
    header($line, false);
}
echo substr($r, $hs);
