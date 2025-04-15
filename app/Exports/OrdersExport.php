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
        $produkData = '-';

        if (!empty($order->products_id)) {
            $productIds = json_decode($order->products_id, true);
            $quantities = json_decode($order->total_barang, true);
            $prices = json_decode($order->total_harga, true);

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $combined = [];

            foreach ($productIds as $index => $productId) {
                $product = $products[$productId] ?? null;
                if ($product) {
                    $qty = $quantities[$index] ?? 0;
                    $price = $prices[$index] ?? 0;
                    $combined[] = "{$product->name} ({$qty} - Rp. " . number_format($price, 0, ',', '.') . ")";
                }
            }

            if (!empty($combined)) {
                $produkData = implode(', ', $combined);
            }
        }

        return [
            $order->member->name ?? '-',
            $order->member->no_telepon ?? '-',
            $order->member->point ?? 0,
            $produkData,
            'Rp. ' . number_format($order->customer_pay, 0, ',', '.'),
            'Rp. ' . number_format($order->customer_return, 0, ',', '.'),
            'Rp. ' . number_format($order->total_harga_after_point, 0, ',', '.'),
            $order->tanggal_penjualan,
        ];
    }

    public function headings(): array
    {
        return [
            [
                'Toko Jaya Abadi', '', '', '', '', '', '', '' // Merge ini dilakukan via AfterSheet
            ],
            [
                "Nama Pelanggan",
                "No Telepon",
                "Point",
                "Produk (Jumlah - Harga)",
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
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Styling header di baris ke-2
        $sheet->getStyle('A2:H2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
        ]);

        $sheet->getRowDimension(2)->setRowHeight(25);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 10,
            'D' => 50,
            'E' => 20,
            'F' => 20,
            'G' => 25,
            'H' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:H1');
            },
        ];
    }
}