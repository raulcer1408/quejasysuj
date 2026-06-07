<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EstadisticoExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $stats,
        private readonly array $filtros
    ) {}

    public function sheets(): array
    {
        return [
            new Sheets\ResumenSheet($this->stats, $this->filtros),
        ];
    }
}
