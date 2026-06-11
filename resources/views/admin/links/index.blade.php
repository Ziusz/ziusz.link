<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Links')])
    </head>
    <body class="min-h-dvh bg-zinc-950 font-sans text-zinc-100 antialiased">
        @php
            $sortLink = function (string $column) use ($sort, $direction): string {
                return request()->fullUrlWithQuery([
                    'sort' => $column,
                    'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc',
                    'page' => null,
                ]);
            };

            $sortOptions = [
                'title' => __('Name'),
                'platform' => __('Platform'),
                'visibility' => __('Type'),
                'is_active' => __('Status'),
                'clicks_count' => __('Clicks'),
                'expires_at' => __('Expires'),
                'created_at' => __('Created'),
            ];
        @endphp

        <main class="mx-auto flex min-h-dvh w-full max-w-6xl flex-col gap-6 px-5 py-8 sm:px-8 sm:py-10">
            <header class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-6">
                <div>
                    <p class="text-sm font-medium uppercase text-zinc-500">
                        {{ __('Admin') }}
                    </p>

                    <h1 class="mt-2 text-3xl font-semibold text-white">
                        {{ __('Links') }}
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.links.create') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-accent px-4 text-sm font-semibold text-accent-foreground transition hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/60">
                        {{ __('New link') }}
                    </a>

                    <a href="{{ route('admin.dashboard') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:bg-zinc-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40">
                        {{ __('Dashboard') }}
                    </a>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf

                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:bg-zinc-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40">
                            {{ __('Log out') }}
                        </button>
                    </form>
                </div>
            </header>

            @if (session('status'))
                <div class="rounded-md border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-3">
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-zinc-800 bg-zinc-900/60 p-3 ring-1 ring-white/5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-1 text-xs font-medium uppercase text-zinc-500">{{ __('Sort') }}</span>

                        @foreach ($sortOptions as $column => $label)
                            <a
                                href="{{ $sortLink($column) }}"
                                @class([
                                    'rounded-md px-2.5 py-1 text-xs font-medium transition focus:outline-none focus:ring-2 focus:ring-blue-500/50',
                                    'bg-accent text-accent-foreground' => $sort === $column,
                                    'border border-zinc-800 bg-zinc-950/70 text-zinc-300 hover:border-blue-500/60 hover:text-white' => $sort !== $column,
                                ])
                            >
                                {{ $label }}
                                @if ($sort === $column)
                                    <span class="ml-1 text-blue-100">{{ $direction === 'asc' ? __('Asc') : __('Desc') }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    <span class="rounded-md border border-zinc-800 bg-zinc-950/70 px-2.5 py-1 text-xs font-medium text-zinc-400">
                        {{ trans_choice(':count link|:count links', $links->total(), ['count' => $links->total()]) }}
                    </span>
                </div>

                @forelse ($links as $link)
                    @php
                        $name = $link->title ?: $link->slug;
                        $logoUrl = $link->resolvedLogoUrl();
                    @endphp

                    <article class="group relative overflow-hidden rounded-lg border border-zinc-800 bg-zinc-900/60 shadow-sm ring-1 ring-white/5 transition hover:border-blue-500/60 hover:bg-zinc-900">
                        <a href="{{ route('admin.links.show', $link) }}" class="absolute inset-0 z-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/60">
                            <span class="sr-only">{{ __('View :name', ['name' => $name]) }}</span>
                        </a>

                        <div class="pointer-events-none relative z-10 grid gap-3 p-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-md border border-white/10 bg-white p-2 shadow-sm shadow-black/20">
                                    @if (filled($logoUrl))
                                        <img src="{{ $logoUrl }}" alt="" class="size-full object-contain" loading="lazy">
                                    @else
                                        <span class="flex size-full items-center justify-center rounded-sm bg-blue-500 text-sm font-bold text-white">
                                            {{ Illuminate\Support\Str::of($name)->substr(0, 1)->upper() }}
                                        </span>
                                    @endif
                                </span>

                                <div class="min-w-0">
                                    <div class="flex min-w-0 flex-wrap items-center gap-2">
                                        <h2 class="max-w-full truncate text-sm font-semibold text-white">
                                            {{ $name }}
                                        </h2>

                                        <span class="rounded-md border border-blue-400/30 bg-blue-400/10 px-2 py-0.5 text-xs font-medium text-blue-200">
                                            {{ ucfirst($link->visibility->value) }}
                                        </span>

                                        @if ($link->isReachable())
                                            <span class="rounded-md border border-emerald-400/30 bg-emerald-400/10 px-2 py-0.5 text-xs font-medium text-emerald-200">{{ __('Reachable') }}</span>
                                        @elseif ($link->isExpired())
                                            <span class="rounded-md border border-amber-400/30 bg-amber-400/10 px-2 py-0.5 text-xs font-medium text-amber-200">{{ __('Expired') }}</span>
                                        @else
                                            <span class="rounded-md border border-zinc-700 bg-zinc-950/70 px-2 py-0.5 text-xs font-medium text-zinc-400">{{ __('Inactive') }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-1 flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-400">
                                        <span class="font-mono text-zinc-500">/{{ $link->slug }}</span>
                                        <span class="truncate">{{ $link->destinationHost() }}</span>
                                        <span>{{ $link->platform?->name ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-2 sm:grid-cols-[1fr_auto] sm:items-center lg:min-w-[27rem]">
                                <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-400 sm:justify-end">
                                    <span class="rounded-md border border-zinc-800 bg-zinc-950/70 px-2.5 py-1 tabular-nums">
                                        {{ trans_choice(':count click|:count clicks', $link->clicks_count, ['count' => $link->clicks_count]) }}
                                    </span>

                                    <span class="rounded-md border border-zinc-800 bg-zinc-950/70 px-2.5 py-1">
                                        {{ __('Expires: :date', ['date' => $link->expires_at?->toFormattedDateString() ?: __('Never')]) }}
                                    </span>

                                    <span class="rounded-md border border-zinc-800 bg-zinc-950/70 px-2.5 py-1">
                                        {{ __('Created: :date', ['date' => $link->created_at?->toFormattedDateString()]) }}
                                    </span>
                                </div>

                                <div class="pointer-events-auto flex flex-wrap items-center gap-2 sm:justify-end">
                                    <a href="{{ route('admin.links.show', $link) }}" class="rounded-md border border-zinc-700 bg-zinc-950/80 px-3 py-1.5 text-xs font-medium text-zinc-200 transition hover:border-blue-500/70 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                                        {{ __('Details') }}
                                    </a>

                                    <a href="{{ route('admin.links.edit', $link) }}" class="rounded-md border border-zinc-700 bg-zinc-950/80 px-3 py-1.5 text-xs font-medium text-zinc-200 transition hover:border-blue-500/70 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                                        {{ __('Edit') }}
                                    </a>

                                    <form method="POST" action="{{ route('admin.links.destroy', $link) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="rounded-md border border-red-400/40 bg-red-950/20 px-3 py-1.5 text-xs font-medium text-red-200 transition hover:border-red-300 hover:text-red-100 focus:outline-none focus:ring-2 focus:ring-red-300/40">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-zinc-700 bg-zinc-900/50 p-8 text-center text-sm text-zinc-400">
                        {{ __('No links yet.') }}
                    </div>
                @endforelse

                @if ($links->hasPages())
                    <div class="rounded-lg border border-zinc-800 bg-zinc-900/60 px-4 py-3">
                        {{ $links->links() }}
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>
