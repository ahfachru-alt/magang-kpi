<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cctv;
use App\Models\Building;
use App\Models\Room;
use App\Services\CctvMonitoringService;
use App\Http\Resources\CctvResource;
use App\Http\Resources\BuildingResource;
use App\Http\Resources\RoomResource;

class CctvController extends Controller
{
    protected $monitoringService;

    public function __construct(CctvMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cctv::with(['building', 'room']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by building
        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        // Filter by room
        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Search by name or IP
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $cctvs = $query->paginate($request->get('per_page', 20));

        return CctvResource::collection($cctvs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:cctvs',
            'building_id' => 'required|exists:buildings,id',
            'room_id' => 'required|exists:rooms,id',
            'status' => 'in:online,offline,maintenance',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $cctv = Cctv::create($request->all());

        return new CctvResource($cctv);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cctv = Cctv::with(['building', 'room'])->findOrFail($id);
        return new CctvResource($cctv);
    }

    /**
     * Update the specified resource in storage.
     * @param  int  $id
     */
    public function update(Request $request, string $id)
    {
        $cctv = Cctv::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'ip_address' => 'sometimes|ip|unique:cctvs,ip_address,' . $id,
            'building_id' => 'sometimes|exists:buildings,id',
            'status' => 'sometimes|in:online,offline,maintenance',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $cctv->update($request->all());

        return new CctvResource($cctv);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cctv = Cctv::findOrFail($id);
        $cctv->delete();

        return response()->json(['message' => 'CCTV deleted successfully']);
    }

    /**
     * Get CCTV status summary
     */
    public function statusSummary()
    {
        $summary = $this->monitoringService->getStatusSummary();
        
        return response()->json([
            'data' => $summary,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get building status summary
     */
    public function buildingStatus()
    {
        $buildings = $this->monitoringService->getBuildingStatusSummary();
        
        return response()->json([
            'data' => $buildings,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get room status summary
     */
    public function roomStatus(Request $request)
    {
        $buildingId = $request->get('building_id');
        $rooms = $this->monitoringService->getRoomStatusSummary($buildingId);
        
        return response()->json([
            'data' => $rooms,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get CCTV alerts
     */
    public function alerts()
    {
        $alerts = $this->monitoringService->getCctvAlerts();
        
        return response()->json([
            'data' => $alerts,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Start maintenance mode
     */
    public function startMaintenance(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $success = $this->monitoringService->startMaintenance($id, $request->reason);

        if ($success) {
            return response()->json(['message' => 'Maintenance mode started successfully']);
        }

        return response()->json(['message' => 'Failed to start maintenance mode'], 500);
    }

    /**
     * End maintenance mode
     */
    public function endMaintenance($id)
    {
        $success = $this->monitoringService->endMaintenance($id);

        if ($success) {
            return response()->json(['message' => 'Maintenance mode ended successfully']);
        }

        return response()->json(['message' => 'Failed to end maintenance mode'], 500);
    }

    /**
     * Get performance metrics
     */
    public function performance()
    {
        $metrics = $this->monitoringService->getPerformanceMetrics();
        
        return response()->json([
            'data' => $metrics,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
