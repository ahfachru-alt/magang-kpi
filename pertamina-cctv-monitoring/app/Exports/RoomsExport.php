<?php

namespace App\Exports;

use App\Models\Room;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RoomsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Room::with(['building', 'cctvs'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Room Name',
            'Building',
            'Floor',
            'Total CCTVs',
            'Online CCTVs',
            'Offline CCTVs',
            'Maintenance CCTVs',
            'Created At',
        ];
    }

    /**
     * @param Room $room
     */
    public function map($room): array
    {
        return [
            $room->id,
            $room->name,
            $room->building->name,
            $room->floor,
            $room->cctvs->count(),
            $room->cctvs->where('status', 'online')->count(),
            $room->cctvs->where('status', 'offline')->count(),
            $room->cctvs->where('status', 'maintenance')->count(),
            $room->created_at->format('Y-m-d H:i:s'),
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