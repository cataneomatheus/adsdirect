<?php
declare(strict_types=1);

const FORCEVITAL_AFFILIATE_URL = 'https://www.sailgeneral.com/375Q8F6Z/247GFKL5/?source_id=force-vital-fr';

$fixedQueryKeys = [];
$fixedQuery = parse_url(FORCEVITAL_AFFILIATE_URL, PHP_URL_QUERY);
if (is_string($fixedQuery)) {
    parse_str($fixedQuery, $fixedQueryParams);
    $fixedQueryKeys = array_fill_keys(array_keys($fixedQueryParams), true);
}

$forwardedParams = [];
foreach ($_GET as $key => $value) {
    if (!is_string($key) || !is_string($value)) {
        continue;
    }

    if (
        $key === ''
        || strlen($key) > 128
        || strlen($value) > 2048
        || preg_match('/[\x00-\x1F\x7F]/', $key)
        || preg_match('/[\x00-\x1F\x7F]/', $value)
        || isset($fixedQueryKeys[$key])
    ) {
        continue;
    }

    $forwardedParams[$key] = $value;
}

$affiliateUrl = FORCEVITAL_AFFILIATE_URL;
if ($forwardedParams !== []) {
    $separator = strpos($affiliateUrl, '?') !== false ? '&' : '?';
    $affiliateUrl .= $separator . http_build_query($forwardedParams, '', '&', PHP_QUERY_RFC3986);
}

header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
header('Location: ' . $affiliateUrl, true, 302);
exit;
