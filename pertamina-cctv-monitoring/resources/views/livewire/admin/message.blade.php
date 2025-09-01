<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6">Messages</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <!-- User List -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Users</h3>
                                <div class="space-y-2">
                                    @foreach($users as $user)
                                        <button wire:click="selectUser({{ $user->id }})"
                                                class="w-full text-left p-3 rounded-lg border transition-colors {{ $selectedUser == $user->id ? 'bg-blue-100 border-blue-300' : 'bg-white border-gray-200 hover:bg-gray-50' }}">
                                            <div class="font-medium">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $user->email }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="lg:col-span-3">
                            <div class="bg-gray-50 p-4 rounded-lg h-96 flex flex-col">
                                @if($selectedUser)
                                    <!-- Messages List -->
                                    <div class="flex-1 overflow-y-auto mb-4 space-y-3">
                                        @foreach($messages as $message)
                                            @if(($message->from_type === 'App\Models\Admin' && $message->from_id === auth()->guard('admin')->id() && $message->to_id === $selectedUser) ||
                                                 ($message->to_type === 'App\Models\Admin' && $message->to_id === auth()->guard('admin')->id() && $message->from_id === $selectedUser))
                                                <div class="flex {{ $message->from_type === 'App\Models\Admin' ? 'justify-end' : 'justify-start' }}">
                                                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->from_type === 'App\Models\Admin' ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                                                        <div class="text-sm">{{ $message->message }}</div>
                                                        <div class="text-xs mt-1 {{ $message->from_type === 'App\Models\Admin' ? 'text-blue-100' : 'text-gray-500' }}">
                                                            {{ $message->created_at->format('d/m/Y H:i') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <!-- Send Message -->
                                    <div class="flex space-x-2">
                                        <input wire:model="newMessage" 
                                               type="text" 
                                               placeholder="Type your message..." 
                                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                               wire:keydown.enter="sendMessage">
                                        <button wire:click="sendMessage" 
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            Send
                                        </button>
                                    </div>
                                @else
                                    <div class="flex-1 flex items-center justify-center">
                                        <div class="text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium">Select a user to start messaging</h3>
                                            <p class="text-sm">Choose a user from the list to begin a conversation.</p>
                                        </div>
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