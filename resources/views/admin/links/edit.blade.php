<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Edit link')])
    </head>
    <body class="min-h-dvh bg-zinc-950 font-sans text-zinc-100 antialiased">
        <main class="mx-auto flex min-h-dvh w-full max-w-5xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10">
            <header class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-6">
                <div class="min-w-0">
                    <p class="text-sm font-medium uppercase text-zinc-500">{{ __('Admin') }}</p>
                    <h1 class="mt-2 truncate text-3xl font-semibold text-white">{{ __('Edit link') }}</h1>
                </div>

                <a href="{{ route('admin.links.show', $link) }}" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40">
                    {{ __('Back to details') }}
                </a>
            </header>

            @include('admin.links.partials.form', [
                'action' => route('admin.links.update', $link),
                'method' => 'PUT',
                'submitLabel' => __('Save changes'),
            ])
        </main>
    </body>
</html>
