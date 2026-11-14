@props(['series', 'money' => false, 'color' => 'bg-[#7b2d5f]'])

@php
    $values = array_values($series);
    $max    = max(1, count($values) ? max($values) : 1);
    $keys   = array_keys($series);
@endphp

@if(count($series))
    <div>
        <div class="flex items-end gap-px h-44 border-b border-gray-200">
            @foreach($series as $date => $value)
                @php $height = $value > 0 ? max(3, (int) round($value / $max * 100)) : 0; @endphp
                <div class="group flex-1 min-w-0 h-full flex flex-col justify-end"
                     title="{{ \Illuminate\Support\Carbon::parse($date)->format('M d') }}: {{ $money ? '₱' . number_format($value, 2) : $value }}">
                    <div class="{{ $color }} rounded-t opacity-80 group-hover:opacity-100 transition" style="height: {{ $height }}%"></div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-2">
            <span>{{ \Illuminate\Support\Carbon::parse($keys[0])->format('M d') }}</span>
            <span>Highest: {{ $money ? '₱' . number_format($max, 2) : $max }}</span>
            <span>{{ \Illuminate\Support\Carbon::parse($keys[count($keys) - 1])->format('M d') }}</span>
        </div>
    </div>
@else
    <p class="text-sm text-gray-500">The chart is shown for date ranges of up to 92 days.</p>
@endif
