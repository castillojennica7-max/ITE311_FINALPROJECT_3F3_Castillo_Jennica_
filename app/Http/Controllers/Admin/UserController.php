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

        $users = User::when(in_array($role, self::ROLES), fn ($q) => $q->where('role', $role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('admin.users.index', [
            'users'  => $users,
            'role'   => $role,
            'roles'  => self::ROLES,
            'counts' => $counts,
        ]);
    }
}