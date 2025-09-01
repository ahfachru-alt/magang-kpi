<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6">Location Management</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Buildings Section -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Buildings</h3>
                                <div class="space-y-2">
                                    @foreach($buildings as $building)
                                        <button wire:click="selectBuilding({{ $building->id }})"
                                                class="w-full text-left p-3 rounded-lg border transition-colors {{ $selectedBuilding == $building->id ? 'bg-blue-100 border-blue-300' : 'bg-white border-gray-200 hover:bg-gray-50' }}">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="font-medium">{{ $building->name }}</div>
                                                    <div class="text-sm text-gray-600">{{ $building->description }}</div>
                                                    <div class="text-sm text-gray-500 mt-1">
                                                        Rooms: {{ $building->rooms->count() }} | 
                                                        CCTV: {{ $building->cctvs->count() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Rooms Section -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Rooms</h3>
                                @if($selectedBuilding)
                                    <div class="space-y-2">
                                        @foreach($rooms as $room)
                                            <button wire:click="selectRoom({{ $room->id }})"
                                                    class="w-full text-left p-3 rounded-lg border transition-colors {{ $selectedRoom == $room->id ? 'bg-blue-100 border-blue-300' : 'bg-white border-gray-200 hover:bg-gray-50' }}">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <div class="font-medium">{{ $room->name }}</div>
                                                        <div class="text-sm text-gray-600">{{ $room->description }}</div>
                                                        <div class="text-sm text-gray-500 mt-1">
                                                            Floor: {{ $room->floor }} | 
                                                            CCTV: {{ $room->cctvs->count() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-gray-500 text-center py-8">
                                        Select a building to view rooms
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- CCTV Section -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">CCTV Cameras</h3>
                                @if($selectedRoom && $cctvs->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($cctvs as $cctv)
                                            <div class="bg-white p-3 rounded-lg border border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <div class="font-medium text-lg">{{ $cctv->name }}</div>
                                                        <div class="text-sm text-gray-600">{{ $cctv->description }}</div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        @if($cctv->status === 'online')
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Online
                                                            </span>
                                                        @elseif($cctv->status === 'offline')
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                Offline
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                Maintenance
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-4 text-sm mt-3">
                                                    <div>
                                                        <span class="font-medium">IP Address:</span>
                                                        <div class="text-gray-600 font-mono">{{ $cctv->ip_address }}</div>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Model:</span>
                                                        <div class="text-gray-600">{{ $cctv->model ?: 'N/A' }}</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-3 flex space-x-2">
                                                    <button class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                                                    <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($selectedRoom)
                                    <div class="text-gray-500 text-center py-8">
                                        No CCTV cameras found in this room
                                    </div>
                                @else
                                    <div class="text-gray-500 text-center py-8">
                                        Select a room to view CCTV cameras
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>