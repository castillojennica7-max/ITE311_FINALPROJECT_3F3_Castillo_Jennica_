@props(['status'])
@php
    $class = match (strtolower($status)) {
        'confirmed'           => 'bg-blue-100 text-blue-700',
        'completed'           => 'bg-green-100 text-green-700',
        'cancelled', 'rejected' => 'bg-red-100 text-red-700',
        default               => 'bg-amber-100 text-amber-700',
    };
@endphp
<span {{ $attributes->class(['inline-flex items-center px-3 py-1 rounded-full text-xs font-bold capitalize', $class]) }}>{{ $status }}</span>
