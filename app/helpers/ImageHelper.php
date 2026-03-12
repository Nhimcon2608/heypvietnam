<?php
namespace App\Helpers;

class ImageHelper {
    
    // Maximum file size in bytes (8MB - safer for default MySQL settings)
    const MAX_FILE_SIZE = 8388608;

    // Maximum dimensions (reduced for better compression)
    const MAX_WIDTH = 1920;
    const MAX_HEIGHT = 1920;
    
    // Allowed MIME types
    const ALLOWED_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif'
    ];

    /**
     * Get allowed MIME types based on server capabilities
     */
    public static function getAllowedTypes() {
        $types = self::ALLOWED_TYPES;

        // Always add WebP - we can convert it if not natively supported
        $types[] = 'image/webp';

        return $types;
    }

    /**
     * Convert WebP to JPEG using available methods
     */
    private static function convertWebPToJPEG($webpFile) {
        // For now, we'll provide a clear error message
        // In the future, this could be extended with conversion methods
        throw new \Exception('WebP không được hỗ trợ trực tiếp trên server này. Vui lòng chuyển đổi file sang định dạng JPG, PNG hoặc GIF trước khi upload, hoặc sử dụng trình duyệt hỗ trợ tự động chuyển đổi.');
    }
    
    /**
     * Validate and process uploaded image
     */
    public static function processUploadedImage($file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Lỗi upload file');
        }
        
        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \Exception('File quá lớn. Kích thước tối đa: ' . (self::MAX_FILE_SIZE / 1024 / 1024) . 'MB');
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        // Validate MIME type
        $allowedTypes = self::getAllowedTypes();
        if (!in_array($mimeType, $allowedTypes)) {
            throw new \Exception('Định dạng file không được hỗ trợ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP');
        }
        
        // Get image info
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            throw new \Exception('File không phải là hình ảnh hợp lệ');
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        // Create image resource based on type
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($file['tmp_name']);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($file['tmp_name']);
                } else {
                    // Convert WebP to JPEG using alternative method
                    $image = self::convertWebPToJPEG($file['tmp_name']);
                    $mimeType = 'image/jpeg'; // Change MIME type to JPEG
                }
                break;
            default:
                throw new \Exception('Định dạng hình ảnh không được hỗ trợ');
        }
        
        if (!$image) {
            throw new \Exception('Không thể xử lý hình ảnh');
        }
        
        // Resize if too large
        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            $image = self::resizeImage($image, $width, $height, self::MAX_WIDTH, self::MAX_HEIGHT);
        }
        
        // Compress and get binary data
        $imageData = self::compressImage($image, $mimeType);
        
        // Clean up
        imagedestroy($image);
        
        return [
            'data' => $imageData,
            'mime_type' => $mimeType,
            'size' => strlen($imageData)
        ];
    }
    
    /**
     * Resize image while maintaining aspect ratio
     */
    private static function resizeImage($image, $originalWidth, $originalHeight, $maxWidth, $maxHeight) {
        // Calculate new dimensions
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = intval($originalWidth * $ratio);
        $newHeight = intval($originalHeight * $ratio);
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        
        // Resize
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        return $newImage;
    }
    
    /**
     * Compress image and return binary data
     */
    private static function compressImage($image, $mimeType, $quality = 75) {
        ob_start();

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                // Higher compression for JPEG
                imagejpeg($image, null, $quality);
                break;
            case 'image/png':
                // Maximum PNG compression (0-9, 9 = max compression)
                imagepng($image, null, 9);
                break;
            case 'image/gif':
                imagegif($image);
                break;
            case 'image/webp':
                // Higher compression for WebP
                if (function_exists('imagewebp')) {
                    imagewebp($image, null, $quality);
                } else {
                    // Fallback to JPEG if WebP not supported
                    imagejpeg($image, null, $quality);
                }
                break;
        }

        $imageData = ob_get_contents();
        ob_end_clean();

        // If still too large, try more aggressive compression
        if (strlen($imageData) > 3 * 1024 * 1024) { // 3MB
            ob_start();
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($image, null, 60); // More aggressive
                    break;
                case 'image/webp':
                    if (function_exists('imagewebp')) {
                        imagewebp($image, null, 60);
                    } else {
                        // Fallback to JPEG if WebP not supported
                        imagejpeg($image, null, 60);
                    }
                    break;
                default:
                    // For PNG/GIF, convert to JPEG for better compression
                    imagejpeg($image, null, 60);
                    break;
            }
            $compressedData = ob_get_contents();
            ob_end_clean();

            if (strlen($compressedData) < strlen($imageData)) {
                $imageData = $compressedData;
            }
        }

        return $imageData;
    }
    
    /**
     * Validate image data size before database insert
     */
    public static function validateImageSize($imageData) {
        $size = strlen($imageData);

        // Check if size is within safe MySQL packet limit (conservative approach)
        $maxSize = 4 * 1024 * 1024; // 4MB (safe for default MySQL settings)

        if ($size > $maxSize) {
            throw new \Exception('Hình ảnh sau khi xử lý vẫn quá lớn (' . round($size / 1024 / 1024, 2) . 'MB). Vui lòng chọn hình ảnh nhỏ hơn hoặc cấu hình MySQL để tăng max_allowed_packet.');
        }

        return true;
    }
    
    /**
     * Create thumbnail from image data
     */
    public static function createThumbnail($imageData, $mimeType, $maxWidth = 300, $maxHeight = 300) {
        // Create image from string
        $image = imagecreatefromstring($imageData);
        if (!$image) {
            throw new \Exception('Không thể tạo thumbnail');
        }
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Resize
        $thumbnail = self::resizeImage($image, $width, $height, $maxWidth, $maxHeight);
        
        // Compress
        $thumbnailData = self::compressImage($thumbnail, $mimeType, 80);
        
        // Clean up
        imagedestroy($image);
        imagedestroy($thumbnail);
        
        return $thumbnailData;
    }
}
?>
