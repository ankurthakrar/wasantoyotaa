<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\customclasses\Corefunctions;
use App\Models\User;
use App\Models\Stock;
use App\Models\Auth;
use DB;
use Illuminate\Support\Facades\File;
use Carbon;
use Exception;
use Illuminate\Support\Facades\Config;
use App\customclasses\AdditionalFunctions;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BulkStockController extends Controller
{
    public function __construct()
    {
      
    }

   
    public function addStockBulk(Request $request)
    { 
        try {
            
            if (!isset($request['stock_file'])) throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            $stock_file = $request->file('stock_file');
            $inputParameters["status_date"] = (isset($inputParameters['status_date']) ) ? $inputParameters['status_date'] :null;

            if ($stock_file) {
                $spreadsheet = IOFactory::load($stock_file);
                $worksheet = $spreadsheet->getActiveSheet();
                $excelData = $worksheet->toArray();
                if (!empty($excelData) && count($excelData) > 1) {
                    array_shift($excelData);
                }
                foreach ($excelData as $row) {
                    $stockData = Stock::checkStockByVinNo($row[1]);
                    if(empty($stockData)){
                        DB::table('stocks')->insertGetId(array(
                            'vin_no' => $row[1],
                            'sc_no' => $row[2],
                            'km_inv_date' => $row[3],
                            'age' => $row[4],
                            'suffix' => $row[5],
                            'model' => $row[6],
                            'grade' => $row[7],
                            'ext_color' => $row[8],
                            'int_color' => $row[9],
                            'suffix_old_new' => $row[10],
                            'year' => $row[11],
                            'p_t_m' => $row[12],
                            'location' => $row[13],
                            'status' => $row[14],
                            'status_date' => $row[15],
                            'customer_name' => $row[16],
                            'so_name' => $row[17],
                            'tl' => $row[18],
                            'team' => $row[19],
                            'eng_no' => $row[20],
                            'created_at' => Carbon\Carbon::now()
                        ));

                    }
                }
         
            } 
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = config('constants.VALIDATIONS.STOCK_SUCCESS');
            return response()->json($response,200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
    }

}