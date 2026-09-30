@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Company Departments</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. organizational structure & divisions.</p>
        </div>
        <button onclick="document.getElementById('addDeptModal').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-plus"></i>
            <span>Add Department</span>
        </button>
    </div>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($departments as $dept)
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col justify-between space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">{{ $dept->department_name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $dept->description ?? 'No description' }}</p>
                    </div>
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 text-xs font-bold rounded-full border border-emerald-200">
                        {{ $dept->employees_count }} Employee(s)
                    </span>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <form method="POST" action="{{ route('departments.destroy', $dept) }}" onsubmit="return confirm('Delete this department?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-8 rounded-xl shadow-sm border border-gray-200 text-center text-gray-500 italic">
                No departments recorded yet.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal: Add Department -->
<div id="addDeptModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Add Department</h3>
            <button onclick="document.getElementById('addDeptModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('departments.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Department Name *</label>
                <input type="text" name="department_name" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. Agronomy & Field Operations">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full text-xs p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="Handles sugarcane planting, fertilization, harvesting..."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addDeptModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Save Department</button>
            </div>
        </form>
    </div>
</div>
@endsection
