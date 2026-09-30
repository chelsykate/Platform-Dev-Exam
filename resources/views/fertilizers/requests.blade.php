@extends('layouts.app')

@section('title', 'Fertilizer Requests')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Fertilizer Assistance Requests</h2>
            <p class="text-xs text-gray-500 mt-1">Review farmer requests, approve allocations, and process warehouse release.</p>
        </div>
        <button onclick="document.getElementById('addRequestModal').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-file-circle-plus"></i>
            <span>Submit Fertilizer Request</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('fertilizer-requests.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search Request / Farmer</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, Farmer Name..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Under Review" {{ request('status') == 'Under Review' ? 'selected' : '' }}>Under Review</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="Ready for Release" {{ request('status') == 'Ready for Release' ? 'selected' : '' }}>Ready for Release</option>
                    <option value="Released" {{ request('status') == 'Released' ? 'selected' : '' }}>Released</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('fertilizer-requests.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Req. Code</th>
                        <th class="py-3 px-4">Farmer</th>
                        <th class="py-3 px-4">Fertilizer Formula</th>
                        <th class="py-3 px-4">Requested Qty</th>
                        <th class="py-3 px-4">Approved Qty</th>
                        <th class="py-3 px-4">Request Date</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Workflow Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $req->request_code }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">
                                {{ $req->farmer->full_name }}
                                <span class="block text-[10px] text-gray-400 font-mono font-normal">{{ $req->farmer->farmer_code }}</span>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $req->fertilizer->name }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-700">{{ $req->requested_quantity }} {{ $req->fertilizer->unit }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-700">
                                {{ $req->approved_quantity ? $req->approved_quantity . ' ' . $req->fertilizer->unit : '-' }}
                            </td>
                            <td class="py-3 px-4 text-gray-500">{{ $req->request_date ? $req->request_date->format('M d, Y') : 'N/A' }}</td>
                            <td class="py-3 px-4">
                                @if($req->status === 'Pending')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-100 text-amber-800">Pending</span>
                                @elseif($req->status === 'Under Review')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-blue-100 text-blue-800">Under Review</span>
                                @elseif($req->status === 'Approved' || $req->status === 'Ready for Release')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800">{{ $req->status }}</span>
                                @elseif($req->status === 'Released')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-purple-100 text-purple-800"><i class="fa-solid fa-circle-check text-[9px] mr-0.5"></i> Released</span>
                                @else
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-red-100 text-red-800">{{ $req->status }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <!-- Review / Approve Action -->
                                @if($req->status !== 'Released' && $req->status !== 'Rejected')
                                    <button onclick="openReviewModal({{ $req->id }}, '{{ $req->request_code }}', '{{ $req->farmer->full_name }}', '{{ $req->fertilizer->name }}', {{ $req->requested_quantity }}, '{{ $req->status }}')" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded font-semibold text-[11px] transition">
                                        Review/Approve
                                    </button>
                                @endif

                                <!-- Warehouse Release Action -->
                                @if(($req->status === 'Approved' || $req->status === 'Ready for Release') && $req->status !== 'Released')
                                    <form method="POST" action="{{ route('fertilizer-distributions.store') }}" class="inline-block" onsubmit="return confirm('Release fertilizer and automatically deduct inventory stock?');">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $req->id }}">
                                        <input type="hidden" name="quantity" value="{{ $req->approved_quantity ?? $req->requested_quantity }}">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded font-bold text-[11px] transition">
                                            <i class="fa-solid fa-truck-ramp-box mr-1"></i> Release Stock
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 italic">No fertilizer assistance requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Submit Request -->
<div id="addRequestModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Submit Fertilizer Assistance Request</h3>
            <button onclick="document.getElementById('addRequestModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('fertilizer-requests.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Select Farmer *</label>
                <select name="farmer_id" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Choose Planter --</option>
                    @foreach($farmers as $farmer)
                        <option value="{{ $farmer->id }}">{{ $farmer->farmer_code }} - {{ $farmer->full_name }} ({{ $farmer->municipality }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Fertilizer Formula *</label>
                <select name="fertilizer_id" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Choose Fertilizer Type --</option>
                    @foreach($fertilizers as $fert)
                        <option value="{{ $fert->id }}">{{ $fert->name }} (Unit: {{ $fert->unit }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Requested Quantity (Bags/Units) *</label>
                <input type="number" step="0.5" name="requested_quantity" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. 25">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Remarks / Farm Plot Context</label>
                <textarea name="remarks" rows="2" class="w-full text-xs p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="Application for crop year vegetative cycle..."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addRequestModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Review / Approve Request -->
<div id="reviewModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-blue-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Review Fertilizer Assistance Request</h3>
            <button onclick="document.getElementById('reviewModal').classList.add('hidden')" class="text-white hover:text-blue-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="reviewForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-xs space-y-1">
                <p>Request Code: <strong id="modalReqCode" class="text-emerald-800 font-mono"></strong></p>
                <p>Farmer: <strong id="modalFarmer"></strong></p>
                <p>Fertilizer: <strong id="modalFertilizer"></strong></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Set Workflow Status *</label>
                <select name="status" id="modalStatus" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                    <option value="Under Review">Under Review</option>
                    <option value="Approved">Approved</option>
                    <option value="Ready for Release">Ready for Release</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Approved Quantity (Bags/Units)</label>
                <input type="number" step="0.5" name="approved_quantity" id="modalApprovedQty" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Review Remarks</label>
                <textarea name="remarks" rows="2" class="w-full text-xs p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" placeholder="Approved by agronomy team for release..."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('reviewModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-700 text-white text-xs font-semibold rounded-lg shadow">Save Decision</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReviewModal(id, code, farmer, fertilizer, qty, status) {
        document.getElementById('reviewForm').action = '/fertilizer-requests/' + id + '/status';
        document.getElementById('modalReqCode').innerText = code;
        document.getElementById('modalFarmer').innerText = farmer;
        document.getElementById('modalFertilizer').innerText = fertilizer;
        document.getElementById('modalApprovedQty').value = qty;
        document.getElementById('modalStatus').value = status;
        document.getElementById('reviewModal').classList.remove('hidden');
    }
</script>
@endsection
