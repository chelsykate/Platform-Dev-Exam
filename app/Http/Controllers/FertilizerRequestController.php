<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Farmer;
use App\Models\FarmerActivity;
use App\Models\Fertilizer;
use App\Models\FertilizerRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FertilizerRequestController extends Controller
{
    /**
     * Display listing of fertilizer requests.
     */
    public function index(Request $request)
    {
        $query = FertilizerRequest::with(['farmer', 'fertilizer', 'distribution'])->latest('request_date');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhereHas('farmer', function ($fq) use ($search) {
                      $fq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('farmer_code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $requests = $query->paginate(10)->withQueryString();
        $farmers = Farmer::where('status', 'Active')->get();
        $fertilizers = Fertilizer::where('status', 'Active')->get();

        return view('fertilizers.requests', compact('requests', 'farmers', 'fertilizers'));
    }

    /**
     * Store a new fertilizer request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'fertilizer_id' => ['required', 'exists:fertilizers,id'],
            'requested_quantity' => ['required', 'numeric', 'min:1'],
            'remarks' => ['nullable', 'string'],
        ]);

        $count = FertilizerRequest::count() + 1;
        $validated['request_code'] = 'REQ-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['request_date'] = now();
        $validated['status'] = 'Pending';

        $req = FertilizerRequest::create($validated);
        $farmer = Farmer::findOrFail($req->farmer_id);
        $fert = Fertilizer::findOrFail($req->fertilizer_id);

        // Activity log
        FarmerActivity::create([
            'farmer_id' => $farmer->id,
            'activity_type' => 'Fertilizer Request',
            'description' => "Submitted fertilizer assistance request ({$req->request_code}) for {$req->requested_quantity} {$fert->unit} of {$fert->name}.",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'Farmer Portal',
        ]);

        // System Notification
        AppNotification::send(null, $farmer->id, 'Fertilizer Request Submitted', "Request {$req->request_code} for {$req->requested_quantity} {$fert->unit} of {$fert->name} was submitted.", 'info');

        AuditLog::log('Submitted fertilizer request', 'Fertilizer Automation', $req->id, null, $req->toArray());

        return back()->with('success', "Fertilizer assistance request '{$req->request_code}' submitted successfully.");
    }

    /**
     * Process review and approval of fertilizer request.
     */
    public function updateStatus(Request $request, FertilizerRequest $fertilizerRequest)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Under Review', 'Approved', 'Rejected', 'Ready for Release', 'Cancelled'])],
            'approved_quantity' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        $oldStatus = $fertilizerRequest->status;
        $newStatus = $validated['status'];

        $updateData = [
            'status' => $newStatus,
            'remarks' => $validated['remarks'] ?? $fertilizerRequest->remarks,
        ];

        if ($newStatus === 'Approved' || $newStatus === 'Ready for Release') {
            $updateData['approved_quantity'] = $validated['approved_quantity'] ?? $fertilizerRequest->requested_quantity;
            $updateData['approval_date'] = now();
            $updateData['approved_by'] = auth()->user()->name ?? 'Employee';
        }

        $fertilizerRequest->update($updateData);
        $farmer = $fertilizerRequest->farmer;
        $fert = $fertilizerRequest->fertilizer;

        // Activity Log
        FarmerActivity::create([
            'farmer_id' => $farmer->id,
            'activity_type' => 'Fertilizer Status Update',
            'description' => "Fertilizer request {$fertilizerRequest->request_code} updated to '{$newStatus}'.",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        // System Notification
        $notifType = ($newStatus === 'Approved' || $newStatus === 'Ready for Release') ? 'success' : (($newStatus === 'Rejected') ? 'alert' : 'info');
        AppNotification::send(null, $farmer->id, "Fertilizer Request {$newStatus}", "Your fertilizer request {$fertilizerRequest->request_code} is now {$newStatus}.", $notifType);

        AuditLog::log("Updated fertilizer request status to {$newStatus}", 'Fertilizer Automation', $fertilizerRequest->id, ['status' => $oldStatus], $updateData);

        return back()->with('success', "Request '{$fertilizerRequest->request_code}' status updated to '{$newStatus}'.");
    }
}
