<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Home')])
    </head>
    <body class="min-h-dvh bg-zinc-950 font-sans text-zinc-100 antialiased">
        <main class="mx-auto flex min-h-dvh w-full max-w-4xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10">
            <header class="border-b border-white/10 pb-6">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                    <img src="/favicon.svg" alt="" class="size-9 shrink-0 rounded-lg bg-white p-1.5">

                    <span class="min-w-0">
                        <span class="block truncate text-lg font-semibold text-white">
                            {{ config('app.name', 'Ziusz Link') }}
                        </span>

                        <span class="block text-sm text-zinc-400">
                            {{ __('Profiles') }}
                        </span>
                    </span>
                </a>
            </header>

            <section class="grid gap-3">
                @forelse ($links as $link)
                    @php
                        $logoUrl = $link->resolvedLogoUrl();
                    @endphp

                    <a href="{{ route('links.redirect', $link) }}" class="group rounded-lg border border-white/10 bg-white/[0.03] p-4 transition hover:border-cyan-300/60 hover:bg-white/[0.06] focus:outline-none focus:ring-2 focus:ring-cyan-300/70">
                        <span class="flex items-start gap-4">
                            <x-link-logo :name="$link->title ?: $link->slug" :url="$logoUrl" size="lg" />

                            <span class="grid min-w-0 flex-1 gap-2">
                                <span class="truncate text-base font-semibold text-white">
                                    {{ $link->title ?: $link->slug }}
                                </span>

                                <span class="truncate text-sm text-zinc-400">
                                    {{ $link->destinationHost() }}
                                </span>

                                @if (filled($link->description))
                                    <span class="text-sm leading-6 text-zinc-300">
                                        {{ $link->description }}
                                    </span>
                                @endif
                            </span>
                        </span>
                    </a>
                @empty
                    <div class="rounded-lg border border-dashed border-white/15 bg-white/[0.03] p-8 text-center">
                        <p class="text-sm font-medium text-zinc-300">
                            {{ __('No links here yet.') }}
                        </p>
                    </div>
                @endforelse
            </section>
        </main>
    </body>
</html>
