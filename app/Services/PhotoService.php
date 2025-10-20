<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PhotoService
{
    private ImageManager $imageManager;
    private string $disk = 'public';
    private int $thumbnailMaxWidth = 400;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Process and save photo with thumbnail
     */
    public function processPhoto(UploadedFile $file, int $displayId): array
    {
        // Create directory for this display
        $displayDir = "displays/{$displayId}";
        
        // Generate unique filenames
        $timestamp = now()->format('YmdHis');
        $originalFilename = "photo_{$timestamp}.jpg";
        $thumbFilename = "photo_thumb_{$timestamp}.jpg";
        
        // Process original image
        $originalPath = $this->processOriginalImage($file, $displayDir, $originalFilename);
        
        // Generate thumbnail
        $thumbPath = $this->generateThumbnail($file, $displayDir, $thumbFilename);
        
        return [
            'photo_path' => $originalPath,
            'photo_thumb_path' => $thumbPath,
        ];
    }

    /**
     * Process original image (convert to JPG, optimize)
     */
    private function processOriginalImage(UploadedFile $file, string $displayDir, string $filename): string
    {
        $image = $this->imageManager->read($file);
        
        // Convert to JPG and optimize
        $image->toJpeg(85); // 85% quality for good balance
        
        $fullPath = "{$displayDir}/{$filename}";
        
        // Save to storage
        Storage::disk($this->disk)->put($fullPath, $image->encode());
        
        return $fullPath;
    }

    /**
     * Generate thumbnail (max 400px width, maintain aspect ratio)
     */
    private function generateThumbnail(UploadedFile $file, string $displayDir, string $filename): string
    {
        $image = $this->imageManager->read($file);
        
        // Resize maintaining aspect ratio
        $image->scaleDown(width: $this->thumbnailMaxWidth);
        
        // Convert to JPG
        $image->toJpeg(80); // Slightly lower quality for thumbnail
        
        $fullPath = "{$displayDir}/{$filename}";
        
        // Save to storage
        Storage::disk($this->disk)->put($fullPath, $image->encode());
        
        return $fullPath;
    }

    /**
     * Delete photo files from storage
     */
    public function deletePhotoFiles(?string $photoPath, ?string $thumbPath): void
    {
        if ($photoPath && Storage::disk($this->disk)->exists($photoPath)) {
            Storage::disk($this->disk)->delete($photoPath);
        }
        
        if ($thumbPath && Storage::disk($this->disk)->exists($thumbPath)) {
            Storage::disk($this->disk)->delete($thumbPath);
        }
    }

    /**
     * Get public URL for photo
     */
    public function getPhotoUrl(?string $photoPath): ?string
    {
        if (!$photoPath) {
            return null;
        }
        
        return Storage::disk($this->disk)->url($photoPath);
    }

    /**
     * Get public URL for thumbnail
     */
    public function getThumbnailUrl(?string $thumbPath): ?string
    {
        if (!$thumbPath) {
            return null;
        }
        
        return Storage::disk($this->disk)->url($thumbPath);
    }

    /**
     * Validate photo file
     */
    public function validatePhoto(UploadedFile $file): array
    {
        $errors = [];
        
        // Check file size (5MB max)
        if ($file->getSize() > 5 * 1024 * 1024) {
            $errors[] = 'El archivo no puede ser mayor a 5MB.';
        }
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $errors[] = 'El archivo debe ser una imagen (JPG, PNG o WEBP).';
        }
        
        return $errors;
    }
}
