<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderRecapExport implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string $month;

    protected string $year;

    protected ?string $leadSource;

    protected int $rowNumber = 0;

    public function __construct(string $month, string $year, ?string $leadSource = null)
    {
        $this->month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $this->year = $year;
        $this->leadSource = $leadSource;
    }

    public function title(): string
    {
        return "Rekap Order {$this->month}-{$this->year}";
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function collection(): Enumerable
    {
        $query = Order::with(['creator', 'pic'])
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month);

        if (! empty($this->leadSource)) {
            $query->where('lead_source', $this->leadSource);
        }

        return $query->orderBy('transaction_date', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'No Order',
            'Nama Pemesan',
            'Asal Pemesan',
            'Produk / Layanan',
            'Nilai Transaksi (Rp)',
            'Komisi (Rp)',
            'Status Pembayaran',
            'PIC / Sales',
        ];
    }

    public function map($order): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $order->transaction_date ? $order->transaction_date->format('d/m/Y') : '-',
            $order->order_number,
            $order->client_name,
            $order->lead_source ?? '-',
            $order->details,
            (float) $order->total_amount,
            (float) $order->commission_amount,
            strtoupper($order->payment_status),
            $order->pic->name ?? ($order->creator->name ?? '-'),
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            3 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E1B4B'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $monthNames = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                ];
                $monthLabel = $monthNames[$this->month] ?? $this->month;

                // Title Block (Row 1)
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', "Laporan Rekap Order & Komisi - Periode: {$monthLabel} {$this->year}");
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $totalRows = $this->rowNumber;
                $startRow = 4;
                $lastDataRow = $startRow + $totalRows - 1;

                if ($totalRows > 0) {
                    // Currency format
                    $sheet->getStyle("G{$startRow}:G{$lastDataRow}")
                        ->getNumberFormat()
                        ->setFormatCode('"Rp "#,##0');

                    $sheet->getStyle("H{$startRow}:H{$lastDataRow}")
                        ->getNumberFormat()
                        ->setFormatCode('"Rp "#,##0');

                    $sheet->getStyle("A{$startRow}:C{$lastDataRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$startRow}:E{$lastDataRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I{$startRow}:I{$lastDataRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Total Footer Row
                    $totalRowIndex = $lastDataRow + 1;
                    $sheet->mergeCells("A{$totalRowIndex}:F{$totalRowIndex}");
                    $sheet->setCellValue("A{$totalRowIndex}", 'TOTAL KESELURUHAN');

                    $sheet->setCellValue("G{$totalRowIndex}", "=SUM(G{$startRow}:G{$lastDataRow})");
                    $sheet->setCellValue("H{$totalRowIndex}", "=SUM(H{$startRow}:H{$lastDataRow})");

                    $totalStyle = [
                        'font' => ['bold' => true, 'size' => 11],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFE2E8F0'],
                        ],
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_THIN],
                            'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
                        ],
                    ];
                    $sheet->getStyle("A{$totalRowIndex}:J{$totalRowIndex}")->applyFromArray($totalStyle);
                    $sheet->getStyle("A{$totalRowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("G{$totalRowIndex}:H{$totalRowIndex}")
                        ->getNumberFormat()
                        ->setFormatCode('"Rp "#,##0');

                    $sheet->getStyle("A3:J{$totalRowIndex}")->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                } else {
                    $emptyRowIndex = 4;
                    $sheet->mergeCells("A{$emptyRowIndex}:J{$emptyRowIndex}");
                    $sheet->setCellValue("A{$emptyRowIndex}", 'Belum ada transaksi order tercatat pada periode ini.');
                    $sheet->getStyle("A{$emptyRowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
