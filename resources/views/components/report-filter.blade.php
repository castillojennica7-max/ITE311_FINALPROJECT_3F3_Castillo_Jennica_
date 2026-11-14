@props(['range', 'action'])

<form method="GET" action="{{ $action }}" class="flex flex-wrap items-end gap-3">
    <div>
        <label for="from" class="block text-xs font-medium text-gray-500 mb-1">From</label>
        <input id="from" type="date" name="from" value="{{ $range->from->toDateString() }}"
               class="border-gray-300 rounded-md text-sm focus:border-[#7b2d5f] focus:ring-[#7b2d5f]">
    </div>
    <div>
        <label for="to" class="block text-xs font-medium text-gray-500 mb-1">To</label>
        <input id="to" type="date" name="to" value="{{ $range->to->toDateString() }}"
               class="border-gray-300 rounded-md text-sm focus:border-[#7b2d5f] focus:ring-[#7b2d5f]">
    </div>
    <button type="submit"
            class="px-5 py-2 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold hover:bg-[#5f2249] transition">
        Apply
    </button>

    <div class="flex flex-wrap gap-2 text-sm">
        <a class="px-3 py-1.5 rounded-full border border-[#e2bcd3] text-[#7b2d5f] hover:bg-[#f3e3ee]"
           href="{{ $action }}?from={{ now()->subDays(6)->toDateString() }}&amp;to={{ now()->toDateString() }}">Last 7 days</a>
        <a class="px-3 py-1.5 rounded-full border border-[#e2bcd3] text-[#7b2d5f] hover:bg-[#f3e3ee]"
           href="{{ $action }}?from={{ now()->subDays(29)->toDateString() }}&amp;to={{ now()->toDateString() }}">Last 30 days</a>
        <a class="px-3 py-1.5 rounded-full border border-[#e2bcd3] text-[#7b2d5f] hover:bg-[#f3e3ee]"
           href="{{ $action }}?from={{ now()->startOfMonth()->toDateString() }}&amp;to={{ now()->toDateString() }}">This month</a>
        <a class="px-3 py-1.5 rounded-full border border-[#e2bcd3] text-[#7b2d5f] hover:bg-[#f3e3ee]"
           href="{{ $action }}?from={{ now()->startOfYear()->toDateString() }}&amp;to={{ now()->toDateString() }}">This year</a>
    </div>
</form>
