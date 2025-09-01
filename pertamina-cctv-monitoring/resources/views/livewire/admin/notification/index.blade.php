<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Notifications</h2>
                        @if($unreadCount > 0)
                            <button wire:click="markAllAsRead" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Mark All as Read
                            </button>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="border border-gray-200 rounded-lg p-4 {{ !$notification->is_read ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="font-semibold text-lg">{{ $notification->title }}</h3>
                                            @if(!$notification->is_read)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    New
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                {{ $notification->type === 'success' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $notification->type === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $notification->type === 'error' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $notification->type === 'info' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ ucfirst($notification->type) }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 mb-2">{{ $notification->body }}</p>
                                        <div class="text-sm text-gray-500">
                                            {{ $notification->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    @if(!$notification->is_read)
                                        <button wire:click="markAsRead({{ $notification->id }})" 
                                                class="ml-4 text-blue-600 hover:text-blue-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($notifications->count() === 0)
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 004 6v10a4 4 0 004 4h10a4 4 0 004-4V6a4 4 0 00-4-4H8a4 4 0 00-2.81 1.19z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                            <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>