@props([
    'name',
    'url' => null,
    'size' => 'md',
    'upload' => false,
    'inputId' => null,
])

@php
    $sizes = [
        'sm' => 'size-11',
        'md' => 'size-12',
        'lg' => 'size-14',
        'xl' => 'size-20',
    ];

    $frameSize = $sizes[$size] ?? $sizes['md'];
    $initials = Illuminate\Support\Str::of($name)->substr(0, 2)->upper();
    $frameClass = "{$frameSize} group relative flex shrink-0 items-center justify-center overflow-hidden rounded-md border border-zinc-700 bg-zinc-950/80 text-xs font-bold text-white shadow-sm shadow-black/20 ring-1 ring-white/5 transition";
    $frameClass .= $upload ? ' cursor-pointer hover:border-blue-500/70 focus-within:border-blue-500' : '';
@endphp

@if ($upload)
<label
    @if ($inputId) for="{{ $inputId }}" @endif
    {{ $attributes->class($frameClass) }}
>
    @if (filled($url))
        <img src="{{ $url }}" alt="" class="size-full object-contain p-2" loading="lazy">
    @else
        <span class="flex size-full items-center justify-center bg-blue-500 text-white">
            {{ $initials }}
        </span>
    @endif

    @if ($upload)
        <span class="absolute inset-x-0 bottom-0 bg-zinc-950/80 py-0.5 text-center text-[10px] font-medium text-zinc-300 opacity-0 transition group-hover:opacity-100">{{ __('Upload') }}</span>
    @endif

    {{ $slot }}
</label>
@else
<span {{ $attributes->class($frameClass) }}>
    @if (filled($url))
        <img src="{{ $url }}" alt="" class="size-full object-contain p-2" loading="lazy">
    @else
        <span class="flex size-full items-center justify-center bg-blue-500 text-white">
            {{ $initials }}
        </span>
    @endif

    {{ $slot }}
</span>
@endif
