<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;

class MultipleFileUploader
{
    public function __construct(
        private readonly FileStorage $storage
    )
    {
    }

    /**
     * @param UploadedFile[] $files
     */
    public function upload(
        array  $files,
        string $directory
    ): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->storage->store($file, $directory);
            }
        }

        return $paths;
    }

    /**
     * @param string[] $paths
     */
    public function delete(
        array $paths
    ): void
    {
        foreach ($paths as $path) {
            $this->storage->delete($path);
        }
    }
}
