<?php

namespace App\Exports;

use App\Models\Cctv;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CctvsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Cctv::with(['building', 'room'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'CCTV Name',
            'IP Address',
            'Building',
            'Room',
            'Status',
            'Latitude',
            'Longitude',
            'Stream URL',
            'Last Online',
            'Created At',
        ];
    }

    /**
     * @param Cctv $cctv
     */
    public function map($cctv): array
    {
        return [
            $cctv->id,
            $cctv->name,
            $cctv->ip_address,
            $cctv->building->name,
            $cctv->room->name,
            ucfirst($cctv->status),
            $cctv->latitude ?? 'N/A',
            $cctv->longitude ?? 'N/A',
            $cctv->stream_url ?? 'N/A',
            $cctv->last_online_at ? $cctv->last_online_at->format('Y-m-d H:i:s') : 'N/A',
            $cctv->created_at->format('Y-m-d H:i:s'),
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