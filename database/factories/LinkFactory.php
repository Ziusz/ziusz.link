<?php

namespace Database\Factories;

use App\Enums\LinkLifetime;
use App\Enums\LinkVisibility;
use App\Link;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'destination_url' => fake()->url(),
            'title' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'is_listed' => true,
            'visibility' => LinkVisibility::Featured,
            'sort_order' => 0,
            'expires_at' => null,
        ];
    }

    public function hidden(?DateTimeInterface $expiresAt = null): static
    {
        return $this->state(fn (): array => [
            'slug' => Link::generateUniqueSlug(),
            'is_listed' => false,
            'visibility' => LinkVisibility::Hidden,
            'expires_at' => $expiresAt ?? LinkLifetime::default()->expiresAt(),
        ]);
    }

    public function sample(int $position): static
    {
        $profiles = $this->profileTemplates();

        return $this->state(function () use ($position, $profiles): array {
            $profile = $profiles[fake()->unique()->randomElement(array_keys($profiles))];
            $handle = fake()->unique()->userName();
            $visibility = in_array($position, [14, 15], true) || fake()->boolean(12)
                ? LinkVisibility::Hidden
                : LinkVisibility::Featured;
            $clicksCount = $position === 1 || fake()->boolean(65)
                ? fake()->numberBetween(1, 250)
                : 0;

            return [
                'slug' => Str::slug($profile['title'].' '.$handle),
                'destination_url' => sprintf($profile['url'], $handle),
                'title' => $profile['title'],
                'description' => $position === 6 || fake()->boolean(25)
                    ? null
                    : $profile['description'],
                'is_active' => $position === 15 ? false : fake()->boolean(92),
                'is_listed' => $visibility === LinkVisibility::Featured,
                'visibility' => $visibility,
                'sort_order' => $position * 10,
                'clicks_count' => $clicksCount,
                'last_clicked_at' => $clicksCount > 0
                    ? now()->subDays(fake()->numberBetween(0, 45))->subMinutes(fake()->numberBetween(1, 1440))
                    : null,
                'expires_at' => $visibility === LinkVisibility::Hidden && fake()->boolean(80)
                    ? now()->addDays(fake()->randomElement([1, 3, 7, 14, 30, 90]))
                    : null,
            ];
        });
    }

    /**
     * @return array<string, array{title: string, url: string, description: string}>
     */
    private function profileTemplates(): array
    {
        return [
            'github' => [
                'title' => 'GitHub',
                'url' => 'https://github.com/%s',
                'description' => 'Code, experiments, and public repositories.',
            ],
            'x' => [
                'title' => 'X',
                'url' => 'https://x.com/%s',
                'description' => 'Short updates and ongoing notes.',
            ],
            'instagram' => [
                'title' => 'Instagram',
                'url' => 'https://instagram.com/%s',
                'description' => 'Photos, stories, and visual updates.',
            ],
            'youtube' => [
                'title' => 'YouTube',
                'url' => 'https://youtube.com/@%s',
                'description' => 'Videos, talks, and longer-form uploads.',
            ],
            'linkedin' => [
                'title' => 'LinkedIn',
                'url' => 'https://linkedin.com/in/%s',
                'description' => 'Professional profile and work history.',
            ],
            'bluesky' => [
                'title' => 'Bluesky',
                'url' => 'https://bsky.app/profile/%s',
                'description' => 'Social posts and conversation threads.',
            ],
            'mastodon' => [
                'title' => 'Mastodon',
                'url' => 'https://mastodon.social/@%s',
                'description' => 'Federated social updates.',
            ],
            'threads' => [
                'title' => 'Threads',
                'url' => 'https://threads.net/@%s',
                'description' => 'Casual posts and quick updates.',
            ],
            'twitch' => [
                'title' => 'Twitch',
                'url' => 'https://twitch.tv/%s',
                'description' => 'Streams and live sessions.',
            ],
            'spotify' => [
                'title' => 'Spotify',
                'url' => 'https://open.spotify.com/user/%s',
                'description' => 'Playlists and listening profile.',
            ],
            'medium' => [
                'title' => 'Medium',
                'url' => 'https://medium.com/@%s',
                'description' => 'Articles and essays.',
            ],
            'dev' => [
                'title' => 'DEV',
                'url' => 'https://dev.to/%s',
                'description' => 'Technical posts and notes.',
            ],
            'dribbble' => [
                'title' => 'Dribbble',
                'url' => 'https://dribbble.com/%s',
                'description' => 'Design shots and interface work.',
            ],
            'behance' => [
                'title' => 'Behance',
                'url' => 'https://behance.net/%s',
                'description' => 'Portfolio projects and case studies.',
            ],
            'product-hunt' => [
                'title' => 'Product Hunt',
                'url' => 'https://producthunt.com/@%s',
                'description' => 'Products, launches, and collections.',
            ],
            'stackoverflow' => [
                'title' => 'Stack Overflow',
                'url' => 'https://stackoverflow.com/users/%s',
                'description' => 'Questions, answers, and technical reputation.',
            ],
            'telegram' => [
                'title' => 'Telegram',
                'url' => 'https://t.me/%s',
                'description' => 'Direct channel and quick contact.',
            ],
            'discord' => [
                'title' => 'Discord',
                'url' => 'https://discord.com/users/%s',
                'description' => 'Community profile and chat identity.',
            ],
            'reddit' => [
                'title' => 'Reddit',
                'url' => 'https://reddit.com/user/%s',
                'description' => 'Posts, comments, and discussions.',
            ],
            'website' => [
                'title' => 'Website',
                'url' => 'https://%s.example.com',
                'description' => 'Personal website and main archive.',
            ],
        ];
    }
}
