<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Maps View</h2>
                        
                        <!-- Status Filter -->
                        <div class="flex items-center space-x-4">
                            <label for="status-filter" class="text-sm font-medium text-gray-700">Filter by Status:</label>
                            <select wire:model.live="selectedStatus" 
                                    id="status-filter"
                                    class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="map" class="w-full h-96 rounded-lg border border-gray-300"></div>

                    <!-- Legend -->
                    <div class="mt-4 flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Buildings</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Online CCTV</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Offline CCTV</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Maintenance CCTV</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            let map;
            let markers = [];

            function initMap() {
                if (map) {
                    map.remove();
                }

                // Initialize map centered on Indonesia
                map = L.map('map').setView([-6.2088, 106.8456], 10);

                // Add OpenStreetMap layer
                const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                });

                // Add satellite layer
                const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '© Esri'
                });

                // Add layer control
                const baseMaps = {
                    "OpenStreetMap": osmLayer,
                    "Satellite": satelliteLayer
                };

                L.control.layers(baseMaps).addTo(map);

                // Add default layer
                osmLayer.addTo(map);

                // Add markers for buildings
                @foreach($buildings as $building)
                    const buildingMarker = L.marker([{{ $building->latitude ?? -6.2088 }}, {{ $building->longitude ?? 106.8456 }}], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<div style="background-color: #3B82F6; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    }).addTo(map);

                    buildingMarker.bindPopup(`
                        <div class="p-2">
                            <h3 class="font-bold text-lg">{{ $building->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $building->address }}</p>
                        </div>
                    `);

                    markers.push(buildingMarker);
                @endforeach

                // Add markers for CCTV cameras
                @foreach($cctvs as $cctv)
                    @php
                        $markerColor = $cctv->status === 'online' ? '#10B981' : ($cctv->status === 'offline' ? '#EF4444' : '#F59E0B');
                    @endphp
                    
                    const cctvMarker = L.marker([{{ $cctv->latitude ?? -6.2088 }}, {{ $cctv->longitude ?? 106.8456 }}], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<div style="background-color: {{ $markerColor }}; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white;"></div>',
                            iconSize: [16, 16],
                            iconAnchor: [8, 8]
                        })
                    }).addTo(map);

                    cctvMarker.bindPopup(`
                        <div class="p-2">
                            <h3 class="font-bold text-lg">{{ $cctv->name }}</h3>
                            <p class="text-sm text-gray-600">IP: {{ $cctv->ip_address }}</p>
                            <p class="text-sm text-gray-600">Building: {{ $cctv->building->name }}</p>
                            <p class="text-sm text-gray-600">Room: {{ $cctv->room->name }}</p>
                            <p class="text-sm">
                                Status: 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $cctv->status === 'online' ? 'bg-green-100 text-green-800' : 
                                       ($cctv->status === 'offline' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($cctv->status) }}
                                </span>
                            </p>
                        </div>
                    `);

                    markers.push(cctvMarker);
                @endforeach
            }

            // Initialize map when component loads
            initMap();

            // Listen for Livewire updates
            Livewire.on('data-updated', () => {
                // Clear existing markers
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                // Reinitialize map with new data
                initMap();
            });
        });
    </script>
    @endpush
</div>