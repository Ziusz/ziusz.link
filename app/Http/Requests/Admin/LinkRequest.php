<?php

namespace App\Http\Requests\Admin;

use App\Enums\LinkLifetime;
use App\Enums\LinkVisibility;
use App\Link;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $link = $this->route('link');
        $ignoreId = $link instanceof Link ? $link->getKey() : null;

        return [
            'description' => ['nullable', 'string'],
            'destination_url' => ['required', 'url', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'lifetime' => ['nullable', Rule::in($this->enumValues(LinkLifetime::cases()))],
            'logo_url' => ['nullable', 'url', 'max:2048'],
            'platform_id' => ['nullable', 'integer', 'exists:platforms,id'],
            'slug' => ['nullable', 'alpha_dash', 'max:128', Rule::unique('links', 'slug')->ignore($ignoreId)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'title' => ['nullable', 'string', 'max:255'],
            'visibility' => ['required', Rule::in($this->enumValues(LinkVisibility::cases()))],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function linkAttributes(): array
    {
        $data = $this->validated();
        $visibility = LinkVisibility::from($data['visibility']);
        $lifetime = LinkLifetime::from($data['lifetime'] ?? LinkLifetime::default()->value);
        $slug = filled($data['slug'] ?? null)
            ? Str::of((string) $data['slug'])->trim()->lower()->toString()
            : Link::generateUniqueSlug();

        return [
            'description' => $data['description'] ?? null,
            'destination_url' => $data['destination_url'],
            'expires_at' => $visibility === LinkVisibility::Hidden ? $lifetime->expiresAt() : null,
            'is_active' => $this->boolean('is_active'),
            'is_listed' => $visibility === LinkVisibility::Featured,
            'logo_url' => $data['logo_url'] ?? null,
            'platform_id' => $data['platform_id'] ?? null,
            'slug' => $slug,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'title' => $data['title'] ?? null,
            'visibility' => $visibility,
        ];
    }

    /**
     * @template TEnum of \BackedEnum
     *
     * @param  array<int, TEnum>  $cases
     * @return array<int, string>
     */
    private function enumValues(array $cases): array
    {
        return array_map(fn (\BackedEnum $case): string => (string) $case->value, $cases);
    }
}
