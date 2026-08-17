<?php

$input_path = "C:\\Users\\ACER\\.gemini\\antigravity-ide\\brain\\b33bcf2c-a2a3-482b-855d-bf73c7fbc6d4\\.user_uploaded\\media_1786873896923.jpg";
$output_dir = __DIR__ . '/images';

if (!is_dir($output_dir)) {
    mkdir($output_dir, 0755, true);
}

if (!file_exists($input_path)) {
    die("Input file not found: " . $input_path);
}

$src = imagecreatefromjpeg($input_path);
if (!$src) {
    die("Failed to open JPEG");
}

$orig_w = imagesx($src);
$orig_h = imagesy($src);

// Crop upper 67% to exclude text
$crop_h = (int)($orig_h * 0.67);
$cropped = imagecreatetruecolor($orig_w, $crop_h);
imagecopy($cropped, $src, 0, 0, 0, 0, $orig_w, $crop_h);

// Find bounding box of dark pixels
$min_x = $orig_w;
$min_y = $crop_h;
$max_x = 0;
$max_y = 0;

for ($y = 0; $y < $crop_h; $y++) {
    for ($x = 0; $x < $orig_w; $x++) {
        $rgb = imagecolorat($cropped, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $gray = (int)(($r + $g + $b) / 3);

        if ($gray < 220) { // Dark pixel
            if ($x < $min_x) $min_x = $x;
            if ($x > $max_x) $max_x = $x;
            if ($y < $min_y) $min_y = $y;
            if ($y > $max_y) $max_y = $y;
        }
    }
}

// Add padding
$pad = 12;
$min_x = max(0, $min_x - $pad);
$min_y = max(0, $min_y - $pad);
$max_x = min($orig_w - 1, $max_x + $pad);
$max_y = min($crop_h - 1, $max_y + $pad);

$box_w = $max_x - $min_x + 1;
$box_h = $max_y - $min_y + 1;
$dim = max($box_w, $box_h);

// Create transparent square canvases
function generateLogo($cropped, $min_x, $min_y, $box_w, $box_h, $dim, $isWhite) {
    $dst = imagecreatetruecolor($dim, $dim);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $dim, $dim, $transparent);

    $offset_x = (int)(($dim - $box_w) / 2);
    $offset_y = (int)(($dim - $box_h) / 2);

    for ($y = 0; $y < $box_h; $y++) {
        for ($x = 0; $x < $box_w; $x++) {
            $rgb = imagecolorat($cropped, $min_x + $x, $min_y + $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $gray = ($r + $g + $b) / 3;

            // In GD alpha: 0 is opaque, 127 is completely transparent
            if ($gray >= 240) {
                $alpha = 127; // Transparent
            } elseif ($gray <= 40) {
                $alpha = 0;   // Fully Opaque
            } else {
                $factor = ($gray - 40) / 200.0;
                $alpha = (int)($factor * 127);
            }

            if ($alpha < 127) {
                if ($isWhite) {
                    $color = imagecolorallocatealpha($dst, 255, 255, 255, $alpha);
                } else {
                    $color = imagecolorallocatealpha($dst, 15, 15, 15, $alpha);
                }
                imagesetpixel($dst, $offset_x + $x, $offset_y + $y, $color);
            }
        }
    }
    return $dst;
}

$logo_black = generateLogo($cropped, $min_x, $min_y, $box_w, $box_h, $dim, false);
$logo_white = generateLogo($cropped, $min_x, $min_y, $box_w, $box_h, $dim, true);

imagepng($logo_black, $output_dir . '/logo-black.png', 9);
imagepng($logo_white, $output_dir . '/logo-white.png', 9);
imagepng($logo_black, $output_dir . '/logo.png', 9);

// Copy to root public if exists
$root_pub = dirname(__DIR__, 2) . '/public/images';
if (!is_dir($root_pub)) {
    @mkdir($root_pub, 0755, true);
}
@imagepng($logo_black, $root_pub . '/logo-black.png', 9);
@imagepng($logo_white, $root_pub . '/logo-white.png', 9);
@imagepng($logo_black, $root_pub . '/logo.png', 9);

echo "Logos generated successfully in " . $output_dir . "\n";
