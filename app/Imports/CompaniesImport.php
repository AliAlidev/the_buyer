<?php

namespace App\Imports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class CompaniesImport implements ToCollection
{
    use Importable;
    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection)
    {
        $tmp = [];
        $counter = 1;
        foreach ($collection as $key => $item) {
            if (!in_array($item[3], $tmp) && $item[3] != null) {
                $company = Company::create([
                    'comp_id' => $counter,
                    'ar_comp_name' => $item[3],
                    'merchant_type' => 1
                ]);
                array_push($tmp, $item[3]);
                $counter++;
            }
        }
    }
}
