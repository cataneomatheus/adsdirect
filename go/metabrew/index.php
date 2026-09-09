<?php
declare(strict_types=1);

const METABREW_DESTINATION_URL = 'https://www.sailgeneral.com/375Q8F6Z/24K6LPT5/';

$consent = $_POST['consent'] ?? 'rejected';
$accepted = $consent === 'accepted';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setcookie('metabrew_consent', $accepted ? 'accepted' : 'rejected', [
        'expires' => time() + 60 * 60 * 24 * 180,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

$params = [];
if ($accepted) {
    foreach (['gclid', 'gbraid', 'wbraid', 'msclkid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'] as $key) {
        $value = $_POST[$key] ?? null;
        if (is_string($value) && $value !== '' && strlen($value) <= 2048 && !preg_match('/[\x00-\x1F\x7F]/', $value)) {
            $params[$key] = $value;
        }
    }
    if (isset($params['gclid'])) $params['sub2'] = $params['gclid'];
    if (isset($params['utm_source'])) $params['sub1'] = $params['utm_source'];
    $parts = array_map(static function ($key) use ($params) { return $params[$key] ?? ''; }, ['utm_medium', 'utm_campaign', 'utm_content', 'utm_term']);
    if (implode('', $parts) !== '') $params['sub3'] = implode('cXnXl', $parts);
}

// Os parâmetros fixos do link de afiliado têm prioridade.
$fixed = [];
parse_str((string) parse_url(METABREW_DESTINATION_URL, PHP_URL_QUERY), $fixed);
$params = array_diff_key($params, $fixed);
$url = METABREW_DESTINATION_URL;
if ($params !== []) {
    $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
header('Location: ' . $url, true, 302);
exit;
