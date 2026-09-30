@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Audit Logs</h2>
        <p class="text-xs text-gray-500 mt-1">Operational trail of create, update, and status changes across the system.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wide text-gray-600 font-bold">Date</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wide text-gray-600 font-bold">User</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wide text-gray-600 font-bold">Module</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wide text-gray-600 font-bold">Action</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wide text-gray-600 font-bold">Record</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($auditLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->created_at ? $log->created_at->format('M d, Y H:i') : '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->module }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->action }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->record_id ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No audit activity has been recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
