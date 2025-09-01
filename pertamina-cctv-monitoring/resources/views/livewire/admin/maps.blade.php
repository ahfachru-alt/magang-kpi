<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Interactive Maps</h2>
                        
                        <!-- Status Filter -->
                        <div class="flex items-center space-x-4">
                            <label class="text-sm font-medium text-gray-700">Filter Status:</label>
                            <select wire:model.live="selectedStatus" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="all">All CCTV</option>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="map" class="w-full h-96 rounded-lg border border-gray-300"></div>

                    <!-- Legend -->
                    <div class="mt-4 flex items-center space-x-6">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Online CCTV</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Offline CCTV</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
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

                // Initialize map centered on Pertamina RU VI Balongan
                map = L.map('map').setView([-6.2088, 106.8456], 15);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Add satellite layer
                const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '© Esri'
                });

                // Layer control
                const baseMaps = {
                    "Street": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
                    "Satellite": satelliteLayer
                };

                L.control.layers(baseMaps).addTo(map);

                // Add building markers
                @foreach($buildings as $building)
                    const buildingMarker = L.marker([{{ $building->latitude }}, {{ $building->longitude }}])
                        .bindPopup(`
                            <div class="p-2">
                                <h3 class="font-bold text-lg">{{ $building->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $building->description }}</p>
                                <p class="text-xs text-gray-500 mt-1">CCTV: {{ $building->cctvs->count() }}</p>
                            </div>
                        `)
                        .addTo(map);
                    markers.push(buildingMarker);
                @endforeach

                // Add CCTV markers
                @foreach($cctvs as $cctv)
                    @if($cctv->room && $cctv->room->building)
                        const cctvColor = '{{ $cctv->status }}' === 'online' ? 'green' : 
                                        '{{ $cctv->status }}' === 'offline' ? 'red' : 'yellow';
                        
                        const cctvMarker = L.circleMarker([{{ $cctv->room->building->latitude }}, {{ $cctv->room->building->longitude }}], {
                            radius: 8,
                            fillColor: cctvColor,
                            color: cctvColor,
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).bindPopup(`
                            <div class="p-2">
                                <h3 class="font-bold">{{ $cctv->name }}</h3>
                                <p class="text-sm">Status: <span class="font-semibold text-${cctvColor}-600">{{ ucfirst($cctv->status) }}</span></p>
                                <p class="text-sm">Room: {{ $cctv->room->name }}</p>
                                <p class="text-sm">Building: {{ $cctv->room->building->name }}</p>
                            </div>
                        `).addTo(map);
                        markers.push(cctvMarker);
                    @endif
                @endforeach
            }

            // Initialize map when component loads
            initMap();

            // Listen for Livewire updates
            Livewire.on('data-updated', () => {
                initMap();
            });
        });
    </script>
    @endpush
</div>