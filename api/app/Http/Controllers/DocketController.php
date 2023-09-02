<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\customclasses\Corefunctions;
use App\Models\User;
use App\Models\Stock;
use App\Models\Docket;
use App\Models\Payment;
use App\Models\Auth;
use DB;
use Illuminate\Support\Facades\File;
use Carbon;
use Exception;
use Illuminate\Support\Facades\Config;
use App\customclasses\AdditionalFunctions;

class DocketController extends Controller
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

    public function addDocket()
    { 

       
        try {
            $inputDocketParameters = $this->request['docket_details'];
           
            $inputVehicleParameters = $this->request['vehicle_details'];
            $inputCustomerParameters = $this->request['customer_details'];
            $inputPaymentParameters = $this->request['payment_details'];
            $inputRegAddrParameters = $this->request['registraion_address'];
            $inputCorrAddrParameters = $this->request['correspondance_address'];

            $dmax = Docket::max('dnum');

            
            $charDckNo = '';
             
            /* if (is_null($dmax) || $dmax == '' || empty($dmax)) {
               $varDckNo = 1;
               $charDckNo = '000' . $varDckNo;
            } else { */
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
            /* } */

            
            $dmax = $dmax + 1;

            $varDckNo = $inputDocketParameters["team_loc"]."/".date('y')."/".date('m')."/".$dmax;
            $docketid =DB::table('docket_details')->insertGetId(array(
                'docket_no' =>  $varDckNo,
                'ctdms_order_booking_no' =>  $inputDocketParameters["ctdms_order_booking_no"],
                'booking_date' => $inputDocketParameters["booking_date"],
                'model' =>$inputDocketParameters["model"],
                'color' =>$inputDocketParameters["color"],
                'int_color' =>$inputDocketParameters["int_color"],
                'suffix' =>$inputDocketParameters["suffix"],
                'grade' =>$inputDocketParameters["grade"],
                'hpa' =>$inputDocketParameters["hpa"],
                'mode_of_payment' =>$inputDocketParameters["mode_of_payment"],
                'insurance' =>$inputDocketParameters["insurance"],
                'registration' =>$inputDocketParameters["registration"],
                'order_source' =>$inputDocketParameters["order_source"],
                'cost_of_vehicle' =>$inputDocketParameters["cost_of_vehicle"],
                'insurance_amt' =>$inputDocketParameters["insurance_amt"],
                'registration_amt' =>$inputDocketParameters["registration_amt"],
                'accessories_amt_inbuilt' =>$inputDocketParameters["accessories_amt_inbuilt"],
                'accessories_amt_additional' =>$inputDocketParameters["accessories_amt_additional"],
                'special_no' =>$inputDocketParameters["special_no"],
                'depot_charges' =>$inputDocketParameters["depot_charges"],
                'tcs' =>$inputDocketParameters["tcs"],
                'fast_tag' =>$inputDocketParameters["fast_tag"],
                'extended_warrnaty' =>$inputDocketParameters["extended_warrnaty"],
                'vas' =>$inputDocketParameters["vas"],
                'other_charges' =>$inputDocketParameters["other_charges"],
                'discount' =>$inputDocketParameters["discount"],
                'discount_approve' =>$inputDocketParameters["discount_approve"],
                'total_charges' =>$inputDocketParameters["total_charges"],
                'approval_rto_crtm' =>$inputDocketParameters["approval_rto_crtm"],
                'approval_insurance' =>$inputDocketParameters["approval_insurance"],
                'approval_ctdms_do_cut' =>$inputDocketParameters["approval_ctdms_do_cut"],
                'approval_delivery' =>$inputDocketParameters["approval_delivery"],
                'approval_refund_cancelation' =>$inputDocketParameters["approval_refund_cancelation"],
                'approval_refund_cancelation_amt' =>$inputDocketParameters["approval_refund_cancelation_amt"],
                'approval_rto_crtm_date' =>$inputDocketParameters["approval_rto_crtm_date"],
                'approval_insurance_date' =>$inputDocketParameters["approval_insurance_date"],
                'approval_ctdms_do_cut_date' =>$inputDocketParameters["approval_ctdms_do_cut_date"],
                'approval_delivery_date' =>$inputDocketParameters["approval_delivery_date"],
                'approval_refund_cancelation_date' =>$inputDocketParameters["approval_refund_cancelation_date"],
                'so_name' =>$inputDocketParameters["so_name"],
                'team' =>$inputDocketParameters["team"],
                'dnum' => $dnum,
                'created_at' => Carbon\Carbon::now()
                     
              ));

              

              $vehicleId =DB::table('vehicle_details')->insertGetId(array(
                'docket_id' =>  $docketid,
                'model' =>  $inputVehicleParameters["model"],
                'vin_no' => $inputVehicleParameters["vin_no"],
                'chassis_no' =>  $inputVehicleParameters["chassis_no"],
                'reg_no' => $inputVehicleParameters["reg_no"],
                'color' =>$inputVehicleParameters["color"],
                'eng_no' =>$inputVehicleParameters["eng_no"],
                'suffix' =>$inputVehicleParameters["suffix"],
                'grade' =>$inputVehicleParameters["grade"],
                'int_color' =>$inputVehicleParameters["int_color"],
                'ins_nominee_name' =>$inputVehicleParameters["ins_nominee_name"],
                'committed_delivery_date' =>$inputVehicleParameters["committed_delivery_date"],
                'actual_delivery_date' =>$inputVehicleParameters["actual_delivery_date"],
                'ssopi_inch' =>$inputVehicleParameters["ssopi_inch"],
                'ctdms_dn_no' =>$inputVehicleParameters["ctdms_dn_no"],
                'created_at' => Carbon\Carbon::now()
                     
              ));

             

              $custId =DB::table('customer_details')->insertGetId(array(
                'docket_id' =>  $docketid,
                'customer_name' =>  $inputCustomerParameters["customer_name"],
                'pan_no' =>  $inputCustomerParameters["pan_no"],
                'adhar_card_no' => $inputCustomerParameters["adhar_card_no"],
                'dob' =>$inputCustomerParameters["dob"],
                'customer_sign' =>$inputCustomerParameters["customer_sign"],
                'created_at' => Carbon\Carbon::now()
                     
              ));


              for($i=0;$i<count($inputPaymentParameters);$i++){
                $paymentId = DB::table('payment_details')->insertGetId(array(
                    'docket_id' =>  $docketid,
                    'r_no' =>  $inputPaymentParameters[$i]["r_no"],
                    'amount' =>  $inputPaymentParameters[$i]["amount"],
                    'particular' => $inputPaymentParameters[$i]["particular"],
                    'total' =>$inputPaymentParameters[$i]["total"],
                    'dt' =>$inputPaymentParameters[$i]["dt"],
                    'approve' =>$inputPaymentParameters[$i]["approve"],
                    'created_at' => Carbon\Carbon::now()
                        
                ));
            }

              $regAddrId = DB::table('address_details')->insertGetId(array(
                'docket_id' =>  $docketid,
                'name' =>  $inputRegAddrParameters["name"],
                'tel_mobile_no' =>  $inputRegAddrParameters["tel_mobile_no"],
                'address' =>  $inputRegAddrParameters["address"],
                'gst' => $inputRegAddrParameters["gst"],
                'mail_id' =>$inputRegAddrParameters["mail_id"],
                'type' =>'registraion',
                'state' => $inputRegAddrParameters["state"],
                'dist' => $inputRegAddrParameters["dist"],
                'tal' => $inputRegAddrParameters["tal"],
                'pin' => $inputRegAddrParameters["pin"],
                'created_at' => Carbon\Carbon::now()
                     
              ));

              $correspondanceAddrId = DB::table('address_details')->insertGetId(array(
                'docket_id' =>  $docketid,
                'name' =>  $inputCorrAddrParameters["name"],
                'tel_mobile_no' =>  $inputCorrAddrParameters["tel_mobile_no"],
                'address' =>  $inputCorrAddrParameters["address"],
                'gst' =>'',
                'mail_id' =>'',
                'type' =>'correspondance',
                'state' => $inputCorrAddrParameters["state"],
                'dist' => $inputCorrAddrParameters["dist"],
                'tal' => $inputCorrAddrParameters["tal"],
                'pin' => $inputCorrAddrParameters["pin"],
                'created_at' => Carbon\Carbon::now()
                     
              ));

              $queryResult = Stock::where('vin_no',$inputVehicleParameters["vin_no"])->first();

              $stockDetails = $this->Corefunctions->convertToArray($queryResult);

              if( !empty( $stockDetails ) ){

                Stock::where('vin_no',$inputVehicleParameters["vin_no"])->update(array(
                    'model' =>$inputVehicleParameters["model"],
                    'suffix' =>$inputVehicleParameters["suffix"],
                    'grade' =>$inputVehicleParameters["grade"],
                    'ext_color' =>$inputVehicleParameters["color"],
                    'int_color' =>$inputVehicleParameters["int_color"],
                    'eng_no' =>$inputVehicleParameters["eng_no"],
                    'ctdms_order_booking_no' =>  $inputDocketParameters["ctdms_order_booking_no"],
                    'so_name' =>$inputDocketParameters["so_name"],
                    'team' =>$inputDocketParameters["team"],
                    'customer_name' => $inputCustomerParameters["customer_name"],
                    'status' =>'ALLOTTED'
                
                ));
                  
                  /* Stock::where('vin_no',$inputVehicleParameters["vin_no"])->update(array(
                      'status' =>'ALLOTTED',
                      'updated_at' => Carbon\Carbon::now())); */
              } 

            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = 'Docket created successfully';
            return response()->json($response,200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }




    

    public function getDocketList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : 10;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : 1;
        //$queryResult = DB::table('docket_details')->paginate($limit, ['*'], $pageName = "page", $page);

 

        
            
        $query = Docket::select(
            'docket_details.id as id',
            'docket_details.docket_no as docket_no',
            'docket_details.ctdms_order_booking_no as ctdms_order_booking_no',
            'docket_details.booking_date as booking_date',
            'docket_details.model as model',
            'docket_details.color as color',
            'docket_details.int_color as int_color',
            'docket_details.suffix as suffix',
            'docket_details.grade as grade',
            'docket_details.hpa as hpa',
            'docket_details.mode_of_payment as mode_of_payment',
            'docket_details.insurance as insurance',
            'docket_details.registration as registration',
            'docket_details.order_source as order_source',
            'docket_details.cost_of_vehicle as cost_of_vehicle',
            'docket_details.insurance_amt as insurance_amt',
            'docket_details.registration_amt as registration_amt',
            'docket_details.accessories_amt_inbuilt as accessories_amt_inbuilt',
            'docket_details.accessories_amt_additional as accessories_amt_additional',
            'docket_details.special_no as special_no',
            'docket_details.depot_charges as depot_charges',
            'docket_details.tcs as tcs',
            'docket_details.fast_tag as fast_tag',
            'docket_details.extended_warrnaty as extended_warrnaty',
            'docket_details.vas as vas',
            'docket_details.other_charges as other_charges',
            'docket_details.discount as discount',
            'docket_details.discount_approve as discount_approve',
            'docket_details.total_charges as total_charges',
            'docket_details.approval_rto_crtm as approval_rto_crtm',
            'docket_details.approval_insurance as approval_insurance',
            'docket_details.approval_ctdms_do_cut as approval_ctdms_do_cut',
            'docket_details.approval_delivery as approval_delivery',
            'docket_details.approval_refund_cancelation as approval_refund_cancelation',
            'docket_details.approval_rto_crtm_date as approval_rto_crtm_date',
            'docket_details.approval_insurance_date as approval_insurance_date',
            'docket_details.approval_ctdms_do_cut_date as approval_ctdms_do_cut_date',
            'docket_details.approval_delivery_date as approval_delivery_date',
            'docket_details.approval_refund_cancelation_date as approval_refund_cancelation_date',
            'vehicle_details.model as vmodel',
            'vehicle_details.vin_no as vin_no',
            'vehicle_details.chassis_no as chassis_no',
            'vehicle_details.reg_no as reg_no',
            'vehicle_details.color as vcolor',
            'vehicle_details.eng_no as eng_no',
            'vehicle_details.int_color as vint_color',
            'vehicle_details.suffix as vsuffix',
            'vehicle_details.grade as vgrade',
            'vehicle_details.ins_nominee_name as ins_nominee_name',
            'vehicle_details.committed_delivery_date as committed_delivery_date',
            'vehicle_details.actual_delivery_date as actual_delivery_date',
            'vehicle_details.ssopi_inch as ssopi_inch',
            'vehicle_details.ctdms_dn_no as ctdms_dn_no',
            'customer_details.customer_name as customer_name',
            'customer_details.pan_no as pan_no',
            'customer_details.adhar_card_no as adhar_card_no',
            'customer_details.dob as dob',
            
            

        )
            ->leftJoin('vehicle_details', 'vehicle_details.docket_id', 'docket_details.id')
            ->leftJoin('customer_details', 'customer_details.docket_id', '=', 'docket_details.id')
            ->orderBy('docket_details.id','desc');
            //->leftJoin('payment_details', 'payment_details.docket_id', 'docket_details.id')
            /* ->leftJoin('address_details', 'address_details.docket_id', 'docket_details.id')
            ->where('address_details.type','registraion'); */
          
               

            if((isset($request->parameters['searchText']) && $request->parameters['searchText']!='') ){

                if((isset($request->parameters['searchBy']) && $request->parameters['searchBy']!='') ){
    
                    if($request->parameters['searchBy'] == 'id'){
                        $queryResult =  $query->where($request->parameters['searchBy'],$request->parameters['searchText'])->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
                    } else{
                        $queryResult =  $query->where($request->parameters['searchBy'], 'like','%'.$request->parameters['searchText'].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
                    }
    
                }else{
                    $queryResult = $query->where('docket_details.docket_no',$request->parameters['searchText'])->orWhere('docket_details.model', 'like', '%'.$request->parameters['searchText'].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
    
                }
               
    
            }else{
    
                $queryResult = $query->paginate($limit, ['*'], $pageName = "page", $page);
            }



        //$queryResult = $query->paginate($limit, ['*'], $pageName = "page", $page);
        
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'Docket List generated successfully', null);
             

    }


    public function generateCustomizedPaginatedResponse($paginatedData, $successCode = 1, $successMessage = null, $data = null)
    {

        $items = $paginatedData->items();
        $ndata = [];
        $tempdata = [];
        foreach ($items as $item)
        {
            $ndata['id'] = $item->id;
            $ndata['details']=$item;
            $paymentdtls = DB::table('payment_details')->where('docket_id',$item->id)->get();
            $ndata['paymentdetail']= $paymentdtls;
            
            $adressdtls = DB::table('address_details')->where('docket_id',$item->id)->get();
            $ndata['addressdetail'] = $adressdtls;
            $tempdata[] =  $ndata;

        }


        $customizedResponse = array();
        $customizedResponse["status"]    = $successCode;
        $customizedResponse["message"]   = $successMessage;
        $customizedResponse['meta_data']['current_page'] = $paginatedData->currentPage();
        $customizedResponse['meta_data']['last_page'] = $paginatedData->lastPage();
        $customizedResponse['meta_data']['total_items'] = $paginatedData->total();
        $customizedResponse['meta_data']['per_page'] = $paginatedData->count();
        //$customizedResponse['data'] = $data == null ? $paginatedData->items() : $data;
        $customizedResponse['data'] = $data == null ? $tempdata : $data;
        return response()->json($customizedResponse);
    }



    public function getDocketDetails(Request $request)
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
                
                /* Docket Details */
                $id = $inputParameters['id'];

                $queryResult = Docket::where('id',$id)->first();

                $docketDetails = $this->Corefunctions->convertToArray($queryResult);
                
                if( empty( $docketDetails ) ){
                    throw new Exception('Docket id does not exist.',475);
                }

                $vehicledtls = DB::table('vehicle_details')->where('docket_id',$id)->first();
                $vehicledtls = $this->Corefunctions->convertToArray($vehicledtls);

                $customerdtls = DB::table('customer_details')->where('docket_id',$id)->first();
                $customerdtls = $this->Corefunctions->convertToArray($customerdtls);

                $paymentdtls = DB::table('payment_details')->where('docket_id',$id)->get();
                //$paymentdtls = $this->Corefunctions->convertToArray($paymentdtls);

                $regaddressdtls = DB::table('address_details')->where('docket_id',$id)->where('type','registraion')->first();
                $regaddressdtls = $this->Corefunctions->convertToArray($regaddressdtls);

                $correspondanceaddressdtls = DB::table('address_details')->where('docket_id',$id)->where('type','correspondance')->first();
                $correspondanceaddressdtls = $this->Corefunctions->convertToArray($correspondanceaddressdtls);
              


                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
               // $response['data'] = $docketDetails;
               $response['data']['docket_details'] = $docketDetails;
               $response['data']['vehicle_details'] = $vehicledtls;
               $response['data']['customer_details'] = $customerdtls;
               $response['data']['payment_details'] = $paymentdtls;
               $response['data']['registraion_address'] = $regaddressdtls;
               $response['data']['correspondance_address'] = $correspondanceaddressdtls;
               
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           


    }



    public function updateDocketDetails(Request $request)
    {

        try {
                 
                /* Input Parameters */
                //$inputParameters = $this->request['parameters'];  // Input data

                /* $requiredFields = array('id');
            
                foreach ($requiredFields as $key => $value) {
                    if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') {
                        $this->AdditionalFunctions->returnError("Please enter required details.");
                    }
                } */
                
                /* Stock Details */
                $inputDockParameters = $this->request['parameters'];
                $id = $inputDockParameters['id'];

                $queryResult = Docket::where('id',$id)->first();

                $docketDetails = $this->Corefunctions->convertToArray($queryResult);

                if( empty( $docketDetails ) ){
                    throw new Exception('Docket id does not exist.',475);
                }

                //$inputDocketParameters = $this->request['dock_id'];

                $inputDocketParameters = $this->request['docket_details'];
           
                $inputVehicleParameters = $this->request['vehicle_details'];
                $inputCustomerParameters = $this->request['customer_details'];
                $inputPaymentParameters = $this->request['payment_details'];
                $inputRegAddrParameters = $this->request['registraion_address'];
                $inputCorrAddrParameters = $this->request['correspondance_address'];
               
                 

                $inputDocketParameters["docket_no"] = (isset($inputDocketParameters['docket_no']) ) ? $inputDocketParameters['docket_no'] :$docketDetails['docket_no'];
                $inputDocketParameters["ctdms_order_booking_no"] = (isset($inputDocketParameters['ctdms_order_booking_no']) ) ? $inputDocketParameters['ctdms_order_booking_no'] :$docketDetails['ctdms_order_booking_no'];
                //$inputDocketParameters["booking_date"] = (isset($inputDocketParameters['booking_date']) ) ? $inputDocketParameters['booking_date'] :$docketDetails['booking_date'];
                $inputDocketParameters["booking_date"] = $inputDocketParameters['booking_date'];
                $inputDocketParameters["model"] = (isset($inputDocketParameters['model']) ) ? $inputDocketParameters['model'] :$docketDetails['model'];
                $inputDocketParameters["color"] = (isset($inputDocketParameters['color']) ) ? $inputDocketParameters['color'] :$docketDetails['color'];
                $inputDocketParameters["int_color"] = (isset($inputDocketParameters['int_color']) ) ? $inputDocketParameters['int_color'] :$docketDetails['int_color'];
                $inputDocketParameters["suffix"] = (isset($inputDocketParameters['suffix']) ) ? $inputDocketParameters['suffix'] :$docketDetails['suffix'];
                $inputDocketParameters["grade"] = (isset($inputDocketParameters['grade']) ) ? $inputDocketParameters['grade'] :$docketDetails['grade'];
                $inputDocketParameters["hpa"] = (isset($inputDocketParameters['hpa']) ) ? $inputDocketParameters['hpa'] :$docketDetails['hpa'];
                $inputDocketParameters["mode_of_payment"] = (isset($inputDocketParameters['mode_of_payment']) ) ? $inputDocketParameters['mode_of_payment'] :$docketDetails['mode_of_payment'];
                $inputDocketParameters["insurance"] = (isset($inputDocketParameters['insurance']) ) ? $inputDocketParameters['insurance'] :$docketDetails['insurance'];
                $inputDocketParameters["registration"] = (isset($inputDocketParameters['registration']) ) ? $inputDocketParameters['registration'] :$docketDetails['registration'];
                $inputDocketParameters["order_source"] = (isset($inputDocketParameters['order_source']) ) ? $inputDocketParameters['order_source'] :$docketDetails['order_source'];
                $inputDocketParameters["cost_of_vehicle"] = (isset($inputDocketParameters['cost_of_vehicle']) ) ? $inputDocketParameters['cost_of_vehicle'] :$docketDetails['cost_of_vehicle'];
                $inputDocketParameters["insurance_amt"] = (isset($inputDocketParameters['insurance_amt']) ) ? $inputDocketParameters['insurance_amt'] :$docketDetails['insurance_amt'];
                $inputDocketParameters["registration_amt"] = (isset($inputDocketParameters['registration_amt']) ) ? $inputDocketParameters['registration_amt'] :$docketDetails['registration_amt'];
                $inputDocketParameters["accessories_amt_inbuilt"] = (isset($inputDocketParameters['accessories_amt_inbuilt']) ) ? $inputDocketParameters['accessories_amt_inbuilt'] :$docketDetails['accessories_amt_inbuilt'];
                $inputDocketParameters["accessories_amt_additional"] = (isset($inputDocketParameters['accessories_amt_additional']) ) ? $inputDocketParameters['accessories_amt_additional'] :$docketDetails['accessories_amt_additional'];
                $inputDocketParameters["special_no"] = (isset($inputDocketParameters['special_no']) ) ? $inputDocketParameters['special_no'] :$docketDetails['special_no'];
                $inputDocketParameters["depot_charges"] = (isset($inputDocketParameters['depot_charges']) ) ? $inputDocketParameters['depot_charges'] :$docketDetails['depot_charges'];
                $inputDocketParameters["tcs"] = (isset($inputDocketParameters['tcs']) ) ? $inputDocketParameters['tcs'] :$docketDetails['tcs'];
                $inputDocketParameters["extended_warrnaty"] = (isset($inputDocketParameters['extended_warrnaty']) ) ? $inputDocketParameters['extended_warrnaty'] :$docketDetails['extended_warrnaty'];
                $inputDocketParameters["vas"] = (isset($inputDocketParameters['vas']) ) ? $inputDocketParameters['vas'] :$docketDetails['vas'];
                $inputDocketParameters["other_charges"] = (isset($inputDocketParameters['other_charges']) ) ? $inputDocketParameters['other_charges'] :$docketDetails['other_charges'];
                $inputDocketParameters["discount"] = (isset($inputDocketParameters['discount']) ) ? $inputDocketParameters['discount'] :$docketDetails['discount'];
                $inputDocketParameters["discount_approve"] = (isset($inputDocketParameters['discount_approve']) ) ? $inputDocketParameters['discount_approve'] :$docketDetails['discount_approve'];
                $inputDocketParameters["total_charges"] = (isset($inputDocketParameters['total_charges']) ) ? $inputDocketParameters['total_charges'] :$docketDetails['total_charges'];
                $inputDocketParameters["approval_rto_crtm"] = (isset($inputDocketParameters['approval_rto_crtm']) ) ? $inputDocketParameters['approval_rto_crtm'] :$docketDetails['approval_rto_crtm'];
                $inputDocketParameters["approval_insurance"] = (isset($inputDocketParameters['approval_insurance']) ) ? $inputDocketParameters['approval_insurance'] :$docketDetails['approval_insurance'];
                $inputDocketParameters["approval_ctdms_do_cut"] = (isset($inputDocketParameters['approval_ctdms_do_cut']) ) ? $inputDocketParameters['approval_ctdms_do_cut'] :$docketDetails['approval_ctdms_do_cut'];
                $inputDocketParameters["approval_delivery"] = (isset($inputDocketParameters['approval_delivery']) ) ? $inputDocketParameters['approval_delivery'] :$docketDetails['approval_delivery'];
                $inputDocketParameters["approval_refund_cancelation"] = (isset($inputDocketParameters['approval_refund_cancelation']) ) ? $inputDocketParameters['approval_refund_cancelation'] :$docketDetails['approval_refund_cancelation'];
                $inputDocketParameters["approval_refund_cancelation_amt"] = (isset($inputDocketParameters['approval_refund_cancelation_amt']) ) ? $inputDocketParameters['approval_refund_cancelation_amt'] :$docketDetails['approval_refund_cancelation_amt'];
                //$inputDocketParameters["approval_rto_crtm_date"] = (isset($inputDocketParameters['approval_rto_crtm_date']) ) ? $inputDocketParameters['approval_rto_crtm_date'] :$docketDetails['approval_rto_crtm_date'];
                //$inputDocketParameters["approval_insurance_date"] = (isset($inputDocketParameters['approval_insurance_date']) ) ? $inputDocketParameters['approval_insurance_date'] :$docketDetails['approval_insurance_date'];
                //$inputDocketParameters["approval_ctdms_do_cut_date"] = (isset($inputDocketParameters['approval_ctdms_do_cut_date']) ) ? $inputDocketParameters['approval_ctdms_do_cut_date'] :$docketDetails['approval_ctdms_do_cut_date'];
                //$inputDocketParameters["approval_delivery_date"] = (isset($inputDocketParameters['approval_delivery_date']) ) ? $inputDocketParameters['approval_delivery_date'] :$docketDetails['approval_delivery_date'];
                $inputDocketParameters["approval_delivery_date"] = $inputDocketParameters["approval_delivery_date"] ;
                //$inputDocketParameters["approval_refund_cancelation_date"] = (isset($inputDocketParameters['approval_refund_cancelation_date']) ) ? $inputDocketParameters['approval_refund_cancelation_date'] :$docketDetails['approval_refund_cancelation_date'];
                $inputDocketParameters["so_name"] = (isset($inputDocketParameters['so_name']) ) ? $inputDocketParameters['so_name'] :$docketDetails['so_name'];
 



                 Docket::where('id',$id)->update(array(
                    'docket_no' =>  $inputDocketParameters["docket_no"],
                    'ctdms_order_booking_no' =>  $inputDocketParameters["ctdms_order_booking_no"],
                    'booking_date' => $inputDocketParameters["booking_date"],
                    'model' =>$inputDocketParameters["model"],
                    'color' =>$inputDocketParameters["color"],
                    'int_color' =>$inputDocketParameters["int_color"],
                    'suffix' =>$inputDocketParameters["suffix"],
                    'grade' =>$inputDocketParameters["grade"],
                    'hpa' =>$inputDocketParameters["hpa"],
                    'mode_of_payment' =>$inputDocketParameters["mode_of_payment"],
                    'insurance' =>$inputDocketParameters["insurance"],
                    'registration' =>$inputDocketParameters["registration"],
                    'order_source' =>$inputDocketParameters["order_source"],
                    'cost_of_vehicle' =>$inputDocketParameters["cost_of_vehicle"],
                    'insurance_amt' =>$inputDocketParameters["insurance_amt"],
                    'registration_amt' =>$inputDocketParameters["registration_amt"],
                    'accessories_amt_inbuilt' =>$inputDocketParameters["accessories_amt_inbuilt"],
                    'accessories_amt_additional' =>$inputDocketParameters["accessories_amt_additional"],
                    'special_no' =>$inputDocketParameters["special_no"],
                    'depot_charges' =>$inputDocketParameters["depot_charges"],
                    'tcs' =>$inputDocketParameters["tcs"],
                    'fast_tag' =>$inputDocketParameters["fast_tag"],
                    'extended_warrnaty' =>$inputDocketParameters["extended_warrnaty"],
                    'vas' =>$inputDocketParameters["vas"],
                    'other_charges' =>$inputDocketParameters["other_charges"],
                    'discount' =>$inputDocketParameters["discount"],
                    'discount_approve' =>$inputDocketParameters["discount_approve"],
                    'total_charges' =>$inputDocketParameters["total_charges"],
                    'approval_rto_crtm' =>$inputDocketParameters["approval_rto_crtm"],
                    'approval_insurance' =>$inputDocketParameters["approval_insurance"],
                    'approval_ctdms_do_cut' =>$inputDocketParameters["approval_ctdms_do_cut"],
                    'approval_delivery' =>$inputDocketParameters["approval_delivery"],
                    'approval_refund_cancelation' =>$inputDocketParameters["approval_refund_cancelation"],
                    'approval_refund_cancelation_amt' =>$inputDocketParameters["approval_refund_cancelation_amt"],
                    'approval_rto_crtm_date' =>$inputDocketParameters["approval_rto_crtm_date"],
                    'approval_insurance_date' =>$inputDocketParameters["approval_insurance_date"],
                    'approval_ctdms_do_cut_date' =>$inputDocketParameters["approval_ctdms_do_cut_date"],
                    'approval_delivery_date' =>$inputDocketParameters["approval_delivery_date"],
                    'approval_refund_cancelation_date' =>$inputDocketParameters["approval_refund_cancelation_date"],
                    'so_name' =>$inputDocketParameters["so_name"],
                    'updated_at' => Carbon\Carbon::now()
                         
                  ));

                  $vehicledtls = DB::table('vehicle_details')->where('docket_id',$id)->get();
                  $vehicledtls = $this->Corefunctions->convertToArray($vehicledtls);

                if( empty( $vehicledtls ) ){
                    //insert new record
                } else{
                    $inputVehicleParameters["model"] = (isset($inputVehicleParameters['model']) ) ? $inputVehicleParameters['model'] :$vehicledtls['model'];
                    $inputVehicleParameters["vin_no"] = (isset($inputVehicleParameters['vin_no']) ) ? $inputVehicleParameters['vin_no'] :$vehicledtls['vin_no'];
                    $inputVehicleParameters["chassis_no"] = (isset($inputVehicleParameters['chassis_no']) ) ? $inputVehicleParameters['chassis_no'] :$vehicledtls['chassis_no'];
                    $inputVehicleParameters["reg_no"] = (isset($inputVehicleParameters['reg_no']) ) ? $inputVehicleParameters['reg_no'] :$vehicledtls['reg_no'];
                    $inputVehicleParameters["color"] = (isset($inputVehicleParameters['color']) ) ? $inputVehicleParameters['color'] :$vehicledtls['color'];
                    $inputVehicleParameters["eng_no"] = (isset($inputVehicleParameters['eng_no']) ) ? $inputVehicleParameters['eng_no'] :$vehicledtls['eng_no'];
                    $inputVehicleParameters["suffix"] = (isset($inputVehicleParameters['suffix']) ) ? $inputVehicleParameters['suffix'] :$vehicledtls['suffix'];
                    $inputVehicleParameters["int_color"] = (isset($inputVehicleParameters['int_color']) ) ? $inputVehicleParameters['int_color'] :$vehicledtls['int_color'];
                    $inputVehicleParameters["grade"] = (isset($inputVehicleParameters['grade']) ) ? $inputVehicleParameters['grade'] :$vehicledtls['grade'];
                    $inputVehicleParameters["ins_nominee_name"] = (isset($inputVehicleParameters['ins_nominee_name']) ) ? $inputVehicleParameters['ins_nominee_name'] :$vehicledtls['ins_nominee_name'];
                    //$inputVehicleParameters["committed_delivery_date"] = (isset($inputVehicleParameters['committed_delivery_date']) ) ? $inputVehicleParameters['committed_delivery_date'] :$vehicledtls['committed_delivery_date'];
                    //$inputVehicleParameters["actual_delivery_date"] = (isset($inputVehicleParameters['actual_delivery_date']) ) ? $inputVehicleParameters['actual_delivery_date'] :$vehicledtls['actual_delivery_date'];
                    $inputVehicleParameters["committed_delivery_date"] =$inputVehicleParameters['committed_delivery_date'];
                    $inputVehicleParameters["actual_delivery_date"] =$inputVehicleParameters['actual_delivery_date'];
                    $inputVehicleParameters["ssopi_inch"] = (isset($inputVehicleParameters['ssopi_inch']) ) ? $inputVehicleParameters['ssopi_inch'] :$vehicledtls['ssopi_inch'];
                    $inputVehicleParameters["ctdms_dn_no"] = (isset($inputVehicleParameters['ctdms_dn_no']) ) ? $inputVehicleParameters['ctdms_dn_no'] :$vehicledtls['ctdms_dn_no'];
                    //$inputVehicleParameters["cost_of_vehicle"] = (isset($inputVehicleParameters['cost_of_vehicle']) ) ? $inputVehicleParameters['cost_of_vehicle'] :$vehicledtls['cost_of_vehicle'];
                    //$inputVehicleParameters["insurance_amt"] = (isset($inputVehicleParameters['insurance_amt']) ) ? $inputDocketParameters['insurance_amt'] :$docketDetails['insurance_amt'];
                   
                    DB::table('vehicle_details')->where('docket_id', $id)->update(array(
                        'model' =>  $inputVehicleParameters["model"],
                        'vin_no' =>  $inputVehicleParameters["vin_no"],
                        'chassis_no' =>  $inputVehicleParameters["chassis_no"],
                        'reg_no' => $inputVehicleParameters["reg_no"],
                        'color' =>$inputVehicleParameters["color"],
                        'eng_no' =>$inputVehicleParameters["eng_no"],
                        'suffix' =>$inputVehicleParameters["suffix"],
                        'int_color' =>$inputVehicleParameters["int_color"],
                        'grade' =>$inputVehicleParameters["grade"],
                        'ins_nominee_name' =>$inputVehicleParameters["ins_nominee_name"],
                        'committed_delivery_date' =>$inputVehicleParameters["committed_delivery_date"],
                        'actual_delivery_date' =>$inputVehicleParameters["actual_delivery_date"],
                        'ssopi_inch' =>$inputVehicleParameters["ssopi_inch"],
                        'ctdms_dn_no' =>$inputVehicleParameters["ctdms_dn_no"],
                        'updated_at' => Carbon\Carbon::now()
                      ));
                }


                $customerdtls = DB::table('customer_details')->where('docket_id',$id)->get();
                $customerdtls = $this->Corefunctions->convertToArray($customerdtls);

               

              if( empty( $customerdtls ) ){
                  //insert new record
              } else{
                  $inputCustomerParameters["customer_name"] = (isset($inputCustomerParameters['customer_name']) ) ? $inputCustomerParameters['customer_name'] :$customerdtls['customer_name'];
                  $inputCustomerParameters["pan_no"] = (isset($inputCustomerParameters['pan_no']) ) ? $inputCustomerParameters['pan_no'] :$customerdtls['pan_no'];
                  $inputCustomerParameters["adhar_card_no"] = (isset($inputCustomerParameters['adhar_card_no']) ) ? $inputCustomerParameters['adhar_card_no'] :$customerdtls['adhar_card_no'];
                  //$inputCustomerParameters["dob"] = (isset($inputCustomerParameters['dob']) ) ? $inputCustomerParameters['dob'] :$customerdtls['dob'];
                  $inputCustomerParameters["dob"] = $inputCustomerParameters['dob'];
                  $inputCustomerParameters["customer_sign"] = (isset($inputCustomerParameters['customer_sign']) ) ? $inputCustomerParameters['customer_sign'] :$customerdtls['customer_sign'];
              
                  DB::table('customer_details')->where('docket_id', $id)->update(array(
                    'customer_name' =>  $inputCustomerParameters["customer_name"],
                    'pan_no' =>  $inputCustomerParameters["pan_no"],
                    'adhar_card_no' => $inputCustomerParameters["adhar_card_no"],
                    'dob' =>$inputCustomerParameters["dob"],
                    'customer_sign' =>$inputCustomerParameters["customer_sign"],
                    'updated_at' => Carbon\Carbon::now()
                    ));
              }



               /*  $custId =DB::table('customer_details')->insertGetId(array(
                    'docket_id' =>  $docketid,
                    'customer_name' =>  $inputCustomerParameters["customer_name"],
                    'pan_no' =>  $inputCustomerParameters["pan_no"],
                    'adhar_card_no' => $inputCustomerParameters["adhar_card_no"],
                    'dob' =>$inputCustomerParameters["dob"],
                    'customer_sign' =>$inputCustomerParameters["customer_sign"],
                    'created_at' => Carbon\Carbon::now()
                         
                  )); */



                  DB::table('payment_details')->where('docket_id', $id)->delete();

                  for($i=0;$i<count($inputPaymentParameters);$i++){
                        $paymentId = DB::table('payment_details')->insertGetId(array(
                            'docket_id' =>  $id,
                            'r_no' =>  $inputPaymentParameters[$i]["r_no"],
                            'amount' =>  $inputPaymentParameters[$i]["amount"],
                            'particular' => $inputPaymentParameters[$i]["particular"],
                            'total' =>$inputPaymentParameters[$i]["total"],
                            'dt' =>$inputPaymentParameters[$i]["dt"],
                            'approve' =>$inputPaymentParameters[$i]["approve"],
                            'created_at' => Carbon\Carbon::now()
                                
                        ));
                    } 
                 // $paymentdtls = DB::table('payment_details')->where('docket_id',$id)->get();
                  //$paymentdtls = $this->Corefunctions->convertToArray($paymentdtls);
  
                 
  /* 
                if( empty( $paymentdtls ) ){
                    //insert new record
                } else{ */
                    /* $inputPaymentParameters["receipt_no"] = (isset($inputPaymentParameters['receipt_no']) ) ? $inputPaymentParameters['receipt_no'] :$paymentdtls['r_no'];
                    $inputPaymentParameters["amount"] = (isset($inputPaymentParameters['amount']) ) ? $inputPaymentParameters['amount'] :$paymentdtls['amount'];
                    $inputPaymentParameters["particular"] = (isset($inputPaymentParameters['particular']) ) ? $inputPaymentParameters['particular'] :$paymentdtls['particular'];
                    $inputPaymentParameters["total"] = (isset($inputPaymentParameters['total']) ) ? $inputPaymentParameters['total'] :$paymentdtls['total'];
                    $inputPaymentParameters["date"] = (isset($inputPaymentParameters['date']) ) ? $inputPaymentParameters['date'] :$paymentdtls['dt'];
                
                    DB::table('payment_details')->where('docket_id', $id)->update(array(
                        'r_no' =>  $inputPaymentParameters["receipt_no"],
                        'amount' =>  $inputPaymentParameters["amount"],
                        'particular' => $inputPaymentParameters["particular"],
                        'total' =>$inputPaymentParameters["total"],
                        'dt' =>$inputPaymentParameters["date"],
                      'updated_at' => Carbon\Carbon::now()
                      )); */

                     
                      
                     /*  for($i=0;$i<count($inputPaymentParameters);$i++){
                        $paymentId = DB::table('payment_details')->insertGetId(array(
                            'docket_id' =>  $id,
                            'r_no' =>  $inputPaymentParameters[$i]["receipt_no"],
                            'amount' =>  $inputPaymentParameters[$i]["amount"],
                            'particular' => $inputPaymentParameters[$i]["particular"],
                            'total' =>$inputPaymentParameters[$i]["total"],
                            'dt' =>$inputPaymentParameters[$i]["date"],
                            'created_at' => Carbon\Carbon::now()
                                
                        ));
                    } */
                //}


                //$inputRegAddrParameters

                $regaddrdtls = DB::table('address_details')->where('docket_id',$id)->where('type', 'registraion')->get();
                $regaddrdtls = $this->Corefunctions->convertToArray($regaddrdtls);

               

              if( empty( $regaddrdtls ) ){
                  //insert new record
              } else{

                 $inputRegAddrParameters["name"] = (isset($inputRegAddrParameters['name']) ) ? $inputRegAddrParameters['name'] :$regaddrdtls['name'];
                    $inputRegAddrParameters["tel_mobile_no"] = (isset($inputRegAddrParameters['tel_mobile_no']) ) ? $inputRegAddrParameters['tel_mobile_no'] :$regaddrdtls['tel_mobile_no'];
                    $inputRegAddrParameters["address"] = (isset($inputRegAddrParameters['address']) ) ? $inputRegAddrParameters['address'] :$regaddrdtls['address'];
                    $inputRegAddrParameters["gst"] = (isset($inputRegAddrParameters['gst']) ) ? $inputRegAddrParameters['gst'] :$regaddrdtls['gst'];
                    $inputRegAddrParameters["mail_id"] = (isset($inputRegAddrParameters['mail_id']) ) ? $inputRegAddrParameters['mail_id'] :$regaddrdtls['mail_id'];
                    $inputRegAddrParameters["dist"] = (isset($inputRegAddrParameters['dist']) ) ? $inputRegAddrParameters['dist'] :$regaddrdtls['dist'];
                    $inputRegAddrParameters["state"] = (isset($inputRegAddrParameters['state']) ) ? $inputRegAddrParameters['state'] :$regaddrdtls['state'];
                    $inputRegAddrParameters["tal"] = (isset($inputRegAddrParameters['tal']) ) ? $inputRegAddrParameters['tal'] :$regaddrdtls['tal'];
                    $inputRegAddrParameters["pin"] = (isset($inputRegAddrParameters['pin']) ) ? $inputRegAddrParameters['pin'] :$regaddrdtls['pin'];
                
                    DB::table('address_details')->where('docket_id', $id)->where('type', 'registraion')->update(array(
                        'name' =>  $inputRegAddrParameters["name"],
                      'tel_mobile_no' =>  $inputRegAddrParameters["tel_mobile_no"],
                      'address' =>  $inputRegAddrParameters["address"],
                      'gst' =>$inputRegAddrParameters["gst"],
                      'mail_id' => $inputRegAddrParameters["mail_id"],
                      'type' =>'registraion',
                      'state' => $inputRegAddrParameters["state"],
                      'dist' => $inputRegAddrParameters["dist"],
                      'tal' => $inputRegAddrParameters["tal"],
                      'pin' => $inputRegAddrParameters["pin"],
                      'updated_at' => Carbon\Carbon::now()
                      )); 
                      

              }


              $correspondanceaddrdtls = DB::table('address_details')->where('docket_id',$id)->where('type', 'correspondance')->get();
              $correspondanceaddrdtls = $this->Corefunctions->convertToArray($correspondanceaddrdtls);

             

            if( empty( $correspondanceaddrdtls ) ){
                //insert new record
            } else{

               $inputCorrAddrParameters["name"] = (isset($inputCorrAddrParameters['name']) ) ? $inputCorrAddrParameters['name'] :$correspondanceaddrdtls['name'];
                  $inputCorrAddrParameters["tel_mobile_no"] = (isset($inputCorrAddrParameters['tel_mobile_no']) ) ? $inputCorrAddrParameters['tel_mobile_no'] :$correspondanceaddrdtls['tel_mobile_no'];
                  $inputCorrAddrParameters["address"] = (isset($inputCorrAddrParameters['address']) ) ? $inputCorrAddrParameters['address'] :$correspondanceaddrdtls['address'];
                 // $inputCorrAddrParameters["gst"] = (isset($inputCorrAddrParameters['gst']) ) ? $inputCorrAddrParameters['gst'] :$correspondanceaddrdtls['gst'];
                 // $inputCorrAddrParameters["mail_id"] = (isset($inputCorrAddrParameters['mail_id']) ) ? $inputCorrAddrParameters['mail_id'] :$correspondanceaddrdtls['mail_id'];
              
                 $inputCorrAddrParameters["dist"] = (isset($inputCorrAddrParameters['dist']) ) ? $inputCorrAddrParameters['dist'] :$regaddrdtls['dist'];
                 $inputCorrAddrParameters["state"] = (isset($inputCorrAddrParameters['state']) ) ? $inputCorrAddrParameters['state'] :$regaddrdtls['state'];
                 $inputCorrAddrParameters["tal"] = (isset($inputCorrAddrParameters['tal']) ) ? $inputCorrAddrParameters['tal'] :$regaddrdtls['tal'];
                 $inputCorrAddrParameters["pin"] = (isset($inputCorrAddrParameters['pin']) ) ? $inputCorrAddrParameters['pin'] :$regaddrdtls['pin'];
             
                  DB::table('address_details')->where('docket_id', $id)->where('type', 'correspondance')->update(array(
                      'name' =>  $inputCorrAddrParameters["name"],
                    'tel_mobile_no' =>  $inputCorrAddrParameters["tel_mobile_no"],
                    'address' =>  $inputCorrAddrParameters["address"],
                    'gst' =>'',
                    'mail_id' => '',
                    'type' =>'correspondance',
                    'state' => $inputCorrAddrParameters["state"],
                    'dist' => $inputCorrAddrParameters["dist"],
                    'tal' => $inputCorrAddrParameters["tal"],
                    'pin' => $inputCorrAddrParameters["pin"],
                    'updated_at' => Carbon\Carbon::now()
                    )); 
                    

            }
    
                 /*  $paymentId = DB::table('payment_details')->insertGetId(array(
                    'docket_id' =>  $docketid,
                    'r_no' =>  $inputPaymentParameters["receipt_no"],
                    'amount' =>  $inputPaymentParameters["amount"],
                    'particular' => $inputPaymentParameters["particular"],
                    'total' =>$inputPaymentParameters["total"],
                    'dt' =>$inputPaymentParameters["date"],
                    'created_at' => Carbon\Carbon::now()
                         
                  ));
    
                  $regAddrId = DB::table('address_details')->insertGetId(array(
                    'docket_id' =>  $docketid,
                    'name' =>  $inputRegAddrParameters["name"],
                    'tel_mobile_no' =>  $inputRegAddrParameters["tel_mobile_no"],
                    'address' =>  $inputRegAddrParameters["address"],
                    'gst' => $inputRegAddrParameters["gst"],
                    'mail_id' =>$inputRegAddrParameters["mail_id"],
                    'type' =>'registraion',
                    'created_at' => Carbon\Carbon::now()
                         
                  ));
    
                  $correspondanceAddrId = DB::table('address_details')->insertGetId(array(
                    'docket_id' =>  $docketid,
                    'name' =>  $inputCorrAddrParameters["name"],
                    'tel_mobile_no' =>  $inputCorrAddrParameters["tel_mobile_no"],
                    'address' =>  $inputCorrAddrParameters["address"],
                    'gst' =>'',
                    'mail_id' =>'',
                    'type' =>'correspondance',
                    'created_at' => Carbon\Carbon::now()
                         
                  ));
 */




 

                 $queryResult = Stock::where('vin_no',$inputVehicleParameters["vin_no"])->first();

                $stockDetails = $this->Corefunctions->convertToArray($queryResult);

                if( !empty( $stockDetails ) ){

                    Stock::where('vin_no',$inputVehicleParameters["vin_no"])->update(array(
                        'model' =>$inputVehicleParameters["model"],
                        'suffix' =>$inputVehicleParameters["suffix"],
                        'grade' =>$inputVehicleParameters["grade"],
                        'ext_color' =>$inputVehicleParameters["color"],
                        'int_color' =>$inputVehicleParameters["int_color"],
                        'eng_no' =>$inputVehicleParameters["eng_no"],
                        'ctdms_order_booking_no' => $inputDocketParameters["ctdms_order_booking_no"],
                        'so_name' =>$inputDocketParameters["so_name"],
                        'team' =>$inputDocketParameters["team"],
                        'customer_name' => $inputCustomerParameters["customer_name"],
                        'status' =>'ALLOTTED'
                    
                    ));
                    
                   /*  Stock::where('vin_no',$inputVehicleParameters["vin_no"])->update(array(
                        'status' =>'ALLOTTED',
                        'updated_at' => Carbon\Carbon::now())); */
                } 

              

                
              
                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = "Docket data updated successfully.";
                $response['data'] = $docketDetails;
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }





            
           


    }
    
  

    public function updatePaymentDetails(Request $request)
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
                
                
                $pid = $inputParameters['id'];
                $docketid = $inputParameters['docket_id'];


                $queryResult = Payment::where('id',$pid)->where('docket_id', $docketid)->first();

                $paymentDetails = $this->Corefunctions->convertToArray($queryResult);

                if( empty( $paymentDetails ) ){
                    throw new Exception('payment id does not exist.',475);
                }

                $inputParameters["approve"] = (isset($inputParameters['approve']) ) ? $inputParameters['approve'] :$paymentDetails['approve'];
                $inputParameters["r_no"] = (isset($inputParameters['r_no']) ) ? $inputParameters['r_no'] :$paymentDetails['r_no'];
                $inputParameters["amount"] = (isset($inputParameters['amount']) ) ? $inputParameters['amount'] :$paymentDetails['amount'];
                $inputParameters["particular"] = (isset($inputParameters['particular']) ) ? $inputParameters['particular'] :$paymentDetails['particular'];
                $inputParameters["total"] = (isset($inputParameters['total']) ) ? $inputParameters['total'] :$paymentDetails['total'];
                $inputParameters["dt"] = (isset($inputParameters['dt']) ) ? $inputParameters['dt'] :$paymentDetails['dt'];

                Payment::where('id',$pid)->update(array(
                        'approve' =>  $inputParameters["approve"],
                        'r_no' =>  $inputParameters["r_no"],
                        'amount' => $inputParameters["amount"],
                        'particular' =>$inputParameters["particular"],
                        'total' =>$inputParameters["total"],
                        'dt' =>$inputParameters["dt"],
                        'updated_at' => Carbon\Carbon::now()));




                
              
                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = "Payment data updated successfully.";
                $response['data'] = $paymentDetails;
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           


    }


}