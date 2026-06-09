<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Hash;

class AdminAccess
{
    public function __construct(private SessionManager $sessions) {}

    public function isConfigured(): bool
    {
        return filled($this->passwordHash());
    }

    public function authenticate(Request $request, string $password): bool
    {
        if (! $this->validatePassword($password)) {
            return false;
        }

        $session = $this->session($request);

        $session->regenerate();
        $session->put($this->sessionKey(), true);
        $this->confirmPassword($request);

        return true;
    }

    public function check(Request $request): bool
    {
        $session = $this->session($request);

        return $this->isConfigured()
            && $session->get($this->sessionKey()) === true;
    }

    public function confirmPassword(Request $request): void
    {
        $this->session($request)->put($this->confirmedAtKey(), now()->getTimestamp());
    }

    public function passwordRecentlyConfirmed(Request $request): bool
    {
        $session = $this->session($request);

        $confirmedAt = $session->get($this->confirmedAtKey());

        if (! is_int($confirmedAt)) {
            return false;
        }

        return $confirmedAt >= now()->subSeconds($this->passwordTimeout())->getTimestamp();
    }

    public function logout(Request $request): void
    {
        $session = $this->session($request);

        $session->invalidate();
        $session->regenerateToken();
    }

    public function sessionKey(): string
    {
        return (string) config('admin.session_key', 'admin.authenticated');
    }

    public function confirmedAtKey(): string
    {
        return (string) config('admin.confirmed_at_key', 'admin.password_confirmed_at');
    }

    private function validatePassword(string $password): bool
    {
        $passwordHash = $this->passwordHash();

        return $passwordHash !== null && Hash::check($password, $passwordHash);
    }

    private function passwordHash(): ?string
    {
        $passwordHash = config('admin.password_hash');

        if (! is_string($passwordHash) || $passwordHash === '') {
            return null;
        }

        return $passwordHash;
    }

    private function passwordTimeout(): int
    {
        return (int) config('admin.password_timeout', 10800);
    }

    private function session(Request $request): Store
    {
        if ($request->hasSession()) {
            return $request->session();
        }

        return $this->sessions->driver();
    }
}
