<?php

namespace App\Http\Controllers;

use App\Link;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $links = Link::query()
            ->featured()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('home', [
            'links' => $links,
        ]);
    }
}
