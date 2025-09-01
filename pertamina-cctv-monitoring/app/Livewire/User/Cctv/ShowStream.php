<?php

namespace App\Livewire\User\Cctv;

use Livewire\Component;
use App\Models\Cctv;

class ShowStream extends Component
{
    public Cctv $cctv;
    public $streamUrl;

    public function mount($cctv)
    {
        $this->cctv = $cctv;
        $this->streamUrl = $this->cctv->stream_url;
    }

    public function render()
    {
        return view('livewire.user.cctv.show-stream')->layout('layouts.app');
    }
}