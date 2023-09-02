<?php

namespace App\customclasses;
use App\Models\Auth;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use DB;
use Illuminate\Support\Facades\Config;


class AdditionalFunctions
{
    
    public function __construct(){
        $this->Corefunctions = new \App\customclasses\Corefunctions; 
        
	}
    
  function returnJson($array){
    
     

    $encodeArray =  json_encode($array);
    //utilityHelper::insertReqResp(request(), $encodeArray);
    print $encodeArray;
    exit;
  } 
  function filterEmail($email){
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $this->returnError("Invalid Email Address.");
    }
  }
  function checkEmailConfirm($email,$emailconfirm){
    if(strcmp($email, $emailconfirm) !== 0){
     $this->returnError("Email mismatch. Please re-enter the email and try again.");
    }
  }

  function checkEmailExists($email){
  $userDetails = user::checkUserByEmail(strtolower($email));
    if( !empty( $userDetails ) ){
      $this->returnError("Email Already exist.");
    }
    return ;

  } 
  /* Return JSON With Error*/
  function returnError($errMsg,$errorCode=0 ){
   
    $return["status"] = 2;
    $return["code"] = $errorCode;
    $return["message"] = $errMsg;
    
    $encodeArray =  json_encode($return);
    
    print $encodeArray;
    exit;
  } 


  function validateAPI($input){
    if( empty($input) ){
      $this->returnError('Invalid Data Request.',-2);
    }
  
    $requestData = $input;
    /* Headers*/
   
    //$params =  $requestData['parameters'];
   
     if( empty( $_SERVER['HTTP_APPID'] ) ){
      $this->returnError('APP Id Missing.',409);
    }
    if( empty( $_SERVER['HTTP_APPSIGNATURE'] ) ){
      $this->returnError('APP signature Missing.',410);
    }
    $appId = $_SERVER['HTTP_APPID'];

    $appSignatureFromApp = $_SERVER['HTTP_APPSIGNATURE'];
      
    //$url  = env('BASEPATH').$_SERVER['REQUEST_URI'];
   
    /* if(strlen($appSignatureFromApp) != 64){
      $this->returnError('APP signature invalid length.',-2);
    }
    if(strlen($appId) != 32){
         $this->returnError('APP ID invalid length.',-2);
    }
    $appDetails = Auth::getAPPIdDetails($appId);
  
    if( empty( $appDetails ) ){
      $this->returnError('Invalid App Details.',-2);
    }
    $appsecret = $appDetails->;
   
    $appsignature = $this->getAppSignature($url,$appId,$appsecret,$params);
      
    if($appSignatureFromApp != $appsignature){
       //$this->returnError('APP signature mismatch.',-2);
    } */


    $appDetails = Auth::getAPPIdDetails('wasanapp');

    if( $appDetails->app_id != $appId){
      $this->returnError('APP ID invalid',407);
    }

    if( $appDetails->app_secret != $appSignatureFromApp){
      $this->returnError('APP signature mismatch',408);
    }
     

    
  
    return $requestData;  
  }


  

 

 

  public function encryptParams($url,$appsecret){
    return hash_hmac('sha256', $url, $appsecret);
  }

  public function validateAccessToken($input){
/*print "<pre>";
print_r ( $_SERVER );
print "</pre>";
exit;*/
    $requestData = $input;
   
    $accesstoken  = ( isset( $_SERVER['HTTP_ACCESSTOKEN'] ) ) ? $_SERVER['HTTP_ACCESSTOKEN'] : '' ;
   
    if( !isset( $accesstoken ) || $accesstoken == '' ){
      $this->returnError('ACCESSTOKEN Missing.',403);
    }

    $accessTokenDetails = Auth::getAccessDetails( $accesstoken );

    if( empty( $accessTokenDetails ) ){
      $this->returnError('Invalid ACCESSTOKEN.','402');
    }

    $userDetails = User::userByID( $accessTokenDetails['user_id'] );

    if( empty($userDetails) ){
      $this->returnError('Invalid Data.','-1');
    }

    /*Check Expire Time*/
    $expiry_on  = $accessTokenDetails['expiry_on'];

    $futureDate = strtotime("+2 hours");

    if(time() <= ($expiry_on)){

      /*Time greater than expiry time and less than exiry time +30 then  increment expiry time by 5 eech request*/ 
      $expiry_on_update = $futureDate;

      /*Update to Db with login info id of last record*/
      //Auth::updateExpiryTime($accessTokenDetails['id'],$expiry_on_update,$accessTokenDetails['userid']);
    }else{
      $this->returnError('Token has expired login again','401');
      //$this->returnSessionError();
    }
    /*check token are equal*/

    $userDetails = User::userByID($accessTokenDetails['user_id']);
  
    if(empty ($userDetails ) ){
      $this->returnError('User is not in our record.','-1');
    }

    if( $userDetails['deleted_at'] != NULL ){
      $this->returnError('Sorry! Your account has been suspended by admin.','-3');
    }

   
    
    
    
      
    
      
     
    return $userDetails;

  }  

  /* Return Session Error */
  public function returnSessionError(){
    $return["status"] = -1;
    $return["message"] = "Your session has expired. Please login again.";
    $encodeArray =  json_encode($return);
    print $encodeArray;
    exit;
  }


}