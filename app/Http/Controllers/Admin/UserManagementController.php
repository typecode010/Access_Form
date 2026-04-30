<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display all users with role management controls.
     */
    public function index(): View
    {
        $users = User::query()
            ->with('roles:id,name')
            ->orderBy('id')
            ->paginate(15);

        $roles = Role::query()
            ->whereIn('name', ['Admin', 'FormCreator', 'Respondent'])
            ->orderBy('name')
            ->get(['name']);

        return view('admin.users.index', [
            'pageTitle' => 'Admin User Management',
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Update a user's primary role.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:Admin,FormCreator,Respondent'],
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Role updated for {$user->email}.");
    }
}
