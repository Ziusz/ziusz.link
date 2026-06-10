<?php

namespace Database\Seeders;

use App\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->platforms() as $platform) {
            $platform['logo_url'] = $this->localLogoUrl($platform['slug'], $platform['logo_source_url']);

            unset($platform['logo_source_url']);

            Platform::query()->updateOrCreate(
                ['slug' => $platform['slug']],
                $platform,
            );
        }
    }

    /**
     * @return array<int, array{slug: string, name: string, domain: string|null, logo_source_url: string|null}>
     */
    private function platforms(): array
    {
        return [
            ['slug' => 'github', 'name' => 'GitHub', 'domain' => 'github.com', 'logo_source_url' => 'https://cdn.simpleicons.org/github'],
            ['slug' => 'x', 'name' => 'X', 'domain' => 'x.com', 'logo_source_url' => 'https://cdn.simpleicons.org/x'],
            ['slug' => 'instagram', 'name' => 'Instagram', 'domain' => 'instagram.com', 'logo_source_url' => 'https://cdn.simpleicons.org/instagram'],
            ['slug' => 'youtube', 'name' => 'YouTube', 'domain' => 'youtube.com', 'logo_source_url' => 'https://cdn.simpleicons.org/youtube'],
            ['slug' => 'linkedin', 'name' => 'LinkedIn', 'domain' => 'linkedin.com', 'logo_source_url' => 'https://cdn.simpleicons.org/linkedin'],
            ['slug' => 'bluesky', 'name' => 'Bluesky', 'domain' => 'bsky.app', 'logo_source_url' => 'https://cdn.simpleicons.org/bluesky'],
            ['slug' => 'mastodon', 'name' => 'Mastodon', 'domain' => 'mastodon.social', 'logo_source_url' => 'https://cdn.simpleicons.org/mastodon'],
            ['slug' => 'threads', 'name' => 'Threads', 'domain' => 'threads.net', 'logo_source_url' => 'https://cdn.simpleicons.org/threads'],
            ['slug' => 'twitch', 'name' => 'Twitch', 'domain' => 'twitch.tv', 'logo_source_url' => 'https://cdn.simpleicons.org/twitch'],
            ['slug' => 'spotify', 'name' => 'Spotify', 'domain' => 'open.spotify.com', 'logo_source_url' => 'https://cdn.simpleicons.org/spotify'],
            ['slug' => 'medium', 'name' => 'Medium', 'domain' => 'medium.com', 'logo_source_url' => 'https://cdn.simpleicons.org/medium'],
            ['slug' => 'dev', 'name' => 'DEV', 'domain' => 'dev.to', 'logo_source_url' => 'https://cdn.simpleicons.org/devdotto'],
            ['slug' => 'dribbble', 'name' => 'Dribbble', 'domain' => 'dribbble.com', 'logo_source_url' => 'https://cdn.simpleicons.org/dribbble'],
            ['slug' => 'behance', 'name' => 'Behance', 'domain' => 'behance.net', 'logo_source_url' => 'https://cdn.simpleicons.org/behance'],
            ['slug' => 'product-hunt', 'name' => 'Product Hunt', 'domain' => 'producthunt.com', 'logo_source_url' => 'https://cdn.simpleicons.org/producthunt'],
            ['slug' => 'stackoverflow', 'name' => 'Stack Overflow', 'domain' => 'stackoverflow.com', 'logo_source_url' => 'https://cdn.simpleicons.org/stackoverflow'],
            ['slug' => 'telegram', 'name' => 'Telegram', 'domain' => 't.me', 'logo_source_url' => 'https://cdn.simpleicons.org/telegram'],
            ['slug' => 'discord', 'name' => 'Discord', 'domain' => 'discord.com', 'logo_source_url' => 'https://cdn.simpleicons.org/discord'],
            ['slug' => 'reddit', 'name' => 'Reddit', 'domain' => 'reddit.com', 'logo_source_url' => 'https://cdn.simpleicons.org/reddit'],
            ['slug' => 'website', 'name' => 'Website', 'domain' => null, 'logo_source_url' => null],
        ];
    }

    private function localLogoUrl(string $slug, ?string $sourceUrl): ?string
    {
        if ($sourceUrl === null) {
            return null;
        }

        $directory = public_path('platform-logos');
        $filename = "{$slug}.svg";

        File::ensureDirectoryExists($directory);

        $contents = Http::timeout(10)
            ->connectTimeout(5)
            ->get($sourceUrl)
            ->throw()
            ->body();

        File::put("{$directory}/{$filename}", $contents);

        return "/platform-logos/{$filename}";
    }
}
