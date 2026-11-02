<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            <div class="mb-4 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('admin.users.index') }}"
                   class="px-3 py-1 rounded border {{ $role ? 'bg-white text-gray-700' : 'bg-[#7b2d5f] text-white' }}">
                    All ({{ $counts->sum() }})
                </a>
                @foreach($roles as $r)
                    <a href="{{ route('admin.users.index', ['role' => $r]) }}"
                       class="px-3 py-1 rounded border {{ $role === $r ? 'bg-[#7b2d5f] text-white' : 'bg-white text-gray-700' }}">
                        {{ str_replace('_', ' ', ucfirst($r)) }} ({{ $counts->get($r, 0) }})
                    </a>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $user->role) }}</td>
                                <td class="px-4 py-3">{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>