<?php

namespace Database\Seeders;

use App\Link;
use App\Platform;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = $this->platforms();

        foreach ($platforms->shuffle()->take(15)->values() as $index => $platform) {
            $position = $index + 1;

            Link::factory()
                ->sample($position)
                ->for($platform)
                ->state([
                    'title' => $platform->name,
                    'description' => $position === 6 || fake()->boolean(25)
                        ? null
                        : "Profile and updates on {$platform->name}.",
                    'destination_url' => $this->destinationUrl($platform),
                ])
                ->create();
        }
    }

    /**
     * @return Collection<int, Platform>
     */
    private function platforms(): Collection
    {
        if ($this->missingSeededPlatforms()) {
            $this->call(PlatformSeeder::class);
        }

        return Platform::query()->get();
    }

    private function missingSeededPlatforms(): bool
    {
        $platformSlugs = array_column(PlatformSeeder::platforms(), 'slug');

        return Platform::query()
            ->whereIn('slug', $platformSlugs)
            ->distinct()
            ->count('slug') < count($platformSlugs);
    }

    private function destinationUrl(Platform $platform): string
    {
        $handle = fake()->unique()->userName();

        return match ($platform->slug) {
            'youtube' => "https://youtube.com/@{$handle}",
            'linkedin' => "https://linkedin.com/in/{$handle}",
            'bluesky' => "https://bsky.app/profile/{$handle}",
            'mastodon' => "https://mastodon.social/@{$handle}",
            'spotify' => "https://open.spotify.com/user/{$handle}",
            'medium' => "https://medium.com/@{$handle}",
            'dev' => "https://dev.to/{$handle}",
            'product-hunt' => "https://producthunt.com/@{$handle}",
            'stackoverflow' => 'https://stackoverflow.com/users/'.fake()->unique()->numberBetween(1000, 999999),
            'telegram' => "https://t.me/{$handle}",
            'discord' => 'https://discord.com/users/'.fake()->unique()->numberBetween(100000000000000000, 999999999999999999),
            'reddit' => "https://reddit.com/user/{$handle}",
            'website' => "https://{$handle}.example.com",
            default => "https://{$platform->domain}/{$handle}",
        };
    }
}
