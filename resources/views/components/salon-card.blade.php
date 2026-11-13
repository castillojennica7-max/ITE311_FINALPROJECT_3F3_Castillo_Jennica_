@props(['salon', 'book' => false])

@php
    $palettes = [
        'from-[#7b2d5f] to-[#c45a9a]',
        'from-[#2d5f7b] to-[#5ac4b8]',
        'from-[#7b5f2d] to-[#c4a05a]',
        'from-[#5f2d7b] to-[#9a5ac4]',
        'from-[#2d7b5f] to-[#5ac49a]',
        'from-[#7b2d3f] to-[#c45a6a]',
    ];
    $gradient = $palettes[$salon->id % count($palettes)];
    $cover    = $salon->coverUrl();
    $logo     = $salon->logoUrl();
    $from     = $salon->startingPrice();
    $isOpen   = $salon->isOpenAt(now());
@endphp

<div class="group flex flex-col bg-white rounded-2xl border border-[#f0dce8] shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 overflow-hidden">
    <div class="relative h-40 overflow-hidden {{ $cover ? '' : 'bg-gradient-to-br ' . $gradient }}">
        @if($cover)
            <img src="{{ $cover }}" alt="{{ $salon->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white/15"></div>
            <div class="absolute -bottom-10 left-10 w-28 h-28 rounded-full bg-white/10"></div>
        @endif

        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-semibold {{ $isOpen ? 'bg-green-100 text-green-800' : 'bg-white/90 text-gray-600' }}">
            {{ $isOpen ? 'Open now' : 'Closed now' }}
        </span>
    </div>

    <div class="relative px-5 pb-5 pt-9 flex-1 flex flex-col">
        <div class="absolute -top-7 left-5 w-14 h-14 rounded-xl bg-white shadow ring-4 ring-white overflow-hidden flex items-center justify-center">
            @if($logo)
                <img src="{{ $logo }}" alt="" class="w-full h-full object-cover">
            @else
                <span class="font-extrabold text-[#7b2d5f] text-lg">{{ $salon->initials() }}</span>
            @endif
        </div>

        <h3 class="text-lg font-bold text-gray-900 leading-snug">{{ $salon->name }}</h3>
        @if($salon->tagline)
            <p class="text-sm text-gray-500 mt-0.5">{{ $salon->tagline }}</p>
        @endif

        <ul class="mt-3 space-y-1 text-sm text-gray-600">
            @if($salon->address)
                <li>📍 {{ $salon->address }}</li>
            @endif
            <li>🕒 {{ $salon->todayLabel() }}</li>
            @if($from)
                <li>💸 Services from <span class="font-semibold text-[#7b2d5f]">₱{{ number_format($from) }}</span></li>
            @endif
        </ul>

        <div class="mt-5 pt-1 flex gap-2 mt-auto">
            <a href="{{ route('salons.show', $salon) }}"
               class="flex-1 text-center px-4 py-2 rounded-full border-2 border-[#7b2d5f] text-[#7b2d5f] text-sm font-semibold hover:bg-[#7b2d5f] hover:text-white transition">
                View salon
            </a>
            @if($book)
                <a href="{{ route('appointments.book', $salon) }}"
                   class="flex-1 text-center px-4 py-2 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold hover:bg-[#5f2249] transition">
                    Book here
                </a>
            @endif
        </div>
    </div>
</div>
