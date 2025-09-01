<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Create New Location</h2>
                        <a href="{{ route('admin.location.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Locations
                        </a>
                    </div>

                    <form wire:submit="save" class="space-y-8">
                        <!-- Building Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Building Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="building_name" class="block text-sm font-medium text-gray-700">Building Name</label>
                                    <input wire:model="building_name" 
                                           type="text" 
                                           id="building_name"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('building_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="building_address" class="block text-sm font-medium text-gray-700">Building Address</label>
                                    <input wire:model="building_address" 
                                           type="text" 
                                           id="building_address"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('building_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Room Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Room Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="room_name" class="block text-sm font-medium text-gray-700">Room Name</label>
                                    <input wire:model="room_name" 
                                           type="text" 
                                           id="room_name"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('room_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="room_floor" class="block text-sm font-medium text-gray-700">Floor</label>
                                    <input wire:model="room_floor" 
                                           type="text" 
                                           id="room_floor"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('room_floor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- CCTV Section -->
                        <div class="pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">CCTV Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="cctv_name" class="block text-sm font-medium text-gray-700">CCTV Name</label>
                                    <input wire:model="cctv_name" 
                                           type="text" 
                                           id="cctv_name"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('cctv_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="cctv_ip_address" class="block text-sm font-medium text-gray-700">IP Address</label>
                                    <input wire:model="cctv_ip_address" 
                                           type="text" 
                                           id="cctv_ip_address"
                                           placeholder="192.168.1.100"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('cctv_ip_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="cctv_status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="cctv_status" 
                                            id="cctv_status"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="online">Online</option>
                                        <option value="offline">Offline</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                    @error('cctv_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Location
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>