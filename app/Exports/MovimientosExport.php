<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovimientosExport implements FromCollection, WithHeadings, WithStyles
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
                $row['fecha'],
                $row['tipo'],
                $row['item'],
                $row['sku'],
                $row['cantidad'],
                $row['area_origen'],
                $row['area_destino'],
                $row['usuario'],
                $row['motivo'],
            ];
        });
    }

    public function headings(): array
    {
        return ['Fecha', 'Tipo', 'Ítem', 'SKU', 'Cantidad', 'Área Origen', 'Área Destino', 'Usuario', 'Motivo'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}