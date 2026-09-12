<?php
declare(strict_types=1);

// Insérer ici l'URL finale de l'offre MedicGLP avant la mise en ligne.
const MEDICGLP_DESTINATION_URL = 'https://www.sailgeneral.com/375Q8F6Z/24139LMS/';

if (MEDICGLP_DESTINATION_URL === '') {
    http_response_code(503);
    header('Cache-Control: no-store');
    header('X-Robots-Tag: noindex, nofollow');
    header('Content-Type: text/plain; charset=UTF-8');
    echo "L'offre MedicGLP sera bientôt disponible.";
    exit;
}

$consent = $_POST['consent'] ?? 'rejected';
$accepted = $consent === 'accepted';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setcookie('medicglp_consent', $accepted ? 'accepted' : 'rejected', [
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

// Les paramètres fixes du lien d'affiliation ont la priorité.
$fixed = [];
parse_str((string) parse_url(MEDICGLP_DESTINATION_URL, PHP_URL_QUERY), $fixed);
$params = array_diff_key($params, $fixed);
$url = MEDICGLP_DESTINATION_URL;
if ($params !== []) {
    $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
header('Location: ' . $url, true, 302);
exit;
