<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold">{{ $cctv->name }}</h2>
                            <p class="text-gray-600">{{ $cctv->room->building->name }} - {{ $cctv->room->name }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if($cctv->status === 'online')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Online
                                </span>
                            @elseif($cctv->status === 'offline')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Offline
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Maintenance
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- CCTV Details -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div class="lg:col-span-2">
                            <!-- Video Stream -->
                            <div class="bg-black rounded-lg overflow-hidden">
                                @if($cctv->status === 'online')
                                    <video id="videoPlayer" class="w-full h-96" controls autoplay muted>
                                        <source src="{{ $streamUrl }}" type="application/x-mpegURL">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <div class="w-full h-96 flex items-center justify-center bg-gray-900">
                                        <div class="text-center text-white">
                                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium">Camera Offline</h3>
                                            <p class="text-gray-400">This camera is currently {{ $cctv->status }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- CCTV Information -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Camera Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Name:</span>
                                        <div class="text-gray-900">{{ $cctv->name }}</div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Description:</span>
                                        <div class="text-gray-900">{{ $cctv->description ?: 'No description' }}</div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Location:</span>
                                        <div class="text-gray-900">{{ $cctv->room->building->name }}</div>
                                        <div class="text-gray-600">{{ $cctv->room->name }}</div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">IP Address:</span>
                                        <div class="text-gray-900 font-mono">{{ $cctv->ip_address }}</div>
                                    </div>
                                    @if($cctv->model)
                                        <div>
                                            <span class="font-medium text-gray-700">Model:</span>
                                            <div class="text-gray-900">{{ $cctv->model }}</div>
                                        </div>
                                    @endif
                                    @if($cctv->manufacturer)
                                        <div>
                                            <span class="font-medium text-gray-700">Manufacturer:</span>
                                            <div class="text-gray-900">{{ $cctv->manufacturer }}</div>
                                        </div>
                                    @endif
                                    @if($cctv->last_online)
                                        <div>
                                            <span class="font-medium text-gray-700">Last Online:</span>
                                            <div class="text-gray-900">{{ $cctv->last_online->format('d/m/Y H:i') }}</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Controls -->
                                <div class="mt-6 space-y-2">
                                    @if($cctv->status === 'online')
                                        <button onclick="toggleFullscreen()" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                            </svg>
                                            Fullscreen
                                        </button>
                                        <button onclick="refreshStream()" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Refresh Stream
                                        </button>
                                    @endif
                                    <a href="{{ route('user.cctv.index') }}" 
                                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-500 text-white text-sm rounded-md hover:bg-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                        </svg>
                                        Back to CCTV List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleFullscreen() {
            const video = document.getElementById('videoPlayer');
            if (video.requestFullscreen) {
                video.requestFullscreen();
            } else if (video.webkitRequestFullscreen) {
                video.webkitRequestFullscreen();
            } else if (video.msRequestFullscreen) {
                video.msRequestFullscreen();
            }
        }

        function refreshStream() {
            const video = document.getElementById('videoPlayer');
            video.load();
        }
    </script>
    @endpush
</div>