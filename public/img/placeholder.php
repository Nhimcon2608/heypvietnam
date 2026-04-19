<?php
// Tạo hình ảnh placeholder cho trang About
header('Content-Type: image/jpeg');

$width = isset($_GET['w']) ? (int)$_GET['w'] : 600;
$height = isset($_GET['h']) ? (int)$_GET['h'] : 400;
$text = isset($_GET['text']) ? $_GET['text'] : 'Placeholder Image';

// Tạo canvas
$image = imagecreate($width, $height);

// Định nghĩa màu sắc theo theme sage green
$bg_color = imagecolorallocate($image, 125, 132, 113); // #7d8471
$text_color = imagecolorallocate($image, 255, 255, 255);
$border_color = imagecolorallocate($image, 200, 199, 196); // #c8c7c4

// Vẽ background
imagefill($image, 0, 0, $bg_color);

// Vẽ border
imagerectangle($image, 0, 0, $width-1, $height-1, $border_color);

// Thêm text
$font_size = min($width, $height) / 20;
$text_box = imagettfbbox($font_size, 0, __DIR__ . '/../../app/assets/fonts/arial.ttf', $text);

if (!$text_box) {
    // Fallback nếu không có font
    $x = ($width - strlen($text) * 10) / 2;
    $y = $height / 2;
    imagestring($image, 5, $x, $y, $text, $text_color);
} else {
    $text_width = $text_box[4] - $text_box[0];
    $text_height = $text_box[1] - $text_box[5];
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2 + $text_height;
    imagettftext($image, $font_size, 0, $x, $y, $text_color, __DIR__ . '/../../app/assets/fonts/arial.ttf', $text);
}

// Output image
imagejpeg($image, null, 90);
imagedestroy($image);
?>
