<?php

namespace App\Http\Controllers;

use App\Link;
use Illuminate\Http\RedirectResponse;

class LinkRedirectController extends Controller
{
    public function __invoke(Link $link): RedirectResponse
    {
        abort_unless($link->is_active, 404);

        $link->increment('clicks_count', 1, [
            'last_clicked_at' => now(),
        ]);

        return redirect()->away($link->destination_url);
    }
}
