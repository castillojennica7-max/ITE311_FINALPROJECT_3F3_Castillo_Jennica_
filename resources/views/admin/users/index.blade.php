<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="{{ route('admin.users.index', array_filter(['q' => $q])) }}"
                       class="px-3 py-1 rounded border {{ $role ? 'bg-white text-gray-700' : 'bg-[#7b2d5f] text-white' }}">
                        All ({{ $counts->sum() }})
                    </a>
                    @foreach($roles as $r)
                        <a href="{{ route('admin.users.index', array_filter(['role' => $r, 'q' => $q])) }}"
                           class="px-3 py-1 rounded border {{ $role === $r ? 'bg-[#7b2d5f] text-white' : 'bg-white text-gray-700' }}">
                            {{ str_replace('_', ' ', ucfirst($r)) }} ({{ $counts->get($r, 0) }})
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
                    @if($role)<input type="hidden" name="role" value="{{ $role }}">@endif
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search name or email"
                           class="rounded-full border-gray-300 px-4 py-2 text-sm focus:border-[#7b2d5f] focus:ring-[#7b2d5f]">
                    <button type="submit" class="px-5 py-2 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold hover:bg-[#5f2249]">Search</button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Registered</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $user->role) }}</td>
                                <td class="px-4 py-3">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($user->suspended_at)
                                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Suspended</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Active</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($user->isAdmin() || $user->id === auth()->id())
                                        <span class="text-gray-400">-</span>
                                    @elseif($user->suspended_at)
                                        <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-700 underline">Activate</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 underline"
                                                    onclick="return confirm('Suspend this user? They will be logged out and cannot log in.')">Suspend</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
