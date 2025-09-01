<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold">{{ $notification->title }}</h2>
                            <div class="flex items-center space-x-2 mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $notification->type === 'success' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $notification->type === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $notification->type === 'error' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $notification->type === 'info' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($notification->type) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $notification->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('admin.notification.index') }}" 
                           class="text-blue-600 hover:text-blue-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Content -->
                    <div class="prose max-w-none">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="text-lg text-gray-700 leading-relaxed">{{ $notification->body }}</p>
                        </div>

                        @if($notification->data)
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold mb-3">Additional Information</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex justify-between items-center">
                        <a href="{{ route('admin.notification.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>