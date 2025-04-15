<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    /**
     * Mengambil data pengguna untuk diekspor.
     */
    public function collection()
    {
        return User::select('id', 'name', 'email', 'role', 'created_at')->get();
    }

    /**
     * Memformat data untuk setiap baris.
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role,
            $user->created_at->format('Y-m-d H:i:s'), // Format tanggal
        ];
    }

    /**
     * Menentukan header untuk file Excel.
     */
    public function headings(): array
    {
        return [
            [
                'Daftar Pengguna', '', '', '', '' // Merge ini dilakukan via AfterSheet
            ],
            [
                'ID',
                'Nama',
                'Email',
                'Role',
                'Tanggal Dibuat',
            ]
        ];
    }

    /**
     * Menambahkan styling ke worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Styling judul di baris 1
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Styling header di baris ke-2
        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
        ]);

        $sheet->getRowDimension(2)->setRowHeight(25);

        // Atur alignment untuk semua kolom agar berada di tengah
        $sheet->getStyle('A:E')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }

    /**
     * Menentukan lebar kolom.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 30,
            'D' => 15,
            'E' => 25,
        ];
    }

    /**
     * Mendaftarkan event untuk worksheet.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Merge judul di baris 1
                $event->sheet->mergeCells('A1:E1');
            },
        ];
    }
}
