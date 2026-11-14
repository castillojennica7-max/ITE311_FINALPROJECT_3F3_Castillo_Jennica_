<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public const ROLES = ['admin', 'salon_owner', 'customer'];

    public function index(Request $request)
    {
        $role = $request->query('role');
        $q    = trim((string) $request->query('q'));

        $users = User::when(in_array($role, self::ROLES), fn ($query) => $query->where('role', $role))
            ->when($q !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")
            ))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('admin.users.index', [
            'users'  => $users,
            'role'   => $role,
            'q'      => $q,
            'roles'  => self::ROLES,
            'counts' => $counts,
        ]);
    }

    public function suspend(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'You cannot suspend your own account.');
        abort_if($user->isAdmin(), 403, 'Administrator accounts cannot be suspended here.');

        $user->forceFill(['suspended_at' => now()])->save();

        return back()->with('success', "{$user->name} has been suspended and can no longer log in.");
    }

    public function restore(Request $request, User $user)
    {
        $user->forceFill(['suspended_at' => null])->save();

        return back()->with('success', "{$user->name} can log in again.");
    }
}
