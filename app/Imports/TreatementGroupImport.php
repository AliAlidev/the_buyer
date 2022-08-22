<?php

namespace App\Imports;

use App\Models\TreatementGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class TreatementGroupImport implements ToCollection
{
    use Importable;
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $tmp1 = [];
        $counter = 1;
        foreach ($collection as $key => $item) {
            if ($item[0] != null)
                if (!in_array($item[0], $tmp1)) {
                    $t_group = TreatementGroup::create([
                        'tg_id' => $counter,
                        'ar_name' => $item[0],
                        'merchant_type' => 1
                    ]);
                    array_push($tmp1, $item[0]);
                    $counter++;
                }
        }
    }
}
