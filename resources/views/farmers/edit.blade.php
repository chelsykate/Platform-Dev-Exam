@extends('layouts.app')

@section('title', 'Edit Farmer Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Edit Farmer Profile</h2>
            <p class="text-xs text-gray-500 mt-1">Farmer Code: <span class="font-mono font-bold text-emerald-800">{{ $farmer->farmer_code }}</span></p>
        </div>
        <a href="{{ route('farmers.show', $farmer) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 px-3 py-2 rounded-lg transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Profile
        </a>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('farmers.update', $farmer) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $farmer->first_name) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    @error('first_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $farmer->middle_name) }}" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $farmer->last_name) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    @error('last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Contact Number *</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $farmer->contact_number) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    @error('contact_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $farmer->email) }}" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Street / Sitio Address *</label>
                <input type="text" name="address" value="{{ old('address', $farmer->address) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                @error('address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Barangay *</label>
                    <input type="text" name="barangay" value="{{ old('barangay', $farmer->barangay) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    @error('barangay') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Municipality *</label>
                    <input type="text" name="municipality" value="{{ old('municipality', $farmer->municipality) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    @error('municipality') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Province *</label>
                    <input type="text" name="province" value="{{ old('province', $farmer->province) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    @error('province') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Farmer Status *</label>
                <select name="status" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="Active" {{ old('status', $farmer->status) == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Pending" {{ old('status', $farmer->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Under Verification" {{ old('status', $farmer->status) == 'Under Verification' ? 'selected' : '' }}>Under Verification</option>
                    <option value="Inactive" {{ old('status', $farmer->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <a href="{{ route('farmers.show', $farmer) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Cancel</a>
                <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow transition">
                    Update Profile Details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

