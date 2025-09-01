<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">CCTV Management</h2>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input wire:model.live="search" 
                                   type="text" 
                                   placeholder="Search CCTV..." 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Building Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Building</label>
                            <select wire:model.live="selectedBuilding" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Buildings</option>
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Room Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                            <select wire:model.live="selectedRoom" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Rooms</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model.live="selectedStatus" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $cctvs->count() }}</div>
                            <div class="text-sm text-blue-600">Total CCTV</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $cctvs->where('status', 'online')->count() }}</div>
                            <div class="text-sm text-green-600">Online</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $cctvs->where('status', 'offline')->count() }}</div>
                            <div class="text-sm text-red-600">Offline</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $cctvs->where('status', 'maintenance')->count() }}</div>
                            <div class="text-sm text-yellow-600">Maintenance</div>
                        </div>
                    </div>

                    <!-- CCTV Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($cctvs as $cctv)
                            <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <!-- Header -->
                                <div class="p-4 border-b border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-lg">{{ $cctv->name }}</h3>
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
                                    <p class="text-sm text-gray-600">{{ $cctv->description ?: 'No description' }}</p>
                                </div>

                                <!-- Details -->
                                <div class="p-4">
                                    <div class="space-y-2 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Location:</span>
                                            <div class="text-gray-600">{{ $cctv->room->building->name }} - {{ $cctv->room->name }}</div>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">IP Address:</span>
                                            <div class="text-gray-600 font-mono">{{ $cctv->ip_address }}</div>
                                        </div>
                                        @if($cctv->model)
                                            <div>
                                                <span class="font-medium text-gray-700">Model:</span>
                                                <div class="text-gray-600">{{ $cctv->model }}</div>
                                            </div>
                                        @endif
                                        @if($cctv->manufacturer)
                                            <div>
                                                <span class="font-medium text-gray-700">Manufacturer:</span>
                                                <div class="text-gray-600">{{ $cctv->manufacturer }}</div>
                                            </div>
                                        @endif
                                        @if($cctv->last_online)
                                            <div>
                                                <span class="font-medium text-gray-700">Last Online:</span>
                                                <div class="text-gray-600">{{ $cctv->last_online->format('d/m/Y H:i') }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-4">
                                        @if($cctv->status === 'online')
                                            <a href="{{ route('user.cctv.stream', $cctv) }}" 
                                               target="_blank"
                                               class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                View Stream
                                            </a>
                                        @else
                                            <button disabled class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-400 text-white text-sm rounded-md cursor-not-allowed">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Offline
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($cctvs->count() === 0)
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No CCTV cameras found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>