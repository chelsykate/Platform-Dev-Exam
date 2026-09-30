@extends('layouts.app')

@section('title', 'Users & Roles Management')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Users & Roles Management</h2>
            <p class="text-xs text-gray-500 mt-1">Manage system accounts, access levels, and role-based permissions.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-user-plus"></i>
            <span>Add New User</span>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search User</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Email..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Filter by Role</label>
                <select name="role_id" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Filter by Status</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Suspended" {{ request('status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Created Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-medium text-gray-900">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full font-semibold">
                                    {{ $user->role_name }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($user->status === 'Active')
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Active
                                    </span>
                                @elseif($user->status === 'Inactive')
                                    <span class="inline-flex items-center gap-1 text-gray-600 font-semibold bg-gray-100 px-2 py-0.5 rounded">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Inactive
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-red-700 font-semibold bg-red-50 px-2 py-0.5 rounded">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Suspended
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-500">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <a href="{{ route('users.show', $user) }}" class="p-1.5 text-gray-600 hover:text-emerald-700 transition" title="View Profile">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="p-1.5 text-gray-600 hover:text-blue-700 transition" title="Edit User">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-600 hover:text-red-700 transition" title="Delete User">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                <i class="fa-solid fa-user-slash text-2xl text-gray-300 mb-2"></i>
                                <p>No users found matching the search criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
