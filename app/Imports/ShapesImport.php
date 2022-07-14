<?php

namespace App\Imports;

use App\Models\Shape;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

class ShapesImport implements ToModel
{
    use Importable;
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        return new Shape([
            'shape_id' => $row[0],
            'shape_name' => $row[1],
            'merchant_type' => 1
        ]);
    }
}
