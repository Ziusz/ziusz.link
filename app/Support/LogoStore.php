<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class LogoStore
{
    private const MaxBytes = 2097152;

    /**
     * @var array<string, string>
     */
    private const Extensions = [
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/svg+xml' => 'svg',
        'image/webp' => 'webp',
    ];

    public function storeRemote(string $directory, string $name, string $url): ?string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        try {
            $response = Http::retry([100, 300])
                ->timeout(10)
                ->connectTimeout(5)
                ->get($url);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $contents = $response->body();
        $extension = $this->extensionFor($response->header('content-type'), $url, $contents);

        if ($extension === null || $contents === '' || strlen($contents) > self::MaxBytes) {
            return null;
        }

        $path = $this->path($directory, $name, $extension);

        Storage::disk('public')->put($path, $contents);

        return $path;
    }

    public function storeUploaded(string $directory, string $name, UploadedFile $file): string
    {
        $extension = Str::lower($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'png');
        $extension = $extension === 'jpeg' ? 'jpg' : $extension;
        $extension = in_array($extension, array_values(self::Extensions), true) ? $extension : 'png';
        $path = $this->path($directory, $name, $extension);

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()) ?: '');

        return $path;
    }

    public static function isStoredPath(?string $path): bool
    {
        return filled($path) && ! Str::startsWith((string) $path, ['http://', 'https://', '/']);
    }

    private function extensionFor(?string $contentType, string $url, string $contents): ?string
    {
        $normalizedContentType = Str::of((string) $contentType)
            ->before(';')
            ->lower()
            ->trim()
            ->value();

        if (isset(self::Extensions[$normalizedContentType])) {
            return self::Extensions[$normalizedContentType];
        }

        $extension = Str::lower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $extension = $extension === 'jpeg' ? 'jpg' : $extension;

        if (in_array($extension, array_values(self::Extensions), true)) {
            return $extension;
        }

        if (Str::startsWith(ltrim($contents), '<svg')) {
            return 'svg';
        }

        return null;
    }

    private function path(string $directory, string $name, string $extension): string
    {
        return trim($directory, '/').'/'.Str::slug($name).'.'.$extension;
    }
}
