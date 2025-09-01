import './bootstrap';
import Alpine from 'alpinejs';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize Livewire
Livewire.start();