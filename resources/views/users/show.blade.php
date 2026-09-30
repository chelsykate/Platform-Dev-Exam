@extends('layouts.app')

@section('title', 'User Profile Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">User Profile</h2>
            <p class="text-xs text-gray-500 mt-1">Detailed information and audit log history.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('users.edit', $user) }}" class="text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-2 rounded-lg transition">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit User
            </a>
            <a href="{{ route('users.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 px-3 py-2 rounded-lg transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Users
            </a>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <div class="w-20 h-20 rounded-full bg-emerald-700 text-white font-bold flex items-center justify-center text-2xl shadow">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        
        <div class="space-y-2 text-center sm:text-left flex-1">
            <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
            <p class="text-xs text-gray-500"><i class="fa-solid fa-envelope mr-1"></i> {{ $user->email }}</p>

            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-2">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-800 text-xs font-semibold rounded-full border border-emerald-200">
                    Role: {{ $user->role_name }}
                </span>
                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                    Status: {{ $user->status }}
                </span>
                <span class="text-xs text-gray-400">
                    Registered: {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                </span>
            </div>
        </div>
    </div>

    <!-- User Recent Audit Logs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2">Recent User Activity & Audit Logs</h4>
        
        <div class="space-y-3">
            @forelse($user->auditLogs as $log)
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 text-xs flex justify-between items-center">
                    <div>
                        <span class="font-bold text-gray-800">{{ $log->action }}</span>
                        <span class="text-gray-500 ml-2">Module: {{ $log->module }}</span>
                    </div>
                    <span class="text-gray-400 text-[11px]">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</span>
                </div>
            @empty
                <p class="text-xs text-gray-500 italic py-4 text-center">No recent audit log activities recorded for this user.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
