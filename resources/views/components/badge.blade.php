@props(['type' => 'primary', 'rounded' => false])

@php
    $bgClass = 'bg-' . $type;
    $textClass = in_array($type, ['warning', 'info', 'light']) ? 'text-dark' : 'text-white';
    
    // Khusus untuk style soft/pastel (bg-opacity) jika diset
    if (isset($attributes['soft'])) {
        $bgClass = "bg-{$type} bg-opacity-10";
        $textClass = "text-{$type} border border-{$type} border-opacity-10";
    }

    $roundedClass = $rounded ? 'rounded-pill px-3' : '';
@endphp

<span {{ $attributes->merge(['class' => "badge {$bgClass} {$textClass} {$roundedClass}"]) }}>
    {{ $slot }}
</span>
