<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\customclasses\Corefunctions;
use App\Models\User;
use App\Models\ModelMst;
use App\Models\Suffix;
use App\Models\Grade;
use App\Models\IntColor;
use App\Models\ExtColor;
use App\Models\Location;
use App\Models\SuffixOldNew;
use App\Models\Team;
use App\Models\Auth;
use DB;
use Illuminate\Support\Facades\File;
use Carbon;
use Exception;
use Illuminate\Support\Facades\Config;
use App\customclasses\AdditionalFunctions;

class MasterController extends Controller
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


    public function addModel()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            
            $requiredFields = array('name');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }

             DB::table('models')->insertGetId(array(
                'name' =>  $inputParameters["name"],
                'created_at' => Carbon\Carbon::now()
            ));
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = "Model created successfully.";
            return response()->json($response,200);
         
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }



    public function getModelList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = ModelMst::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'Model List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }
        $queryResult = ModelMst::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'Model List generated successfully', null);
             

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



    public function deleteModelDetails(Request $request)
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
                
                /* model Details */
                $id = $inputParameters['id'];

                $queryResult = ModelMst::where('id',$id)->first();

              

                $modelDetails = $this->Corefunctions->convertToArray($queryResult);
                
                if( empty( $modelDetails ) ){
                    throw new Exception('model id does not exist.',475);
                }

                $result = DB::table('models')
                ->where('id', $id)
                ->update([
                    'updated_at' => Carbon\Carbon::now(),
                    'deleted_at' => Carbon\Carbon::now()
                    ]);

                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = 'Model deleted successfully';
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           


    }



    public function addSuffix()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            
            $requiredFields = array('name');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }

             DB::table('suffixs')->insertGetId(array(
                'name' =>  $inputParameters["name"],
                'created_at' => Carbon\Carbon::now()
            ));
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = "suffix created successfully.";
            return response()->json($response,200);
         
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }



    public function getSuffixList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = Suffix::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'Suffix List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }
        $queryResult = Suffix::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'Suffix List generated successfully', null);
             

    }




    public function deleteSuffixDetails(Request $request)
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
                
                /* model Details */
                $id = $inputParameters['id'];
                $queryResult = Suffix::where('id',$id)->first();

                $modelDetails = $this->Corefunctions->convertToArray($queryResult);
                if( empty( $modelDetails ) ){
                    throw new Exception('Suffix id does not exist.',475);
                }

                $result = DB::table('suffixs')
                ->where('id', $id)
                ->update([
                    'updated_at' => Carbon\Carbon::now(),
                    'deleted_at' => Carbon\Carbon::now()
                    ]);

                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = 'Suffix deleted successfully';
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           
    }



    public function addGrade()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            
            $requiredFields = array('name');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }

             DB::table('grades')->insertGetId(array(
                'name' =>  $inputParameters["name"],
                'created_at' => Carbon\Carbon::now()
            ));
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = "grade created successfully.";
            return response()->json($response,200);
         
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }



    public function getGradeList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = Grade::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'Grade List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }
        $queryResult = Grade::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'Grade List generated successfully', null);
             

    }




    public function deleteGradeDetails(Request $request)
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
                
                /* model Details */
                $id = $inputParameters['id'];
                $queryResult = Grade::where('id',$id)->first();

                $modelDetails = $this->Corefunctions->convertToArray($queryResult);
                if( empty( $modelDetails ) ){
                    throw new Exception('Grade id does not exist.',475);
                }

                $result = DB::table('grades')
                ->where('id', $id)
                ->update([
                    'updated_at' => Carbon\Carbon::now(),
                    'deleted_at' => Carbon\Carbon::now()
                    ]);

                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = 'Grade deleted successfully';
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           
    }



    public function addIntColor()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            
            $requiredFields = array('name');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }

             DB::table('intcolors')->insertGetId(array(
                'name' =>  $inputParameters["name"],
                'created_at' => Carbon\Carbon::now()
            ));
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = "intcolor created successfully.";
            return response()->json($response,200);
         
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }



    public function getIntColorList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = IntColor::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'IntColor List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }
        $queryResult = IntColor::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'IntColor List generated successfully', null);
             

    }




    public function deleteIntColorDetails(Request $request)
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
                
                /* model Details */
                $id = $inputParameters['id'];
                $queryResult = IntColor::where('id',$id)->first();

                $modelDetails = $this->Corefunctions->convertToArray($queryResult);
                if( empty( $modelDetails ) ){
                    throw new Exception('IntColor id does not exist.',475);
                }

                $result = DB::table('intcolors')
                ->where('id', $id)
                ->update([
                    'updated_at' => Carbon\Carbon::now(),
                    'deleted_at' => Carbon\Carbon::now()
                    ]);

                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = 'IntColor deleted successfully';
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           
    }


    public function addExtColor()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            
            $requiredFields = array('name');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }

             DB::table('extcolors')->insertGetId(array(
                'name' =>  $inputParameters["name"],
                'created_at' => Carbon\Carbon::now()
            ));
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = "extcolor created successfully.";
            return response()->json($response,200);
         
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }



    public function getExtColorList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = ExtColor::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'extcolor List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }
        $queryResult = ExtColor::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'extcolor List generated successfully', null);
             

    }




    public function deleteExtColorDetails(Request $request)
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
                
                /* model Details */
                $id = $inputParameters['id'];
                $queryResult = ExtColor::where('id',$id)->first();

                $modelDetails = $this->Corefunctions->convertToArray($queryResult);
                if( empty( $modelDetails ) ){
                    throw new Exception('ExtColor id does not exist.',475);
                }

                $result = DB::table('extcolors')
                ->where('id', $id)
                ->update([
                    'updated_at' => Carbon\Carbon::now(),
                    'deleted_at' => Carbon\Carbon::now()
                    ]);

                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = 'ExtColor deleted successfully';
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           
    }



    public function addLocation()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            
            $requiredFields = array('name');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }

             DB::table('locations')->insertGetId(array(
                'name' =>  $inputParameters["name"],
                'created_at' => Carbon\Carbon::now()
            ));
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = "location created successfully.";
            return response()->json($response,200);
         
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }



    public function getLocationList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = Location::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'location List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }
        $queryResult = Location::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'extcolor List generated successfully', null);
             

    }




    public function deleteLocationDetails(Request $request)
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
                
                /* model Details */
                $id = $inputParameters['id'];
                $queryResult = Location::where('id',$id)->first();

                $modelDetails = $this->Corefunctions->convertToArray($queryResult);
                if( empty( $modelDetails ) ){
                    throw new Exception('Location id does not exist.',475);
                }

                $result = DB::table('locations')
                ->where('id', $id)
                ->update([
                    'updated_at' => Carbon\Carbon::now(),
                    'deleted_at' => Carbon\Carbon::now()
                    ]);

                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = 'Location deleted successfully';
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           
    }



    public function addTeam()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            
            $requiredFields = array('name');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }

             DB::table('teams')->insertGetId(array(
                'name' =>  $inputParameters["name"],
                'created_at' => Carbon\Carbon::now()
            ));
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = "team created successfully.";
            return response()->json($response,200);
         
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }



    public function getTeamList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = Team::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'Team List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }
        $queryResult = Team::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'extcolor List generated successfully', null);
             

    }




    public function deleteTeamDetails(Request $request)
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
                
                /* model Details */
                $id = $inputParameters['id'];
                $queryResult = Team::where('id',$id)->first();

                $modelDetails = $this->Corefunctions->convertToArray($queryResult);
                if( empty( $modelDetails ) ){
                    throw new Exception('Team id does not exist.',475);
                }

                $result = DB::table('teams')
                ->where('id', $id)
                ->update([
                    'updated_at' => Carbon\Carbon::now(),
                    'deleted_at' => Carbon\Carbon::now()
                    ]);

                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = 'Team deleted successfully';
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           
    }



    public function addSuffixOldNew()
    { 
        try {
            $inputParameters = $this->request['parameters'];
            
            $requiredFields = array('name');
            foreach ($requiredFields as $key => $value) {
                if (!isset($inputParameters[$value]) || $inputParameters[$value] == '') throw new Exception(config('constants.VALIDATIONS.REQUIRED_FIELD'),404);

            }

             DB::table('suffix_old_news')->insertGetId(array(
                'name' =>  $inputParameters["name"],
                'created_at' => Carbon\Carbon::now()
            ));
            
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] = "SuffixOldNew created successfully.";
            return response()->json($response,200);
         
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),'code'=> $e->getCode(),'status'=>config('constants.API_CODES.ERROR_STATUS')], config('constants.API_CODES.ERROR'));
        
        }
        

    }



    public function getSuffixOldNewList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = SuffixOldNew::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'SuffixOldNew List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }
        $queryResult = SuffixOldNew::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'SuffixOldNew List generated successfully', null);
             

    }




    public function deleteSuffixOldNewDetails(Request $request)
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
                
                /* model Details */
                $id = $inputParameters['id'];
                $queryResult = SuffixOldNew::where('id',$id)->first();

                $modelDetails = $this->Corefunctions->convertToArray($queryResult);
                if( empty( $modelDetails ) ){
                    throw new Exception('SuffixOldNew id does not exist.',475);
                }

                $result = DB::table('suffix_old_news')
                ->where('id', $id)
                ->update([
                    'updated_at' => Carbon\Carbon::now(),
                    'deleted_at' => Carbon\Carbon::now()
                    ]);

                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = 'SuffixOldNew deleted successfully';
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           
    }

    public function getUserList(Request $request){
        //$limit =10;
        //$page =1;
        $limit = isset($request->parameters['limit']) ? $request->parameters['limit']  : null;
        $page = isset($request->parameters['page'])  ? $request->parameters['page']  : null;
        if($page == null){
            $queryResult = User::whereNull('deleted_at')->orderBy('id','desc')->get();

            $queryResult = $this->Corefunctions->convertToArray($queryResult);
            $response['code'] = config('constants.API_CODES.SUCCESS');
            $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
            $response['message'] =  'User List generated successfully';
            $response['data'] = $queryResult;
            return response()->json($response,200);

        }

        if((isset($request->parameters['searchText']) && $request->parameters['searchText']!='') ){

            if((isset($request->parameters['searchBy']) && $request->parameters['searchBy']!='') ){


                 
                    if($request->parameters['searchBy'] == 'id' || $request->parameters['searchBy'] == 'status'){
                        $queryResult = User::where($request->parameters['searchBy'],$request->parameters['searchText'])->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
                    } else{
                        if($request->parameters['searchBy'] == 'name'){

                            $arrayString =  explode(" ", $request->parameters['searchText']);
                            $cnt = count($arrayString);
                            if($cnt > 1){
                                $queryResult = User::where('first_name', 'like','%'.$arrayString[0].'%')->where('last_name', 'like','%'.$arrayString[1].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
                            } else{
                                $queryResult = User::where('first_name', 'like','%'.$arrayString[0].'%')->orWhere('last_name', 'like','%'.$arrayString[0].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
                            }


                        }else{
                            $queryResult = User::where($request->parameters['searchBy'], 'like','%'.$request->parameters['searchText'].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
                        }
                        
                    }

                 
                

            }else{
                $queryResult = User::where('id',$request->parameters['searchText'])->orWhere('email', 'like', '%'.$request->parameters['searchText'].'%')->orWhere('username', 'like', '%'.$request->parameters['searchText'].'%')->orWhere('role', 'like', '%'.$request->parameters['searchText'].'%')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);

            }
           

        }else{

            $queryResult = User::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        }


       // $queryResult = User::whereNull('deleted_at')->orderBy('id','desc')->paginate($limit, ['*'], $pageName = "page", $page);
        return $this->generateCustomizedPaginatedResponse($queryResult, 1, 'User List generated successfully', null);
             

    }

    public function getUserDetails(Request $request)
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
                
                /* User Details */
                $id = $inputParameters['id'];

                $queryResult = User::where('id',$id)->first();

                $userDetails = $this->Corefunctions->convertToArray($queryResult);
                
                if( empty( $userDetails ) ){
                    throw new Exception('User id does not exist.',475);
                }
                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['data'] = $userDetails;
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           


    }



    public function updateUserDetails(Request $request)
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
                
                /* User Details */
                $id = $inputParameters['id'];

                $queryResult = User::where('id',$id)->first();

                $userDetails = $this->Corefunctions->convertToArray($queryResult);

                if( empty( $userDetails ) ){
                    throw new Exception('user id does not exist.',675);
                }



                $inputParameters['firstname'] = (isset($inputParameters['firstname']) ) ? $inputParameters['firstname'] :$userDetails['first_name']; 
                $inputParameters['lastname'] = (isset($inputParameters['lastname']) ) ? $inputParameters['lastname'] :$userDetails['last_name'];
                $inputParameters['email'] = (isset($inputParameters['email']) ) ? $inputParameters['email'] :$userDetails['email']; 
                $inputParameters['username'] = (isset($inputParameters['username']) ) ? $inputParameters['username'] :$userDetails['username']; 
                $inputParameters['role'] = (isset($inputParameters['role']) ) ? $inputParameters['role'] :$userDetails['role']; 
                $inputParameters['team'] = (isset($inputParameters['team']) ) ? $inputParameters['team'] :$userDetails['team'];
                $inputParameters['status'] = (isset($inputParameters['status']) ) ? $inputParameters['status'] :$userDetails['status'];

		 

              
 


                User::where('id',$id)->update(array(
                        'first_name' =>  $inputParameters["firstname"],
                        'last_name' =>  $inputParameters["lastname"],
                        'email' => $inputParameters["email"],
                        'username' =>$inputParameters["username"],
                        'role' =>$inputParameters["role"],
                        'team' =>$inputParameters["team"],
                        'status' =>$inputParameters["status"], 
                        'updated_at' => Carbon\Carbon::now()));


                        $queryResult = User::where('id',$id)->first();

                        $userDetails = $this->Corefunctions->convertToArray($queryResult);

                
              
                $response['code'] = config('constants.API_CODES.SUCCESS');
                $response['status'] = config('constants.API_CODES.SUCCESS_STATUS');
                $response['message'] = "User data updated successfully.";
                $response['data'] = $userDetails;
                return response()->json($response,200);
            } catch (Exception $ex) {
                return response()->json(['message' => $ex->getMessage()], 400);
            }
           


    }




}