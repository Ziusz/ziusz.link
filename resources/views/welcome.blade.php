<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Home')])
    </head>
    <body class="min-h-dvh bg-zinc-950 font-sans text-zinc-100 antialiased">
        <main class="flex min-h-dvh items-center justify-center px-6 py-12">
            <section class="w-full max-w-2xl">
                <p class="text-sm font-medium uppercase text-zinc-500">
                    {{ __('Private links') }}
                </p>

                <h1 class="mt-4 text-4xl font-semibold text-white sm:text-5xl">
                    {{ config('app.name', 'Ziusz Link') }}
                </h1>

                <p class="mt-5 max-w-xl text-base leading-7 text-zinc-300">
                    {{ __('The public link listing and short URLs will live here.') }}
                </p>
            </section>
        </main>
    </body>
</html>
