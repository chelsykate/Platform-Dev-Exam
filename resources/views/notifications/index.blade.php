@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Notifications</h2>
            <p class="text-xs text-gray-500 mt-1">System alerts, farmer updates, and operational activity.</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wide">
            {{ $unreadCount }} unread
        </span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <div class="p-4 flex items-start justify-between gap-4 {{ $notification->is_read ? 'bg-white' : 'bg-emerald-50/40' }}">
                    <div class="flex gap-3">
                        <div class="mt-1 flex-none">
                            @switch($notification->type)
                                @case('success')
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><i class="fa-solid fa-circle-check"></i></span>
                                    @break
                                @case('warning')
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700"><i class="fa-solid fa-triangle-exclamation"></i></span>
                                    @break
                                @case('alert')
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-700"><i class="fa-solid fa-bolt"></i></span>
                                    @break
                                @default
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 text-sky-700"><i class="fa-solid fa-bell"></i></span>
                            @endswitch
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $notification->title }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 mt-2">
                                {{ $notification->created_at ? $notification->created_at->format('M d, Y H:i') : '—' }}
                            </p>
                        </div>
                    </div>

                    @if(!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification) }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Mark read</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="p-10 text-center text-sm text-gray-500">
                    No notifications yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
