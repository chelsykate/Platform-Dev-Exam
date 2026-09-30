@extends('layouts.app')

@section('title', $farmer->full_name . ' - Profile')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('farmers.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 px-3 py-2 rounded-lg transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Farmers
            </a>
            <span class="text-xs font-mono bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded">
                {{ $farmer->farmer_code }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('farmers.edit', $farmer) }}" class="text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white px-3.5 py-2 rounded-lg shadow transition">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Profile
            </a>
        </div>
    </div>

    <!-- Profile Header Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-emerald-800 text-white font-bold text-xl flex items-center justify-center shadow">
                    {{ strtoupper(substr($farmer->first_name, 0, 1) . substr($farmer->last_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $farmer->full_name }}</h2>
                    <p class="text-xs text-gray-500"><i class="fa-solid fa-location-dot text-emerald-600 mr-1"></i> {{ $farmer->address }}, {{ $farmer->barangay }}, {{ $farmer->municipality }}, {{ $farmer->province }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-3 border-t border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 block font-medium">Contact Number</span>
                    <span class="font-semibold text-gray-800">{{ $farmer->contact_number }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Email Address</span>
                    <span class="font-semibold text-gray-800">{{ $farmer->email ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Account Status</span>
                    <span class="inline-block px-2 py-0.5 rounded font-bold text-[11px] {{ $farmer->status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                        {{ $farmer->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Summary Stats -->
        <div class="bg-emerald-950 text-white p-4 rounded-xl flex flex-col justify-between shadow-inner">
            <span class="text-xs text-emerald-300 font-semibold uppercase tracking-wider">Planter Portfolio</span>
            
            <div class="grid grid-cols-2 gap-2 my-2">
                <div>
                    <p class="text-2xl font-bold text-white">{{ $farmer->farms->count() }}</p>
                    <p class="text-[10px] text-emerald-300">Registered Farms</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-400">{{ $farmer->farms->sum('farm_size') }}</p>
                    <p class="text-[10px] text-emerald-300">Total Hectares</p>
                </div>
            </div>

            <div class="text-[11px] text-emerald-200 border-t border-emerald-800/80 pt-2 flex justify-between">
                <span>Total Yield Recorded:</span>
                <strong class="text-white">{{ $farmer->productions->sum('actual_yield') }} Tons</strong>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs / Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Columns: Farms & Production -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Farms Section -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-900 text-base"><i class="fa-solid fa-wheat-field text-emerald-700 mr-2"></i> Registered Sugarcane Farms</h3>
                    <button onclick="document.getElementById('addFarmModal').classList.toggle('hidden')" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                        <i class="fa-solid fa-plus mr-1"></i> Add Farm
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($farmer->farms as $farm)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-2 relative">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm">{{ $farm->farm_name }}</h4>
                                    <p class="text-xs text-gray-500"><i class="fa-solid fa-location-pin text-gray-400 mr-1"></i> {{ $farm->location }}</p>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">{{ $farm->status }}</span>
                            </div>

                            <div class="grid grid-cols-2 text-xs border-t border-gray-200 pt-2 mt-2">
                                <div>
                                    <span class="text-gray-400">Area Size:</span>
                                    <p class="font-bold text-gray-800">{{ $farm->farm_size }} {{ $farm->farm_size_unit }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400">Soil Type:</span>
                                    <p class="font-bold text-gray-800">{{ $farm->soil_type }}</p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 text-xs pt-1">
                                <form method="POST" action="{{ route('farms.destroy', $farm) }}" onsubmit="return confirm('Delete this farm?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-[11px] font-semibold">Delete Farm</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 text-center py-6 text-gray-500 text-xs italic">
                            No farms registered for this farmer yet. Click "Add Farm" to register a plot.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Production Records Section -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-900 text-base"><i class="fa-solid fa-chart-area text-amber-600 mr-2"></i> Sugarcane Production & Yield Records</h3>
                    @if($farmer->farms->count() > 0)
                        <button onclick="document.getElementById('addProductionModal').classList.toggle('hidden')" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                            <i class="fa-solid fa-plus mr-1"></i> Log Harvest/Production
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                                <th class="py-2.5 px-3">Crop Year</th>
                                <th class="py-2.5 px-3">Farm</th>
                                <th class="py-2.5 px-3">Planting Date</th>
                                <th class="py-2.5 px-3">Harvest Date</th>
                                <th class="py-2.5 px-3">Est. Yield</th>
                                <th class="py-2.5 px-3">Actual Yield</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($farmer->productions as $prod)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2.5 px-3 font-bold text-gray-900">{{ $prod->crop_year }}</td>
                                    <td class="py-2.5 px-3 text-gray-700">{{ $prod->farm->farm_name ?? 'N/A' }}</td>
                                    <td class="py-2.5 px-3 text-gray-500">{{ $prod->planting_date ? $prod->planting_date->format('M d, Y') : '-' }}</td>
                                    <td class="py-2.5 px-3 text-gray-500">{{ $prod->harvest_date ? $prod->harvest_date->format('M d, Y') : '-' }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-amber-700">{{ $prod->estimated_yield }} Tons</td>
                                    <td class="py-2.5 px-3 font-bold text-emerald-700">{{ $prod->actual_yield ? $prod->actual_yield . ' Tons' : 'Pending' }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">{{ $prod->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500 text-xs italic">
                                        No production records logged for this farmer yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right 1 Column: Activity Timeline -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
            <h3 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3"><i class="fa-solid fa-clock-rotate-left text-emerald-700 mr-2"></i> Activity Log Timeline</h3>
            
            <div class="relative pl-4 border-l-2 border-emerald-200 space-y-4 text-xs">
                @forelse($farmer->activities as $act)
                    <div class="relative">
                        <span class="absolute -left-[21px] top-0 w-3.5 h-3.5 rounded-full bg-emerald-600 border-2 border-white"></span>
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <span class="font-bold text-gray-900 block">{{ $act->activity_type }}</span>
                            <p class="text-gray-600 mt-1">{{ $act->description }}</p>
                            <div class="flex justify-between items-center text-[10px] text-gray-400 mt-2 pt-1 border-t border-gray-200/60">
                                <span>By: {{ $act->created_by }}</span>
                                <span>{{ $act->activity_date ? $act->activity_date->diffForHumans() : '' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 italic text-center py-4">No activities logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Farm -->
<div id="addFarmModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Add Sugarcane Farm Plot</h3>
            <button onclick="document.getElementById('addFarmModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('farms.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Farm Name / Identifier *</label>
                <input type="text" name="farm_name" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. Guihing Farm Sector A">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Location / Barangay *</label>
                <input type="text" name="location" value="{{ $farmer->barangay }}, {{ $farmer->municipality }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Farm Size *</label>
                    <input type="number" step="0.01" name="farm_size" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="5.50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Unit *</label>
                    <select name="farm_size_unit" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        <option value="Hectares">Hectares</option>
                        <option value="Square Meters">Square Meters</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Soil Type *</label>
                    <select name="soil_type" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        <option value="Loam">Loam Soil</option>
                        <option value="Clay">Clay Soil</option>
                        <option value="Sandy Loam">Sandy Loam</option>
                        <option value="Volcanic Alluvial">Volcanic Alluvial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Farm Status *</label>
                    <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        <option value="Active">Active</option>
                        <option value="Fallow">Fallow</option>
                        <option value="Under Harvest">Under Harvest</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addFarmModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Save Farm Plot</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Production Record -->
@if($farmer->farms->count() > 0)
<div id="addProductionModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-amber-700 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Log Crop Yield & Production</h3>
            <button onclick="document.getElementById('addProductionModal').classList.add('hidden')" class="text-white hover:text-amber-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('farmer-production.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Select Farm *</label>
                <select name="farm_id" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600">
                    @foreach($farmer->farms as $farm)
                        <option value="{{ $farm->id }}">{{ $farm->farm_name }} ({{ $farm->farm_size }} {{ $farm->farm_size_unit }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Crop Year *</label>
                    <input type="text" name="crop_year" value="2025-2026" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600" placeholder="2025-2026">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                    <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600">
                        <option value="Planted">Planted</option>
                        <option value="Growing">Growing</option>
                        <option value="Harvested">Harvested</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Planting Date *</label>
                    <input type="date" name="planting_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Harvest Date</label>
                    <input type="date" name="harvest_date" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Est. Yield (Tons) *</label>
                    <input type="number" step="0.1" name="estimated_yield" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600" placeholder="e.g. 150">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Actual Yield (Tons)</label>
                    <input type="number" step="0.1" name="actual_yield" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600" placeholder="e.g. 145.5">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addProductionModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 text-white text-xs font-semibold rounded-lg shadow">Log Production</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

