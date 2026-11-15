<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    private const IMG = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    /** Public image (listing photo). Real MIME is sniffed from the file bytes, not the client's claim. */
    public function storePublic(UploadedFile $f, string $dir = 'listings'): ?string
    {
        $ext = self::IMG[$f->getMimeType()] ?? null;
        if (!$ext || $f->getSize() > 3 * 1024 * 1024) return null;
        $name = $dir.'/'.Str::random(24).'.'.$ext;
        Storage::disk('public')->put($name, file_get_contents($f->getRealPath()));
        return $name;
    }
    /** Private document (certificate / licence): image or PDF, stored off the web root. */
    public function storePrivate(UploadedFile $f): ?string
    {
        $mime = $f->getMimeType();
        $ext = self::IMG[$mime] ?? ($mime === 'application/pdf' ? 'pdf' : null);
        if (!$ext || $f->getSize() > 4 * 1024 * 1024) return null;
        $name = 'docs/'.Str::random(24).'.'.$ext;
        Storage::disk('private')->put($name, file_get_contents($f->getRealPath()));
        return $name;
    }
}
