<?php

namespace Database\Seeders;

use App\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->platforms() as $platform) {
            Platform::query()->updateOrCreate(
                ['slug' => $platform['slug']],
                $platform,
            );
        }
    }

    /**
     * @return array<int, array{slug: string, name: string, domain: string|null, logo_url: string|null}>
     */
    private function platforms(): array
    {
        return [
            ['slug' => 'github', 'name' => 'GitHub', 'domain' => 'github.com', 'logo_url' => 'https://cdn.simpleicons.org/github'],
            ['slug' => 'x', 'name' => 'X', 'domain' => 'x.com', 'logo_url' => 'https://cdn.simpleicons.org/x'],
            ['slug' => 'instagram', 'name' => 'Instagram', 'domain' => 'instagram.com', 'logo_url' => 'https://cdn.simpleicons.org/instagram'],
            ['slug' => 'youtube', 'name' => 'YouTube', 'domain' => 'youtube.com', 'logo_url' => 'https://cdn.simpleicons.org/youtube'],
            ['slug' => 'linkedin', 'name' => 'LinkedIn', 'domain' => 'linkedin.com', 'logo_url' => 'https://cdn.simpleicons.org/linkedin'],
            ['slug' => 'bluesky', 'name' => 'Bluesky', 'domain' => 'bsky.app', 'logo_url' => 'https://cdn.simpleicons.org/bluesky'],
            ['slug' => 'mastodon', 'name' => 'Mastodon', 'domain' => 'mastodon.social', 'logo_url' => 'https://cdn.simpleicons.org/mastodon'],
            ['slug' => 'threads', 'name' => 'Threads', 'domain' => 'threads.net', 'logo_url' => 'https://cdn.simpleicons.org/threads'],
            ['slug' => 'twitch', 'name' => 'Twitch', 'domain' => 'twitch.tv', 'logo_url' => 'https://cdn.simpleicons.org/twitch'],
            ['slug' => 'spotify', 'name' => 'Spotify', 'domain' => 'open.spotify.com', 'logo_url' => 'https://cdn.simpleicons.org/spotify'],
            ['slug' => 'medium', 'name' => 'Medium', 'domain' => 'medium.com', 'logo_url' => 'https://cdn.simpleicons.org/medium'],
            ['slug' => 'dev', 'name' => 'DEV', 'domain' => 'dev.to', 'logo_url' => 'https://cdn.simpleicons.org/devdotto'],
            ['slug' => 'dribbble', 'name' => 'Dribbble', 'domain' => 'dribbble.com', 'logo_url' => 'https://cdn.simpleicons.org/dribbble'],
            ['slug' => 'behance', 'name' => 'Behance', 'domain' => 'behance.net', 'logo_url' => 'https://cdn.simpleicons.org/behance'],
            ['slug' => 'product-hunt', 'name' => 'Product Hunt', 'domain' => 'producthunt.com', 'logo_url' => 'https://cdn.simpleicons.org/producthunt'],
            ['slug' => 'stackoverflow', 'name' => 'Stack Overflow', 'domain' => 'stackoverflow.com', 'logo_url' => 'https://cdn.simpleicons.org/stackoverflow'],
            ['slug' => 'telegram', 'name' => 'Telegram', 'domain' => 't.me', 'logo_url' => 'https://cdn.simpleicons.org/telegram'],
            ['slug' => 'discord', 'name' => 'Discord', 'domain' => 'discord.com', 'logo_url' => 'https://cdn.simpleicons.org/discord'],
            ['slug' => 'reddit', 'name' => 'Reddit', 'domain' => 'reddit.com', 'logo_url' => 'https://cdn.simpleicons.org/reddit'],
            ['slug' => 'website', 'name' => 'Website', 'domain' => null, 'logo_url' => null],
        ];
    }
}
