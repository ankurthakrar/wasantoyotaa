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

class StockController extends Controller
{
    public function __construct()
    {
        $jsonData = file_get_contents('php://input');
        $input= json_decode($jsonData,true);
        $this->Corefunctions = new \App\customclasses\Corefunctions;
        $this->AdditionalFunctions = new \App\customclasses\AdditionalFunctions;
        $this->request = $this -> AdditionalFunctions->validateAPI( $input );
        $userinfo = $this->AdditionalFunctions->validateAccessToken($input);
        //$userinfo = $this->Corefunctions->convertToArray($userinfo);
        
        if (!empty($userinfo)) {
            $this->userinfo = $userinfo;
        }
      
    }

    public function addStock()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            $requiredFields = array('sc_no', 'km_inv_date', 'age', 'model','suffix','grade','ext_color','int_color');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }
            $inputParameters["status_date"] = (isset($inputParameters['status_date']) ) ? $inputParameters['status_date'] :null;

            $stockData = Stock::checkStockByVinNo($inputParameters["vin_no"]);
            if(!empty($stockData)) throw new Exception('Vin number already exist',706);

             DB::table('stocks')->insertGetId(array(
                'sc_no' =>  $inputParameters["sc_no"],
                'vin_no' =>  $inputParameters["vin_no"],
                'km_inv_date' => $inputParameters["km_inv_date"],
                'age' =>$inputParameters["age"],
                'model' =>$inputParameters["model"],
                'suffix' =>$inputParameters["suffix"],
                'grade' =>$inputParameters["grade"],
                'ext_color' =>$inputParameters["ext_color"],
                'int_color' =>$inputParameters["int_color"],
                'suffix_old_new' =>$inputParameters["suffix_old_new"],
                'year' =>$inputParameters["year"],
                'p_t_m' =>$inputParameters["p_t_m"],
                'location' =>$inputParameters["location"],
                'status' =>$inputParameters["status"],
                'status_date' =>$inputParameters["status_date"],
                'customer_name' =>$inputParameters["customer_name"],
                'so_name' =>$inputParameters["so_name"],
                'tl' =>$inputParameters["tl"],
                'team' =>$inputParameters["team"],
                'eng_no' =>$inputParameters["eng_no"],
                'created_at' => Carbon\Carbon::now()
                     
              ));

            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = config('constants.VALIDATIONS.STOCK_SUCCESS');
            return response()->json($response,200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }




    public function logout(Request $request)
    {
        try {
            $accessToken  = ( isset( $_SERVER['HTTP_ACCESSTOKEN'] ) ) ? $_SERVER['HTTP_ACCESSTOKEN'] : '' ;
            if($accessToken == '' ) throw new Exception(config('constants.VALIDATIONS.LOGOUT_REQUIRED_FIELD'));
            $accessTokenDetails = Auth::getAccessDetails( $accessToken );
            if(empty($accessTokenDetails))throw new Exception(config('constants.VALIDATIONS.INVALID_TOKEN'));

            $expiryTime = strtotime("-1 minutes",strtotime("now"));
            $updateTokenTime = DB::table('auth_tokens')->where('id', $accessTokenDetails['id'])->update(array(
                'expiry_on'=> $expiryTime
            ));
          
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = config('constants.VALIDATIONS.LOGOUT_SUCCESS');
            return response()->json($response,200);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }

    public function getStockList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : 10;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : 1;
        //$queryResult = DB::table('stocks')->paginate($limit, ['*'], $pageName = "page", $page);
        //$queryResult = Stock::orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        //$queryResult = Stock::where($request->parameters['searchBy'], 'like','%'.$request->parameters['searchText'].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        //return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'Stock List generated successfully', null);

        if((isset($request->parameters['searchText']) && $request->parameters['searchText']!='') ){

            if((isset($request->parameters['searchBy']) && $request->parameters['searchBy']!='') ){

                if($request->parameters['searchBy'] == 'id'){
                    $queryResult = Stock::where($request->parameters['searchBy'],$request->parameters['searchText'])->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
                } else{
                    $queryResult = Stock::where($request->parameters['searchBy'], 'like','%'.$request->parameters['searchText'].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
                }

            }else{
                $queryResult = Stock::where('vin_no','like', '%'.$request->parameters['searchText'].'%')->orWhere('model', 'like', '%'.$request->parameters['searchText'].'%')->orWhere('status', 'like', '%'.$request->parameters['searchText'].'%')->orWhere('ext_color', 'like', '%'.$request->parameters['searchText'].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);

            }
           

        }else{

            $queryResult = Stock::orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        }

        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'Stock List generated successfully', null);
             

    }


    public function generateCustomizedPaginatedResponse($paginatedData, $successCode = 1, $successMessage = null, $data = null)
    {
        $customizedResponse = array();
        $customizedResponse["status"]    = $successCode;
        $customizedResponse["message"]   = $successMessage;
        $customizedResponse['meta_data']['current_page'] = $paginatedData->currentPage();
        $customizedResponse['meta_data']['last_page'] = $paginatedData->lastPage();
        $customizedResponse['meta_data']['total_items'] = $paginatedData->total();
        $customizedResponse['meta_data']['per_page'] = $paginatedData->count();
        $customizedResponse['data'] = $data == null ? $paginatedData->items() : $data;
        return response()->json($customizedResponse);
    }



    public function getStockDetails(Request $request)
    {

        try {
                 
                /* Input Parameters */
                $inputParameters = $this->request['parameters'];  // Input data

                $requiredFields = array('id');
            
                foreach ($requiredFields as $key => $value) {
                    if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') {
                        $this->AdditionalFunctions->returnError("Please enter required details.");
                    }
                }
                
                /* Stock Details */
               /*  if( isset($inputParameters['vin_no']) || $inputParameters['vin_no'] != '' ){
                    $vin_no = $inputParameters['vin_no'];
                    $queryResult = Stock::where('vin_no',$vin_no)->first();
                    $stockDetails = $this->Corefunctions->convertToArray($queryResult);

                } else { */
                    $id = $inputParameters['id'];
                    $queryResult = Stock::where('id',$id)->first();
                    $stockDetails = $this->Corefunctions->convertToArray($queryResult);

               /*  } */

               
                
                if( empty( $stockDetails ) ){
                    throw new Exception('stock id does not exist.',475);
                }
                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['data'] = $stockDetails;
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           


    }



    public function updateStockDetails(Request $request)
    {

        try {
                 
                /* Input Parameters */
                $inputParameters = $this->request['parameters'];  // Input data

                $requiredFields = array('id');
            
                foreach ($requiredFields as $key => $value) {
                    if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') {
                        $this->AdditionalFunctions->returnError("Please enter required details.");
                    }
                }
                
                /* Stock Details */
                $id = $inputParameters['id'];

                $queryResult = Stock::where('id',$id)->first();

                $stockDetails = $this->Corefunctions->convertToArray($queryResult);

                if( empty( $stockDetails ) ){
                    throw new Exception('stock id does not exist.',475);
                }

                $inputParameters["status_date"] = (isset($inputParameters['status_date']) ) ? $inputParameters['status_date'] :$stockDetails['status_date'];
                $inputParameters["sc_no"] = (isset($inputParameters['sc_no']) ) ? $inputParameters['sc_no'] :$stockDetails['sc_no'];
                $inputParameters["vin_no"] = (isset($inputParameters['vin_no']) ) ? $inputParameters['vin_no'] :$stockDetails['vin_no'];
                $inputParameters["km_inv_date"] = (isset($inputParameters['km_inv_date']) ) ? $inputParameters['km_inv_date'] :$stockDetails['km_inv_date'];
                $inputParameters["age"] = (isset($inputParameters['age']) ) ? $inputParameters['age'] :$stockDetails['age'];
                $inputParameters["model"] = (isset($inputParameters['model']) ) ? $inputParameters['model'] :$stockDetails['model'];
                $inputParameters["suffix"] = (isset($inputParameters['suffix']) ) ? $inputParameters['suffix'] :$stockDetails['suffix'];
                $inputParameters["grade"] = (isset($inputParameters['grade']) ) ? $inputParameters['grade'] :$stockDetails['grade'];
                $inputParameters["ext_color"] = (isset($inputParameters['ext_color']) ) ? $inputParameters['ext_color'] :$stockDetails['ext_color'];
                $inputParameters["int_color"] = (isset($inputParameters['int_color']) ) ? $inputParameters['int_color'] :$stockDetails['int_color'];
                $inputParameters["suffix_old_new"] = (isset($inputParameters['suffix_old_new']) ) ? $inputParameters['suffix_old_new'] :$stockDetails['suffix_old_new'];
                $inputParameters["year"] = (isset($inputParameters['year']) ) ? $inputParameters['year'] :$stockDetails['year'];
                $inputParameters["p_t_m"] = (isset($inputParameters['p_t_m']) ) ? $inputParameters['p_t_m'] :$stockDetails['p_t_m'];
                $inputParameters["location"] = (isset($inputParameters['location']) ) ? $inputParameters['location'] :$stockDetails['location'];
                $inputParameters["customer_name"] = (isset($inputParameters['customer_name']) ) ? $inputParameters['customer_name'] :$stockDetails['customer_name'];
                $inputParameters["so_name"] = (isset($inputParameters['so_name']) ) ? $inputParameters['so_name'] :$stockDetails['so_name'];
                $inputParameters["tl"] = (isset($inputParameters['tl']) ) ? $inputParameters['tl'] :$stockDetails['tl'];
                $inputParameters["team"] = (isset($inputParameters['team']) ) ? $inputParameters['team'] :$stockDetails['team'];
                $inputParameters["eng_no"] = (isset($inputParameters['eng_no']) ) ? $inputParameters['eng_no'] :$stockDetails['eng_no'];
                $inputParameters["status"] = (isset($inputParameters['status']) ) ? $inputParameters['status'] :$stockDetails['status'];
                
 


                    Stock::where('id',$id)->update(array(
                        'sc_no' =>  $inputParameters["sc_no"],
                        'vin_no' =>  $inputParameters["vin_no"],
                        'km_inv_date' => $inputParameters["km_inv_date"],
                        'age' =>$inputParameters["age"],
                        'model' =>$inputParameters["model"],
                        'suffix' =>$inputParameters["suffix"],
                        'grade' =>$inputParameters["grade"],
                        'ext_color' =>$inputParameters["ext_color"],
                        'int_color' =>$inputParameters["int_color"],
                        'suffix_old_new' =>$inputParameters["suffix_old_new"],
                        'year' =>$inputParameters["year"],
                        'p_t_m' =>$inputParameters["p_t_m"],
                        'location' =>$inputParameters["location"],
                        'status_date' =>$inputParameters["status_date"],
                        'status' =>$inputParameters["status"],
                        'customer_name' =>$inputParameters["customer_name"],
                        'so_name' =>$inputParameters["so_name"],
                        'tl' =>$inputParameters["tl"],
                        'team' =>$inputParameters["team"],
                        'eng_no' =>$inputParameters["eng_no"],
                        'updated_at' => Carbon\Carbon::now()));




                
              
                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = "Stock data updated successfully.";
                $response['data'] = $stockDetails;
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           


    }


    public function getStockDetailsByVinNo(Request $request)
    {

        try {
                 
                /* Input Parameters */
                $inputParameters = $this->request['parameters'];  // Input data

                $requiredFields = array('vin_no');
            
                foreach ($requiredFields as $key => $value) {
                    if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') {
                        $this->AdditionalFunctions->returnError("Please enter required details.");
                    }
                }
                
                /* Stock Details */
               /*  if( isset($inputParameters['vin_no']) || $inputParameters['vin_no'] != '' ){ */
                    $vin_no = $inputParameters['vin_no'];
                    $queryResult = Stock::where('vin_no',$vin_no)->first();
                    $stockDetails = $this->Corefunctions->convertToArray($queryResult);

               /*  } else {
                    $queryResult = Stock::where('id',$id)->first();
                    $stockDetails = $this->Corefunctions->convertToArray($queryResult);

                } */

               
                
                if( empty( $stockDetails ) ){
                    throw new Exception('vin no does not exist.',475);
                }
                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['data'] = $stockDetails;
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           


    }


    public function getStockVinNoList(Request $request){
       
        $queryResult = Stock::where('status', '<>' , 'ALLOTTED')->pluck('vin_no')->all();
        //$queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'Stock Vinno List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

    }
    
  


}