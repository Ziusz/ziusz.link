<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Admin')])
    </head>
    <body class="min-h-dvh bg-zinc-950 font-sans text-zinc-100 antialiased">
        <main class="mx-auto flex min-h-dvh w-full max-w-5xl flex-col gap-8 px-6 py-10">
            <header class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium uppercase text-zinc-500">
                        {{ __('Admin') }}
                    </p>

                    <h1 class="mt-2 text-3xl font-semibold text-white">
                        {{ __('Link manager') }}
                    </h1>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40"
                    >
                        {{ __('Log out') }}
                    </button>
                </form>
            </header>

            <section class="rounded-md border border-zinc-800 bg-zinc-900/50 p-6">
                <p class="text-sm leading-6 text-zinc-300">
                    {{ __('The private link administration panel will live here.') }}
                </p>
            </section>
        </main>
    </body>
</html>
