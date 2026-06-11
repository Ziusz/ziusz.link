<?php

use App\Support\AdminAccess;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('Admin login')] class extends Component
{
    public string $password = '';

    private AdminAccess $adminAccess;

    public function boot(AdminAccess $adminAccess): void
    {
        $this->adminAccess = $adminAccess;
    }

    public function mount(): mixed
    {
        if ($this->adminAccess->check(request())) {
            return redirect()->route('admin.dashboard');
        }

        return null;
    }

    public function unlock(): mixed
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited();

        if (! $this->adminAccess->authenticate(request(), $this->password)) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'password' => 'The admin password is incorrect.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        $this->reset('password');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function isConfigured(): bool
    {
        return $this->adminAccess->isConfigured();
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'password' => "Too many unlock attempts. Try again in {$seconds} seconds.",
        ]);
    }

    private function throttleKey(): string
    {
        return 'admin-login:'.request()->ip();
    }
};
?>

<div class="flex min-h-dvh items-center justify-center bg-zinc-950 px-6 py-12 text-zinc-100">
    <main class="w-full max-w-md">
        <div class="mb-8">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-md bg-accent text-sm font-bold text-accent-foreground">
                    ZL
                </div>

                <div>
                    <p class="text-sm font-medium text-blue-300">{{ config('app.name', 'Ziusz Link') }}</p>
                    <h1 class="text-2xl font-semibold text-white">{{ __('Admin login') }}</h1>
                </div>
            </div>

            <p class="text-sm leading-6 text-zinc-400">
                {{ __('Private link administration panel.') }}
            </p>
        </div>

        @if (! $this->isConfigured())
            <div class="mb-6 rounded-md border border-amber-400/30 bg-amber-400/10 px-4 py-3 text-sm text-amber-100">
                {{ __('Admin password is not configured.') }}
            </div>
        @endif

        <form wire:submit="unlock" class="space-y-5">
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-zinc-200">{{ __('Password') }}</label>

                <input
                    id="password"
                    type="password"
                    wire:model="password"
                    autocomplete="current-password"
                    autofocus
                    @disabled(! $this->isConfigured())
                    class="block h-11 w-full rounded-md border border-zinc-700 bg-zinc-900 px-3 text-base text-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-60"
                >

                @error('password')
                    <p class="text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                @disabled(! $this->isConfigured())
                class="inline-flex h-11 w-full items-center justify-center rounded-md bg-accent px-4 text-sm font-semibold text-accent-foreground transition hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40 disabled:cursor-not-allowed disabled:bg-zinc-700 disabled:text-zinc-400"
            >
                <span wire:loading.remove wire:target="unlock">{{ __('Unlock') }}</span>
                <span wire:loading wire:target="unlock">{{ __('Checking') }}</span>
            </button>
        </form>
    </main>
</div>
