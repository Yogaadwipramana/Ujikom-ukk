<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    /**
     * Mengambil data produk untuk diekspor.
     */
    public function collection()
    {
        return Product::select('id', 'name', 'price', 'stock', 'created_at')->get();
    }

    /**
     * Memformat data untuk setiap baris.
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            'Rp ' . number_format($product->price, 0, ',', '.'),
            $product->stock,
            $product->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Menentukan header untuk file Excel.
     */
    public function headings(): array
    {
        return [
            [
                'Daftar Produk Toko Jaya Abadi', '', '', '', ''
            ],
            [
                'ID',
                'Nama Produk',
                'Harga',
                'Stok',
                'Tanggal Dibuat',
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
        ]);

        $sheet->getRowDimension(2)->setRowHeight(25);

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
            'B' => 30,
            'C' => 20,
            'D' => 10,
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
                $event->sheet->mergeCells('A1:E1');
            },
        ];
    }
}
