<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Room Management</h2>
                        
                        <!-- Search -->
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <input wire:model.live="search" 
                                       type="text" 
                                       placeholder="Search rooms..." 
                                       class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-10">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Rooms List -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">All Rooms</h3>
                            <div class="space-y-3">
                                @foreach($rooms as $room)
                                    <button wire:click="selectRoom({{ $room->id }})"
                                            class="w-full text-left p-4 rounded-lg border transition-colors {{ $selectedRoom == $room->id ? 'bg-blue-100 border-blue-300' : 'bg-gray-50 border-gray-200 hover:bg-gray-100' }}">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-medium text-lg">{{ $room->name }}</div>
                                                <div class="text-sm text-gray-600">{{ $room->description }}</div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    Building: {{ $room->building->name }} | 
                                                    Floor: {{ $room->floor }} | 
                                                    Room: {{ $room->room_number }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $room->cctvs->count() }} CCTV
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $room->cctvs->where('status', 'online')->count() }} Online
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- CCTV Details -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">CCTV Cameras</h3>
                            @if($selectedRoom && $cctvs->count() > 0)
                                <div class="space-y-3">
                                    @foreach($cctvs as $cctv)
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <div class="flex items-center justify-between mb-3">
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
                                            
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="font-medium">IP Address:</span>
                                                    <div class="text-gray-600">{{ $cctv->ip_address }}</div>
                                                </div>
                                                <div>
                                                    <span class="font-medium">Model:</span>
                                                    <div class="text-gray-600">{{ $cctv->model ?: 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <span class="font-medium">Manufacturer:</span>
                                                    <div class="text-gray-600">{{ $cctv->manufacturer ?: 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <span class="font-medium">Last Online:</span>
                                                    <div class="text-gray-600">
                                                        {{ $cctv->last_online ? $cctv->last_online->format('d/m/Y H:i') : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($cctv->status === 'online')
                                                <div class="mt-3">
                                                    <a href="{{ route('user.cctv.stream', $cctv) }}" 
                                                       target="_blank"
                                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                        View Live Stream
                                                    </a>
                                                </div>
                                            @endif
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