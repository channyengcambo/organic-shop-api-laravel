<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorage
{
    public function store(
        UploadedFile $file,
        string       $directory,
        string       $disk = 'public'
    ): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, $disk);
    }

    public function delete(
        ?string $path,
        string  $disk = 'public'
    ): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
