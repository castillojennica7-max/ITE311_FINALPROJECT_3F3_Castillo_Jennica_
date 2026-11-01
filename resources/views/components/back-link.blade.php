@props(['href' => null, 'label' => 'Back to Dashboard'])

<a href="{{ $href ?? route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-[#7b2d5f] hover:underline">
    &larr; {{ $label }}
</a>