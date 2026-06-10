<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $link->title ?: $link->slug])
    </head>
    <body class="min-h-dvh bg-zinc-950 font-sans text-zinc-100 antialiased">
        <main class="mx-auto flex min-h-dvh w-full max-w-5xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10">
            <header class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-6">
                <div class="min-w-0">
                    <p class="text-sm font-medium uppercase text-zinc-500">
                        {{ __('Link details') }}
                    </p>

                    <h1 class="mt-2 truncate text-3xl font-semibold text-white">
                        {{ $link->title ?: $link->slug }}
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.links.edit', $link) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-cyan-300 px-4 text-sm font-semibold text-zinc-950 transition hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/70">
                        {{ __('Edit') }}
                    </a>

                    <a href="{{ route('admin.links.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40">
                        {{ __('Back to links') }}
                    </a>
                </div>
            </header>

            <section class="grid gap-4 rounded-lg border border-zinc-800 bg-zinc-900/50 p-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Destination') }}</p>
                    <p class="mt-2 break-all text-sm text-zinc-200">{{ $link->destination_url }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Short URL') }}</p>
                    <p class="mt-2 break-all font-mono text-sm text-zinc-200">{{ route('links.redirect', $link) }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Platform') }}</p>
                    <p class="mt-2 text-sm text-zinc-200">{{ $link->platform?->name ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Logo') }}</p>
                    <p class="mt-2 break-all text-sm text-zinc-200">{{ $link->resolvedLogoUrl() ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Type') }}</p>
                    <p class="mt-2 text-sm text-zinc-200">{{ ucfirst($link->visibility->value) }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Reachability') }}</p>
                    <p class="mt-2 text-sm text-zinc-200">{{ $link->isReachable() ? __('Reachable') : __('Not reachable') }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Clicks') }}</p>
                    <p class="mt-2 text-sm tabular-nums text-zinc-200">{{ $link->clicks_count }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Last clicked') }}</p>
                    <p class="mt-2 text-sm text-zinc-200">{{ $link->last_clicked_at?->toDayDateTimeString() ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Expires') }}</p>
                    <p class="mt-2 text-sm text-zinc-200">{{ $link->expires_at?->toDayDateTimeString() ?: __('Never') }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Created') }}</p>
                    <p class="mt-2 text-sm text-zinc-200">{{ $link->created_at?->toDayDateTimeString() }}</p>
                </div>

                <div class="sm:col-span-2">
                    <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Description') }}</p>
                    <p class="mt-2 text-sm leading-6 text-zinc-200">{{ $link->description ?: '—' }}</p>
                </div>
            </section>

            <form method="POST" action="{{ route('admin.links.destroy', $link) }}">
                @csrf
                @method('DELETE')

                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md border border-red-400/40 px-4 text-sm font-medium text-red-200 transition hover:border-red-300 hover:text-red-100 focus:outline-none focus:ring-2 focus:ring-red-300/40">
                    {{ __('Delete link') }}
                </button>
            </form>
        </main>
    </body>
</html>
