<?php
declare(strict_types=1);

const REPELLIO_DESTINATION_URL = 'https://www.sailgeneral.com/375Q8F6Z/23RMXBFM/';

$fixedQueryKeys = [];
$fixedQuery = parse_url(REPELLIO_DESTINATION_URL, PHP_URL_QUERY);
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

$destinationUrl = REPELLIO_DESTINATION_URL;
if ($forwardedParams !== []) {
    $separator = strpos($destinationUrl, '?') !== false ? '&' : '?';
    $destinationUrl .= $separator . http_build_query($forwardedParams, '', '&', PHP_QUERY_RFC3986);
}

header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
header('Location: ' . $destinationUrl, true, 302);
exit;
