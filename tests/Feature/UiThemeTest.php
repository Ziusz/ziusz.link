<?php

use Illuminate\Support\Facades\File;

test('app css configures blue accent and hides number input steppers', function () {
    $css = File::get(resource_path('css/app.css'));

    expect($css)
        ->toContain('--color-accent: var(--color-blue-500);')
        ->toContain('--color-accent-content: var(--color-blue-600);')
        ->toContain('--color-accent-content: var(--color-blue-400);')
        ->toContain("input[type='number']::-webkit-inner-spin-button")
        ->toContain("input[type='number']::-webkit-outer-spin-button")
        ->toContain('-moz-appearance: textfield;');
});
