<?php

namespace App\Http\Controllers;

use App\Link;
use App\Platform;
use App\Support\LogoStore;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogoController extends Controller
{
    public function platform(Platform $platform): BinaryFileResponse
    {
        return $this->logoResponse($platform->logo_url);
    }

    public function link(Link $link): BinaryFileResponse
    {
        return $this->logoResponse($link->logo_url);
    }

    private function logoResponse(?string $path): BinaryFileResponse
    {
        abort_unless(LogoStore::isStoredPath($path), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path));
    }
}
