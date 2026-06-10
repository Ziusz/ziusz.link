@php
    $selectedVisibility = old('visibility', $link->visibility?->value ?? App\Enums\LinkVisibility::Hidden->value);
    $selectedLifetime = old('lifetime', $link->isHidden() && $link->expires_at === null ? App\Enums\LinkLifetime::Permanent->value : App\Enums\LinkLifetime::default()->value);
@endphp

<form method="POST" action="{{ $action }}" class="grid gap-6 rounded-lg border border-zinc-800 bg-zinc-900/50 p-5">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <label class="grid gap-2">
            <span class="text-sm font-medium text-zinc-200">{{ __('Destination URL') }}</span>
            <input name="destination_url" value="{{ old('destination_url', $link->destination_url) }}" required class="h-11 rounded-md border border-zinc-700 bg-zinc-950 px-3 text-sm text-white outline-none transition placeholder:text-zinc-600 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            @error('destination_url') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
        </label>

        <label class="grid gap-2">
            <span class="text-sm font-medium text-zinc-200">{{ __('Alias') }}</span>
            <input name="slug" value="{{ old('slug', $link->exists ? $link->slug : '') }}" placeholder="{{ __('Leave blank to generate') }}" class="h-11 rounded-md border border-zinc-700 bg-zinc-950 px-3 text-sm text-white outline-none transition placeholder:text-zinc-600 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            @error('slug') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
        </label>

        <label class="grid gap-2">
            <span class="text-sm font-medium text-zinc-200">{{ __('Title') }}</span>
            <input name="title" value="{{ old('title', $link->title) }}" class="h-11 rounded-md border border-zinc-700 bg-zinc-950 px-3 text-sm text-white outline-none transition placeholder:text-zinc-600 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            @error('title') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
        </label>

        <label class="grid gap-2">
            <span class="text-sm font-medium text-zinc-200">{{ __('Platform') }}</span>
            <select name="platform_id" class="h-11 rounded-md border border-zinc-700 bg-zinc-950 px-3 text-sm text-white outline-none transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
                <option value="">{{ __('No platform') }}</option>
                @foreach ($platforms as $platform)
                    <option value="{{ $platform->id }}" @selected((string) old('platform_id', $link->platform_id) === (string) $platform->id)>
                        {{ $platform->name }}
                    </option>
                @endforeach
            </select>
            @error('platform_id') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
        </label>

        <label class="grid gap-2">
            <span class="text-sm font-medium text-zinc-200">{{ __('Type') }}</span>
            <select name="visibility" class="h-11 rounded-md border border-zinc-700 bg-zinc-950 px-3 text-sm text-white outline-none transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
                @foreach ($visibilities as $value => $label)
                    <option value="{{ $value }}" @selected($selectedVisibility === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('visibility') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
        </label>

        <label class="grid gap-2">
            <span class="text-sm font-medium text-zinc-200">{{ __('Hidden link lifetime') }}</span>
            <select name="lifetime" class="h-11 rounded-md border border-zinc-700 bg-zinc-950 px-3 text-sm text-white outline-none transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
                @foreach ($lifetimes as $value => $label)
                    <option value="{{ $value }}" @selected($selectedLifetime === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('lifetime') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
        </label>

        <label class="grid gap-2">
            <span class="text-sm font-medium text-zinc-200">{{ __('Sort order') }}</span>
            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $link->sort_order ?? 0) }}" class="h-11 rounded-md border border-zinc-700 bg-zinc-950 px-3 text-sm text-white outline-none transition placeholder:text-zinc-600 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            @error('sort_order') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
        </label>

        <label class="grid gap-2">
            <span class="text-sm font-medium text-zinc-200">{{ __('Logo override URL') }}</span>
            <input name="logo_url" value="{{ old('logo_url', $link->logo_url) }}" class="h-11 rounded-md border border-zinc-700 bg-zinc-950 px-3 text-sm text-white outline-none transition placeholder:text-zinc-600 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            @error('logo_url') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
        </label>
    </div>

    <label class="grid gap-2">
        <span class="text-sm font-medium text-zinc-200">{{ __('Description') }}</span>
        <textarea name="description" rows="4" class="rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition placeholder:text-zinc-600 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">{{ old('description', $link->description) }}</textarea>
        @error('description') <span class="text-sm text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="flex items-center gap-3">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $link->is_active ?? true)) class="size-4 rounded border-zinc-700 bg-zinc-950 text-cyan-300 focus:ring-cyan-300/30">
        <span class="text-sm font-medium text-zinc-200">{{ __('Active') }}</span>
    </label>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-zinc-800 pt-5">
        <a href="{{ $link->exists ? route('admin.links.show', $link) : route('admin.links.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:text-white">
            {{ __('Cancel') }}
        </a>

        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-cyan-300 px-4 text-sm font-semibold text-zinc-950 transition hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/70">
            {{ $submitLabel }}
        </button>
    </div>
</form>
