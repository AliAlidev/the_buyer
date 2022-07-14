<?php

namespace App\Imports;

use App\Models\Data;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

class DataImport implements ToModel
{
    use Importable;
    /**
     * @param Collection $collection
     */

    public function model(array $row)
    {
        return new Data([
            'name' => $row[1],
            'dose' => $row[2],
            'tab_count' => $row[3],
            'med_shape' => $row[4],
            'med_comp' => $row[5],
            'effict_matterials' => $row[6],
            'treatement_group' => $row[7],
            'treatements' => $row[8],
            'special_alarms' => $row[9],
            'interference' => $row[10],
            'side_effects' => $row[11]
        ]);
    }
}
