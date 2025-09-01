<?php

namespace App\Exports;

use App\Models\Building;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BuildingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Building::with('rooms.cctvs')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Building Name',
            'Address',
            'Latitude',
            'Longitude',
            'Total Rooms',
            'Total CCTVs',
            'Online CCTVs',
            'Offline CCTVs',
            'Maintenance CCTVs',
            'Created At',
        ];
    }

    /**
     * @param Building $building
     */
    public function map($building): array
    {
        $totalCctvs = $building->rooms->sum(function ($room) {
            return $room->cctvs->count();
        });

        $onlineCctvs = $building->rooms->sum(function ($room) {
            return $room->cctvs->where('status', 'online')->count();
        });

        $offlineCctvs = $building->rooms->sum(function ($room) {
            return $room->cctvs->where('status', 'offline')->count();
        });

        $maintenanceCctvs = $building->rooms->sum(function ($room) {
            return $room->cctvs->where('status', 'maintenance')->count();
        });

        return [
            $building->id,
            $building->name,
            $building->address,
            $building->latitude ?? 'N/A',
            $building->longitude ?? 'N/A',
            $building->rooms->count(),
            $totalCctvs,
            $onlineCctvs,
            $offlineCctvs,
            $maintenanceCctvs,
            $building->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}