<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    public function collection()
    {
        return Order::with('member')->get();
    }

    public function map($order): array
    {
        $rows = [];
        $productIds = json_decode($order->products_id, true) ?? [];
        $quantities = json_decode($order->total_barang, true) ?? [];
        $prices = json_decode($order->total_harga, true) ?? [];

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($productIds as $index => $productId) {
            $product = $products[$productId] ?? null;

            $rows[] = [
                $index === 0 ? ($order->member->name ?? '-') : '', // Nama Pelanggan hanya di baris pertama
                $index === 0 ? ($order->member->no_telepon ?? '-') : '', // No Telepon hanya di baris pertama
                $index === 0 ? ($order->member->point ?? 0) : '', // Point hanya di baris pertama
                $product ? $product->name : '-', // Nama Produk
                $quantities[$index] ?? 0, // Jumlah
                $product ? 'Rp. ' . number_format($prices[$index] ?? 0, 0, ',', '.') : '-', // Harga
                $index === 0 ? 'Rp. ' . number_format($order->customer_pay, 0, ',', '.') : '', // Bayar hanya di baris pertama
                $index === 0 ? 'Rp. ' . number_format($order->customer_return, 0, ',', '.') : '', // Kembalian hanya di baris pertama
                $index === 0 ? 'Rp. ' . number_format($order->total_harga_after_point, 0, ',', '.') : '', // Total Harga Setelah Point hanya di baris pertama
                $index === 0 ? $order->tanggal_penjualan : '', // Tanggal Penjualan hanya di baris pertama
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            [
                'Toko Jaya Abadi', '', '', '', '', '', '', '', '', '' // Merge ini dilakukan via AfterSheet
            ],
            [
                "Nama Pelanggan",
                "No Telepon",
                "Point",
                "Nama Produk",
                "Jumlah",
                "Harga",
                "Bayar",
                "Kembalian",
                "Total Harga Setelah Point",
                "Tanggal Penjualan",
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling judul toko di baris 1
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Styling header di baris ke-2
        $sheet->getStyle('A2:J2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
        ]);

        $sheet->getRowDimension(2)->setRowHeight(25);

        // Aktifkan wrap text dan atur alignment untuk kolom produk
        $sheet->getStyle('D')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Atur alignment untuk semua kolom agar berada di tengah
        $sheet->getStyle('A:J')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 10,
            'D' => 30,
            'E' => 10,
            'F' => 15,
            'G' => 20,
            'H' => 20,
            'I' => 25,
            'J' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Merge judul di baris 1
                $event->sheet->mergeCells('A1:J1');
            },
        ];
    }
}
