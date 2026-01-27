<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;

class SingleFileUploader
{
    public function __construct(
        private readonly FileStorage $storage
    )
    {
    }

    public function upload(
        ?UploadedFile $file,
        string        $directory
    ): ?string
    {
        if (!$file) {
            return null;
        }

        return $this->storage->store($file, $directory);
    }

    public function replace(
        ?string       $oldPath,
        ?UploadedFile $newFile,
        string        $directory
    ): ?string
    {
        if (!$newFile) {
            return $oldPath;
        }

        $this->storage->delete($oldPath);

        return $this->storage->store($newFile, $directory);
    }
}
