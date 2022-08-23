<?php

namespace App\Imports;

use App\Models\EffMaterial;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class EffictMatImport implements ToCollection
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
            $val00 = trim($item[0]);
            if ($val00 != null) {
                $values = explode('+', $val00);
                foreach ($values as $key => $value) {
                    if (!in_array($value, $tmp1)) {
                        $effMat = EffMaterial::create([
                            'eff_mat_id' => $counter,
                            'en_name' => $value,
                            'merchant_type' => 1
                        ]);
                        array_push($tmp1, $value);
                        $counter++;
                    }
                }
            }
        }
    }
}
