<?php
declare(strict_types=1);

const SLIMQA_DESTINATION_URL = 'https://www.clickrtrckr.com/LGXNB45/94JB5LK/?source_id=slimqa';

$clickIds = [];
foreach (['gclid'] as $key) {
    if (!isset($_GET[$key]) || !is_string($_GET[$key])) {
        continue;
    }

    $value = $_GET[$key];
    if (strlen($value) <= 1024 && !preg_match('/[\x00-\x1F\x7F]/', $value)) {
        $clickIds[$key] = $value;
    }
}

$destinationUrl = SLIMQA_DESTINATION_URL;
if ($clickIds !== []) {
    $separator = strpos($destinationUrl, '?') !== false ? '&' : '?';
    $destinationUrl .= $separator . http_build_query($clickIds, '', '&', PHP_QUERY_RFC3986);
}

header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
header('Location: ' . $destinationUrl, true, 302);
exit;
