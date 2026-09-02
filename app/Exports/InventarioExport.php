<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarioExport implements FromCollection, WithHeadings, WithStyles
{
    protected $rows;

    public function __construct(iterable $rows)
    {
        $this->rows = collect($rows);
    }

    public function collection()
    {
        return $this->rows->map(function ($row) {
            return [
                $row['empresa'],
                $row['sucursal'],
                $row['area'],
                $row['sku'],
                $row['item'],
                $row['categoria'],
                $row['cantidad'],
                $row['unidad'],
                $row['responsable'],
                $row['estado'],
            ];
        });
    }

    public function headings(): array
    {
        return ['Empresa', 'Sucursal', 'Área', 'SKU', 'Ítem', 'Categoría', 'Cantidad', 'Unidad', 'Responsable', 'Estado'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}