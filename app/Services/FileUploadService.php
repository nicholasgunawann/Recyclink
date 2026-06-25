<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    // ponytail: simple helper to store uploaded files
    public function upload(UploadedFile $file, string $directory = 'uploads'): string
    {
        return $file->store($directory, 'public');
    }
}
