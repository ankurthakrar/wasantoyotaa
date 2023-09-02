<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use Exception;
use App\customclasses\Corefunctions;

class Auth extends Model
{
    protected $table = 'users';
    
    public static function getAPPIdDetails($appId){
      return DB::table('app_keys')->select('*')->where('app_name',$appId)->first();
    }
    
 
  public static function validateUserByUserName($username){ 
    $Corefunctions = new \App\customclasses\Corefunctions;
    $row = DB::table('users')->where('username',strtolower($username))->limit(1)->first();
    $row = $Corefunctions->convertToArray($row);
   
    return $row; 
  }

   public static function validateUserByEmailID($email){    
    $Corefunctions = new \App\customclasses\Corefunctions;
    $row = DB::table('users')->where('email',strtolower($email))->first();
    if(!$row)
      throw new Exception(config('constants.MESSAGE.1002'));
        
    $row = $Corefunctions->convertToArray($row);   
    return $row; 
  }   
   
   
  public static function validateUserByUUID($uuid){
    $Corefunctions = new \App\customclasses\Corefunctions;
    $row = DB::table('users')->where('user_uuid',$uuid)->first();
    if(!$row)
      throw new Exception(config('constants.VALIDATIONS.UUID_NOT_EXIST_MESSAGE'));
         
    $row = $Corefunctions->convertToArray($row);   
    return $row; 
  }


   public static function getAccessDetails($accesstoken){ 
        $Corefunctions = new \App\customclasses\Corefunctions;
        $result = DB::table('auth_tokens')->select('id','auth_token','expiry_on','user_id')->where('auth_token',$accesstoken)->orderBy('id', 'desc')->first();
        $result = $Corefunctions->convertToArray($result);
        return $result;
    }  
    

    

    public static function addAuthTokens($userid,$expiry_on,$token,$ip) {
        $Corefunctions = new \App\customclasses\Corefunctions;
         
        $logininfoid = DB::table('auth_tokens')->insertGetId(array(
            'user_id' => $userid,
            'auth_token' => $token,
            'ip_address' => $ip,
            'expiry_on' => strtotime('+1 year', $expiry_on),
            'created_at' => Carbon\Carbon::now()
         ));
       return $logininfoid; 
    }

    public static function addUserInfo($inputParameters,$passwordHash){

      $role = (isset($inputParameters['role']) ) ? $inputParameters['role'] :''; 
      $team = (isset($inputParameters['team']) ) ? $inputParameters['team'] :''; 
      $status = (isset($inputParameters['status']) ) ? $inputParameters['status'] :0; 
		return DB::table('users')->insertGetId(array(
		  'first_name' =>  $inputParameters["firstname"],
		  'last_name' => $inputParameters["lastname"],
		  'email' =>strtolower($inputParameters["email"]),
      'username' =>strtolower($inputParameters["username"]),
       
		  'password'=>$passwordHash,
      'role'=>$role,
      'team'=>$team,
      'status'=>$status,
		  'created_at' => Carbon\Carbon::now()
		  	 
		));
       
    } 

  public static function updateProfile($inputParameters,$userID){
    return DB::table('users')->where('id', $userID)->limit(1)->update(array(
      'first_name' =>  $inputParameters["firstname"],
      'last_name' => $inputParameters["lastname"],
      'email' =>strtolower($inputParameters["email"]),
      'updated_at'        => Carbon\Carbon::now()
      ));
  }

 
    

    

 
  
 
 

     
}
