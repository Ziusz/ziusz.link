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
        @endphp

        <main class="mx-auto flex min-h-dvh w-full max-w-7xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10">
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
                    <a href="{{ route('admin.links.create') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-cyan-300 px-4 text-sm font-semibold text-zinc-950 transition hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/70">
                        {{ __('New link') }}
                    </a>

                    <a href="{{ route('admin.dashboard') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40">
                        {{ __('Dashboard') }}
                    </a>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf

                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40">
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

            <section class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-900/50">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-800 text-sm">
                        <thead class="bg-zinc-900">
                            <tr class="text-left text-xs font-medium uppercase text-zinc-500">
                                <th class="px-4 py-3"><a href="{{ $sortLink('title') }}" class="hover:text-zinc-200">{{ __('Name') }}</a></th>
                                <th class="px-4 py-3"><a href="{{ $sortLink('slug') }}" class="hover:text-zinc-200">{{ __('Slug') }}</a></th>
                                <th class="px-4 py-3"><a href="{{ $sortLink('platform') }}" class="hover:text-zinc-200">{{ __('Platform') }}</a></th>
                                <th class="px-4 py-3"><a href="{{ $sortLink('visibility') }}" class="hover:text-zinc-200">{{ __('Type') }}</a></th>
                                <th class="px-4 py-3"><a href="{{ $sortLink('is_active') }}" class="hover:text-zinc-200">{{ __('Status') }}</a></th>
                                <th class="px-4 py-3"><a href="{{ $sortLink('clicks_count') }}" class="hover:text-zinc-200">{{ __('Clicks') }}</a></th>
                                <th class="px-4 py-3"><a href="{{ $sortLink('expires_at') }}" class="hover:text-zinc-200">{{ __('Expires') }}</a></th>
                                <th class="px-4 py-3"><a href="{{ $sortLink('created_at') }}" class="hover:text-zinc-200">{{ __('Created') }}</a></th>
                                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-800">
                            @forelse ($links as $link)
                                <tr class="text-zinc-300">
                                    <td class="max-w-60 px-4 py-4">
                                        <div class="truncate font-medium text-white">{{ $link->title ?: $link->slug }}</div>
                                        <div class="truncate text-xs text-zinc-500">{{ $link->destinationHost() }}</div>
                                    </td>
                                    <td class="px-4 py-4 font-mono text-xs text-zinc-400">{{ $link->slug }}</td>
                                    <td class="px-4 py-4">{{ $link->platform?->name ?: '—' }}</td>
                                    <td class="px-4 py-4">{{ ucfirst($link->visibility->value) }}</td>
                                    <td class="px-4 py-4">
                                        @if ($link->isReachable())
                                            <span class="text-emerald-300">{{ __('Reachable') }}</span>
                                        @elseif ($link->isExpired())
                                            <span class="text-amber-300">{{ __('Expired') }}</span>
                                        @else
                                            <span class="text-zinc-500">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 tabular-nums">{{ $link->clicks_count }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $link->expires_at?->toFormattedDateString() ?: __('Never') }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $link->created_at?->toFormattedDateString() }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.links.show', $link) }}" class="rounded-md border border-zinc-700 px-3 py-1.5 text-xs font-medium text-zinc-200 transition hover:border-zinc-500 hover:text-white">
                                                {{ __('Details') }}
                                            </a>

                                            <a href="{{ route('admin.links.edit', $link) }}" class="rounded-md border border-zinc-700 px-3 py-1.5 text-xs font-medium text-zinc-200 transition hover:border-zinc-500 hover:text-white">
                                                {{ __('Edit') }}
                                            </a>

                                            <form method="POST" action="{{ route('admin.links.destroy', $link) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="rounded-md border border-red-400/40 px-3 py-1.5 text-xs font-medium text-red-200 transition hover:border-red-300 hover:text-red-100">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-10 text-center text-sm text-zinc-400">
                                        {{ __('No links yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($links->hasPages())
                    <div class="border-t border-zinc-800 px-4 py-3">
                        {{ $links->links() }}
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>
