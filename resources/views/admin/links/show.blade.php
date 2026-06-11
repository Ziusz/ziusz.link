@php
    $name = $link->title ?: $link->slug;
    $logoUrl = $link->resolvedLogoUrl();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $name])
    </head>
    <body class="min-h-dvh bg-zinc-950 font-sans text-zinc-100 antialiased">
        <main class="mx-auto flex min-h-dvh w-full max-w-6xl flex-col gap-6 px-5 py-8 sm:px-8 sm:py-10">
            <header class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-6">
                <div class="flex min-w-0 items-center gap-4">
                    <x-link-logo :name="$name" :url="$logoUrl" size="xl" />

                    <div class="min-w-0">
                        <p class="text-sm font-medium uppercase text-zinc-500">
                            {{ __('Link details') }}
                        </p>

                        <h1 class="mt-1 truncate text-3xl font-semibold text-white">
                            {{ $name }}
                        </h1>

                        <div class="mt-2 flex min-w-0 flex-wrap items-center gap-2 text-xs text-zinc-400">
                            <span class="rounded-md border border-blue-400/30 bg-blue-400/10 px-2 py-0.5 font-medium text-blue-200">
                                {{ ucfirst($link->visibility->value) }}
                            </span>

                            @if ($link->isReachable())
                                <span class="rounded-md border border-emerald-400/30 bg-emerald-400/10 px-2 py-0.5 font-medium text-emerald-200">{{ __('Reachable') }}</span>
                            @elseif ($link->isExpired())
                                <span class="rounded-md border border-amber-400/30 bg-amber-400/10 px-2 py-0.5 font-medium text-amber-200">{{ __('Expired') }}</span>
                            @else
                                <span class="rounded-md border border-zinc-700 bg-zinc-950/70 px-2 py-0.5 font-medium text-zinc-400">{{ __('Inactive') }}</span>
                            @endif

                            <span class="font-mono text-zinc-500">/{{ $link->slug }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.links.edit', $link) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-accent px-4 text-sm font-semibold text-accent-foreground transition hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/60">
                        {{ __('Edit') }}
                    </a>

                    <a href="{{ route('admin.links.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:bg-zinc-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40">
                        {{ __('Back to links') }}
                    </a>
                </div>
            </header>

            @if (session('status'))
                <div class="rounded-md border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <div class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-900/60 ring-1 ring-white/5">
                    <div class="grid gap-5 p-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Destination') }}</p>
                            <p class="mt-2 break-all text-sm text-zinc-200">{{ $link->destination_url }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Short URL') }}</p>
                            <p class="mt-2 break-all font-mono text-sm text-zinc-200">{{ route('links.redirect', $link) }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Platform') }}</p>
                            <p class="mt-2 text-sm text-zinc-200">{{ $link->platform?->name ?: '-' }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium uppercase text-zinc-500">{{ __('Description') }}</p>
                            <p class="mt-2 text-sm leading-6 text-zinc-200">{{ $link->description ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <aside class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-900/60 ring-1 ring-white/5">
                    <dl class="grid divide-y divide-zinc-800">
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-xs font-medium uppercase text-zinc-500">{{ __('Clicks') }}</dt>
                            <dd class="text-sm tabular-nums text-zinc-200">{{ $link->clicks_count }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-xs font-medium uppercase text-zinc-500">{{ __('Last clicked') }}</dt>
                            <dd class="text-right text-sm text-zinc-200">{{ $link->last_clicked_at?->toDayDateTimeString() ?: '-' }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-xs font-medium uppercase text-zinc-500">{{ __('Expires') }}</dt>
                            <dd class="text-right text-sm text-zinc-200">{{ $link->expires_at?->toDayDateTimeString() ?: __('Never') }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-xs font-medium uppercase text-zinc-500">{{ __('Created') }}</dt>
                            <dd class="text-right text-sm text-zinc-200">{{ $link->created_at?->toDayDateTimeString() }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-xs font-medium uppercase text-zinc-500">{{ __('Logo') }}</dt>
                            <dd class="text-right text-sm text-zinc-200">{{ $logoUrl ? __('Stored') : '-' }}</dd>
                        </div>
                    </dl>
                </aside>
            </section>

            <form method="POST" action="{{ route('admin.links.destroy', $link) }}">
                @csrf
                @method('DELETE')

                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md border border-red-400/40 bg-red-950/20 px-4 text-sm font-medium text-red-200 transition hover:border-red-300 hover:text-red-100 focus:outline-none focus:ring-2 focus:ring-red-300/40">
                    {{ __('Delete link') }}
                </button>
            </form>
        </main>
    </body>
</html>
