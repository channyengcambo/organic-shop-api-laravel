<?php

namespace App\Services\Media;

use App\Services\Media\Contracts\FileUploaderInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use finfo;

class FileUploaderService implements FileUploaderInterface
{
    protected array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    protected int $maxSize = 20 * 1024 * 1024; // 20MB

    public function upload(UploadedFile|array $files, string $directory): string|array
    {
        if (is_array($files)) {
            return array_map(
                fn($file) => $this->uploadSingle($file, $directory),
                $files
            );
        }

        return $this->uploadSingle($files, $directory);
    }

    private function uploadSingle(UploadedFile $file, string $directory): string
    {
        $this->validateFile($file);

        $filename = Str::uuid() . '.' . $file->guessExtension();

        return $file->storeAs($directory, $filename, 'public');
    }

    private function validateFile(UploadedFile $file): void
    {
        // Size check
        if ($file->getSize() > $this->maxSize) {
            throw new \RuntimeException('File size exceeds limit');
        }

        // Real MIME detection (Apache Tika equivalent)
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file->getPathname());

        if (!in_array($mime, $this->allowedMimes, true)) {
            throw new \RuntimeException('Invalid file content');
        }
    }
}
