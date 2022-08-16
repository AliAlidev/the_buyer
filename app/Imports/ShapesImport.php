<?php


namespace App\Imports;

set_time_limit(0);

use App\Models\Company;
use App\Models\Data;
use App\Models\Shape;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class ShapesImport implements ToCollection
{
    use Importable;
    /**
     * @param Collection $collection
     */
    // public function model(array $row)
    // {
    //     return new Shape([
    //         'shape_id' => $row[0],
    //         'shape_name' => $row[1],
    //         'merchant_type' => 1
    //     ]);
    // }

    public function collection(Collection $collection)
    {
        $tmp1 = [];
        $counter = 1;
        foreach ($collection as $key => $item) {
            if (!in_array($item[2], $tmp1)) {
                $shape = Shape::create([
                    'shape_id' => $counter,
                    'ar_shape_name' => $item[2],
                    'merchant_type' => 1
                ]);
                array_push($tmp1, $item[2]);
                $counter++;
            }
        }
    }
}
