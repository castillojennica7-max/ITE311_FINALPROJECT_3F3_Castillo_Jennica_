<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Staff</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4">
                <a href="{{ route('staff.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-[#7b2d5f] rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5f2249] transition">
                    + Add Staff
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMembers as $member)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $member->name }}</td>
                                <td class="px-4 py-3">{{ $member->role }}</td>
                                <td class="px-4 py-3">{{ $member->phone }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">No staff yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>