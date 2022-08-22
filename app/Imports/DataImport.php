<?php

namespace App\Imports;

use App\Exports\DataExport;
use App\Models\Company;
use App\Models\Data;
use App\Models\Shape;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Facades\Excel;

class DataImport implements ToCollection
{
    use Importable;
    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection)
    {
        $admin = User::where('email', 'admin@buyer.com')->first();
        foreach ($collection as $key => $item) {
            $shape = Shape::where('ar_shape_name', $item[2])->first();
            $comp = Company::where('ar_comp_name', $item[3])->first();
            if ($item[4] != null)
                Data::create([
                    'name' => $item[4],
                    'dose' => $item[1],
                    'shape_id' => $shape != null ? $shape->shape_id : null,
                    'comp_id' =>  $comp != null ? $comp->comp_id : null,
                    'merchant_type' => 1,
                    'created_by' => $admin != null ? $admin->id : 0
                ]);
        }
    }



    function utf8_strrev($str)
    {
        preg_match_all('/./us', $str, $ar);
        return join('', array_reverse($ar[0]));
    }

    public function model(array $row)
    {
        dd($row);

        // return new Data([
        //     'name' => $row[1],
        //     'dose' => $row[2],
        //     'tab_count' => $row[3],
        //     'med_shape' => $row[4],
        //     'med_comp' => $row[5],
        //     'effict_matterials' => $row[6],
        //     'treatement_group' => $row[7],
        //     'treatements' => $row[8],
        //     'special_alarms' => $row[9],
        //     'interference' => $row[10],
        //     'side_effects' => $row[11]
        // ]);
    }
}
