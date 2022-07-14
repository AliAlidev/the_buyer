<?php

namespace App\Imports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

class CompaniesImport implements ToModel
{
    use Importable;
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        return new Company([
            'comp_id' => $row[0],
            'comp_name' => $row[1],
            'merchant_type' => 1
        ]);
    }
}
