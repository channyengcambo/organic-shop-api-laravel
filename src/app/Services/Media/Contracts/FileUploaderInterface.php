<?php

namespace App\Services\Media\Contracts;

use Illuminate\Http\UploadedFile;

interface FileUploaderInterface
{
    public function upload(UploadedFile|array $files, string $directory): string|array;
}
