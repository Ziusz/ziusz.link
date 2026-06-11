@php
    $selectedVisibility = old('visibility', $link->visibility?->value ?? App\Enums\LinkVisibility::Hidden->value);
    $selectedLifetime = old('lifetime', $link->isHidden() && $link->expires_at === null ? App\Enums\LinkLifetime::Permanent->value : App\Enums\LinkLifetime::default()->value);
    $logoName = $link->title ?: $link->slug ?: config('app.name', 'Ziusz Link');
    $logoUrl = $link->resolvedLogoUrl();
    $logoInputId = 'logo_file_'.($link->exists ? $link->getKey() : 'new');
    $logoRemoteValue = old('logo_url', App\Support\LogoStore::isStoredPath($link->logo_url) ? '' : $link->logo_url);
    $fieldClass = 'group grid gap-2 rounded-md border border-zinc-800 bg-zinc-950/60 p-3 transition focus-within:border-blue-500/70 focus-within:bg-zinc-950 focus-within:ring-2 focus-within:ring-blue-500/15';
    $labelClass = 'text-xs font-medium uppercase text-zinc-500 transition group-focus-within:text-blue-300';
    $inputClass = 'h-9 w-full rounded-md border-0 bg-transparent p-0 text-sm text-white outline-none placeholder:text-zinc-600 focus:ring-0';
    $selectClass = 'h-9 w-full rounded-md border-0 bg-transparent p-0 text-sm text-white outline-none focus:ring-0';
    $textareaClass = 'min-h-28 w-full resize-y rounded-md border-0 bg-transparent p-0 text-sm leading-6 text-white outline-none placeholder:text-zinc-600 focus:ring-0';
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-900/70 shadow-2xl shadow-black/10 ring-1 ring-white/5">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 p-5 sm:p-6">
        <section class="grid gap-4">
            <h2 class="text-sm font-semibold text-white">{{ __('Routing') }}</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="{{ $fieldClass }} sm:col-span-2">
                    <span class="{{ $labelClass }}">{{ __('Destination URL') }}</span>
                    <input name="destination_url" value="{{ old('destination_url', $link->destination_url) }}" required class="{{ $inputClass }}">
                    @error('destination_url')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="{{ $fieldClass }}">
                    <span class="{{ $labelClass }}">{{ __('Alias') }}</span>
                    <input name="slug" value="{{ old('slug', $link->exists ? $link->slug : '') }}" placeholder="{{ __('Leave blank to generate') }}" class="{{ $inputClass }}">
                    @error('slug')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="{{ $fieldClass }}">
                    <span class="{{ $labelClass }}">{{ __('Sort order') }}</span>
                    <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $link->sort_order ?? 0) }}" class="{{ $inputClass }}">
                    @error('sort_order')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </section>

        <section class="grid gap-4 border-t border-zinc-800 pt-6">
            <h2 class="text-sm font-semibold text-white">{{ __('Presentation') }}</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="flex items-start gap-4 rounded-md border border-zinc-800 bg-zinc-950/60 p-3 sm:col-span-2">
                    <x-link-logo :name="$logoName" :url="$logoUrl" size="xl" upload :input-id="$logoInputId">
                        <input id="{{ $logoInputId }}" name="logo_file" type="file" accept="image/png,image/jpeg,image/webp,image/gif" class="sr-only">
                    </x-link-logo>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white">{{ __('Logo') }}</p>
                        <p class="mt-1 text-sm leading-6 text-zinc-400">{{ __('Click the logo to choose an uploaded replacement, or paste a remote logo URL below.') }}</p>

                        @error('logo_file')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <label class="{{ $fieldClass }}">
                    <span class="{{ $labelClass }}">{{ __('Title') }}</span>
                    <input name="title" value="{{ old('title', $link->title) }}" class="{{ $inputClass }}">
                    @error('title')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="{{ $fieldClass }}">
                    <span class="{{ $labelClass }}">{{ __('Platform') }}</span>
                    <select name="platform_id" class="{{ $selectClass }}">
                        <option value="">{{ __('No platform') }}</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform->id }}" @selected((string) old('platform_id', $link->platform_id) === (string) $platform->id)>
                                {{ $platform->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('platform_id')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="{{ $fieldClass }} sm:col-span-2">
                    <span class="{{ $labelClass }}">{{ __('Remote logo URL') }}</span>
                    <input name="logo_url" value="{{ $logoRemoteValue }}" class="{{ $inputClass }}">
                    @error('logo_url')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="{{ $fieldClass }} sm:col-span-2">
                    <span class="{{ $labelClass }}">{{ __('Description') }}</span>
                    <textarea name="description" rows="4" class="{{ $textareaClass }}">{{ old('description', $link->description) }}</textarea>
                    @error('description')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </section>

        <section class="grid gap-4 border-t border-zinc-800 pt-6">
            <h2 class="text-sm font-semibold text-white">{{ __('Availability') }}</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="{{ $fieldClass }}">
                    <span class="{{ $labelClass }}">{{ __('Type') }}</span>
                    <select name="visibility" class="{{ $selectClass }}">
                        @foreach ($visibilities as $value => $label)
                            <option value="{{ $value }}" @selected($selectedVisibility === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('visibility')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="{{ $fieldClass }}">
                    <span class="{{ $labelClass }}">{{ __('Hidden link lifetime') }}</span>
                    <select name="lifetime" class="{{ $selectClass }}">
                        @foreach ($lifetimes as $value => $label)
                            <option value="{{ $value }}" @selected($selectedLifetime === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('lifetime')
                        <span class="text-sm text-red-300">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="flex items-center justify-between gap-4 rounded-md border border-zinc-800 bg-zinc-950/60 p-3 transition focus-within:border-blue-500/70 focus-within:ring-2 focus-within:ring-blue-500/15">
                <span class="text-sm font-medium text-zinc-200">{{ __('Active') }}</span>
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $link->is_active ?? true)) class="size-5 rounded border-zinc-700 bg-zinc-950 text-blue-500 accent-blue-500 focus:ring-2 focus:ring-blue-500/30">
            </label>
        </section>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-zinc-800 bg-zinc-950/50 px-5 py-4 sm:px-6">
        <a href="{{ $link->exists ? route('admin.links.show', $link) : route('admin.links.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-700 px-4 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:bg-zinc-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/40">
            {{ __('Cancel') }}
        </a>

        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-accent px-4 text-sm font-semibold text-accent-foreground transition hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/60">
            {{ $submitLabel }}
        </button>
    </div>
</form>
