<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BuildingsExport;
use App\Exports\RoomsExport;
use App\Exports\CctvsExport;

class ExportController extends Controller
{
    public function buildings()
    {
        return Excel::download(new BuildingsExport, 'pertamina-buildings-' . date('Y-m-d') . '.xlsx');
    }

    public function rooms()
    {
        return Excel::download(new RoomsExport, 'pertamina-rooms-' . date('Y-m-d') . '.xlsx');
    }

    public function cctvs()
    {
        return Excel::download(new CctvsExport, 'pertamina-cctvs-' . date('Y-m-d') . '.xlsx');
    }

    public function all()
    {
        // Create a multi-sheet Excel file
        return Excel::download(new class implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            public function sheets(): array
            {
                return [
                    'Buildings' => new BuildingsExport,
                    'Rooms' => new RoomsExport,
                    'CCTVs' => new CctvsExport,
                ];
            }
        }, 'pertamina-cctv-monitoring-' . date('Y-m-d') . '.xlsx');
    }
}
