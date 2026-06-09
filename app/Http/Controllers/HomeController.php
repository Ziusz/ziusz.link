<?php

namespace App\Http\Controllers;

use App\Link;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $links = Link::query()
            ->where('is_active', true)
            ->where('is_listed', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('home', [
            'links' => $links,
        ]);
    }
}
