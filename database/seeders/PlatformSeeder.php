<?php

namespace Database\Seeders;

use App\Platform;
use App\Support\LogoStore;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function __construct(private LogoStore $logos) {}

    public function run(): void
    {
        foreach (self::platforms() as $platform) {
            $platform['logo_url'] = $this->storedLogoPath(
                $platform['slug'],
                $platform['logo_source_url'],
            );

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
    public static function platforms(): array
    {
        return [
            ['slug' => 'github', 'name' => 'GitHub', 'domain' => 'github.com', 'logo_source_url' => 'https://cdn.simpleicons.org/github'],
            ['slug' => 'x', 'name' => 'X', 'domain' => 'x.com', 'logo_source_url' => 'https://cdn.simpleicons.org/x'],
            ['slug' => 'instagram', 'name' => 'Instagram', 'domain' => 'instagram.com', 'logo_source_url' => 'https://cdn.simpleicons.org/instagram'],
            ['slug' => 'youtube', 'name' => 'YouTube', 'domain' => 'youtube.com', 'logo_source_url' => 'https://cdn.simpleicons.org/youtube'],
            ['slug' => 'linkedin', 'name' => 'LinkedIn', 'domain' => 'linkedin.com', 'logo_source_url' => 'https://upload.wikimedia.org/wikipedia/commons/8/81/LinkedIn_icon.svg'],
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

    private function storedLogoPath(string $slug, ?string $sourceUrl): ?string
    {
        if ($sourceUrl === null) {
            return null;
        }

        return $this->logos->storeRemote('platform-logos', $slug, $sourceUrl)
            ?? Platform::query()->where('slug', $slug)->value('logo_url');
    }
}
