<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiReportController extends Controller
{
    public function expired()
    {
        try {
            $merchant_id = Auth::guard('api')->user()->merchant_id;
            $data = DB::select('SELECT data.code, data.name, amounts.amount, amounts.expiry_date from amounts left JOIN data on amounts.data_id = data.id where amounts.merchant_id=' . $merchant_id . ' and amounts.expiry_date < CURRENT_DATE;');
            return $this->sendResponse("Proccess completed successfully", $data);
        } catch (Exception $th) {
            return $this->errors("ApiReportController@expired", $th->getMessage());
        }
    }

    // expired between current date and (current date + month count) 
    public function expiredTillMonth($month)
    {
        try {
            $merchant_id = Auth::guard('api')->user()->merchant_id;
            $month = json_decode($month);
            if (is_int($month) && $month > 0) {
                $data = DB::select('SELECT data.code, data.name, amounts.amount, amounts.expiry_date from amounts left JOIN data on amounts.data_id = data.id where amounts.merchant_id=' . $merchant_id . ' and amounts.expiry_date BETWEEN CURRENT_DATE and date_add(CURRENT_DATE, INTERVAL ' . $month . ' month);');
                return $this->sendResponse("Proccess completed successfully", $data);
            }
            return $this->sendErrorResponse('Validation error', ['month' => 'Should be positive interger value']);
        } catch (Exception $th) {
            return $this->errors("ApiReportController@expired", $th->getMessage());
        }
    }
}
