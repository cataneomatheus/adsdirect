<?php
// Run with: php -d extension=gd scripts/convert-metabrew-images.php
foreach (['metabew-mobile', 'metabew-tablet', 'metabew'] as $name) {
    $source = __DIR__ . '/../assets/img/' . $name . '.jpg';
    $target = __DIR__ . '/../assets/img/' . $name . '.webp';
    $image = imagecreatefromjpeg($source);
    if (!$image || !imagewebp($image, $target, 80)) {
        throw new RuntimeException('Conversion failed: ' . $name);
    }
    $check = getimagesize($target);
    if (!$check || $check[0] !== imagesx($image) || $check[1] !== imagesy($image)) {
        throw new RuntimeException('Invalid output: ' . $name);
    }
    printf("%s: %d -> %d bytes (%.1f%% smaller)\n", $name, filesize($source), filesize($target), 100 * (1 - filesize($target) / filesize($source)));
    imagedestroy($image);
}
