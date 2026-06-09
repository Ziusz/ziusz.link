<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Admin login')])
    </head>
    <body class="min-h-dvh bg-zinc-950 font-sans text-zinc-100 antialiased">
        <main class="flex min-h-dvh items-center justify-center px-6 py-12">
            <section class="w-full max-w-md">
                <h1 class="text-2xl font-semibold text-white">
                    {{ __('Admin login') }}
                </h1>

                <p class="mt-3 text-sm leading-6 text-zinc-400">
                    {{ __('Admin access will be unlocked here.') }}
                </p>
            </section>
        </main>
    </body>
</html>
