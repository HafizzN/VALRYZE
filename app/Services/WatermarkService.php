<?php

namespace App\Services;

use Intervention\Image\ImageManager;

class WatermarkService
{
    /**
     * Apply watermark text to attendance selfie image using GD.
     *
     * @param string $imagePath     Absolute path to source image
     * @param array  $watermarkData Array of text lines to overlay
     * @return string               Path to watermarked image (saved)
     */
    public function applyWatermark(string $imagePath, array $watermarkData): string
    {
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        // Load image using GD
        $source = match($ext) {
            'jpg', 'jpeg' => imagecreatefromjpeg($imagePath),
            'png'         => imagecreatefrompng($imagePath),
            'webp'        => imagecreatefromwebp($imagePath),
            default       => imagecreatefromjpeg($imagePath),
        };

        if (!$source) {
            return $imagePath; // return original if loading fails
        }

        $width  = imagesx($source);
        $height = imagesy($source);

        // Create semi-transparent overlay at bottom
        $overlayHeight = 160;
        $overlay = imagecreatetruecolor($width, $overlayHeight);
        imagealphablending($overlay, true);
        $black = imagecolorallocatealpha($overlay, 0, 0, 0, 60);
        imagefill($overlay, 0, 0, $black);

        // Merge overlay onto source image at bottom
        imagecopymerge($source, $overlay, 0, $height - $overlayHeight, 0, 0, $width, $overlayHeight, 70);
        imagedestroy($overlay);

        // Set text color
        $white = imagecolorallocate($source, 255, 255, 255);
        $yellow = imagecolorallocate($source, 255, 215, 0);

        // Write watermark lines
        $fontPath = base_path('resources/fonts/DejaVuSans.ttf');
        $fontSize = 14;
        $x = 15;
        $y = $height - $overlayHeight + 20;

        foreach ($watermarkData as $i => $line) {
            $color = $i === 0 ? $yellow : $white;
            if (file_exists($fontPath)) {
                imagettftext($source, $fontSize, 0, $x, $y, $color, $fontPath, $line);
            } else {
                imagestring($source, 3, $x, $y, $line, $color);
            }
            $y += 22;
        }

        // Add company logo watermark (top right corner)
        $companyName = config('app.name', 'Smart HR Portal');
        $logoColor = imagecolorallocatealpha($source, 255, 255, 255, 80);
        if (file_exists($fontPath)) {
            imagettftext($source, 11, 0, $width - 220, 25, $logoColor, $fontPath, $companyName);
        }

        // Save watermarked image
        $outputPath = str_replace('.' . $ext, '_wm.' . $ext, $imagePath);
        match($ext) {
            'png'  => imagepng($source, $outputPath),
            'webp' => imagewebp($source, $outputPath, 90),
            default => imagejpeg($source, $outputPath, 90),
        };

        imagedestroy($source);
        return $outputPath;
    }

    /**
     * Build standard watermark data for attendance photo.
     */
    public function buildAttendanceWatermarkData(
        string $employeeName,
        string $date,
        string $time,
        float $latitude,
        float $longitude,
        int $distance
    ): array {
        return [
            config('app.name', 'Smart HR Portal'),
            'Karyawan : ' . $employeeName,
            'Tanggal  : ' . $date,
            'Waktu    : ' . $time . ' WIB',
            'Lat/Long : ' . number_format($latitude, 7) . ', ' . number_format($longitude, 7),
            'Jarak    : ' . $distance . ' meter dari kantor',
        ];
    }
}
