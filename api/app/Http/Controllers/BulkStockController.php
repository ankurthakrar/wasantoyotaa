<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\customclasses\Corefunctions;
use App\Models\User;
use App\Models\Stock;
use App\Models\Docket;
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

        
    public function addDocketBulk(Request $request)
    { 

       
        try {
            if (!isset($request['docket_file'])) throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            $docket_file = $request->file('docket_file');

            if ($docket_file) {
                $spreadsheet = IOFactory::load($docket_file);
                $worksheet = $spreadsheet->getActiveSheet();
                $excelData = $worksheet->toArray();
                if (!empty($excelData) && count($excelData) > 1) {
                    array_shift($excelData);
                }
                foreach ($excelData as $key=>$row) {
                    if (
                        ($row[1] == null) || ($row[2] == null) || ($row[3] == null) || ($row[4] == null) || ($row[5] == null) || ($row[6] == null) || ($row[7] == null) || ($row[8] == null) || ($row[9] == null) || ($row[10] == null) || ($row[12] == null) || ($row[13] == null) || ($row[14] == null) || ($row[15] == null) || ($row[16] == null) || ($row[17] == null) || ($row[18] == null) || ($row[19] == null)
                    ) {
                        continue;
                    }

                    $dmax = Docket::max('dnum');

                    $varDckNo = $dmax + 1;
                    $dnum = $dmax + 1;
                    if (strlen($varDckNo) === 1) {
                        $varDckNo = '000' . $varDckNo;
                    } elseif (strlen($varDckNo) === 2) {
                        $varDckNo = '00' . $varDckNo;
                    } elseif (strlen($varDckNo) === 3) {
                        $varDckNo = '0' . $varDckNo;
                    } else {
                        $varDckNo = '' . $varDckNo;
                    }
                    
                    $dmax = $dmax + 1;
        
                    $varDckNo = $row[2]."/".date('y')."/".date('m')."/".$dmax;

                    $docketid =DB::table('docket_details')->insertGetId(array(
                        'docket_no' =>  $varDckNo,
                        'model' =>  $row[4],
                        'color' =>$row[7],
                        'int_color' =>$row[6],
                        'suffix' =>$row[3],
                        'grade' =>$row[5],
                        'mode_of_payment' =>$row[8],
                        'insurance' =>$row[9],
                        'registration' =>$row[10],
                        'order_source' =>$row[12],
                        // 'special_no_by' => $row[11], 
                        'cost_of_vehicle' =>$row[19],
                        'so_name' =>$row[1],
                        'dnum' => $dnum,
                        'created_at' => Carbon\Carbon::now()
                    ));

                      
                    // $vehicleId =DB::table('vehicle_details')->insertGetId(array(
                    //     'docket_id' =>  $docketid,
                    //     'model' =>  $row[4],
                    //     'color' =>$row[7],
                    //     'suffix' =>$row[3],
                    //     'grade' =>$row[5],
                    //     'int_color' =>$row[6],
                    //     'created_at' => Carbon\Carbon::now()
                    // ));
        
                     
        
                    $custId =DB::table('customer_details')->insertGetId(array(
                        'docket_id' =>  $docketid,
                        // 'customer_name' =>  $row[13],
                        'pan_no' =>  $row[17],
                        'dob' =>$row[18],
                        'created_at' => Carbon\Carbon::now()
                    ));
        
        
                    // $paymentId = DB::table('payment_details')->insertGetId(array(
                    //     'docket_id' =>  $docketid,
                    //     'created_at' => Carbon\Carbon::now()
                    // ));
        
                    $regAddrId = DB::table('address_details')->insertGetId(array(
                        'docket_id' =>  $docketid,
                        'name' =>  $row[13],
                        'tel_mobile_no' =>  $row[14],
                        'address' =>  $row[15],
                        'mail_id' =>$row[16],
                        'type' =>'registraion',
                        'created_at' => Carbon\Carbon::now()
                    ));
    
                    $correspondanceAddrId = DB::table('address_details')->insertGetId(array(
                        'docket_id' =>  $docketid,
                        'name' =>  $row[13],
                        'tel_mobile_no' =>  $row[14],
                        'address' =>  $row[15],
                        'mail_id' =>$row[16],
                        'type' =>'correspondance',
                        'created_at' => Carbon\Carbon::now()
                    ));
         
                }
            }

            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = 'Docket created successfully';
            return response()->json($response,200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
    }


}