<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount([
            'tasks as assigned_tasks_count',
            'tasks as completed_tasks_count' => fn($q) => $q->where('status', 'done'),
            'projects as active_projects_count',
        ])->where('role', '!=', 'client');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $members = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('team.index', compact('members'));
    }

    public function create()
    {
        return view('team.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:super_admin,project_manager,team_member',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'required|string|max:100',
            'department' => 'required|string|max:100',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'],
            'department' => $validated['department'],
            'is_active' => true,
        ]);

        ActivityLog::log('Added Team Member', "Added new team member: {$user->name} ({$user->job_title})");

        return redirect()->route('team.index')->with('success', 'Team member added successfully!');
    }

    public function edit(User $user)
    {
        return view('team.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:super_admin,project_manager,team_member',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'],
            'department' => $validated['department'],
            'is_active' => (bool) $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        ActivityLog::log('Updated Team Member', "Updated details for: {$user->name}");

        return redirect()->route('team.index')->with('success', 'Team member details updated!');
    }

    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        ActivityLog::log('User Status Changed', "Set {$user->name} status to " . ($user->is_active ? 'Active' : 'Inactive'));

        return back()->with('success', "User account has been " . ($user->is_active ? 'activated' : 'deactivated') . ".");
    }
}
