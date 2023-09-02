<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Illuminate\Support\Facades\DB;
use Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    public $timestamps = false;

   

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    // protected $fillable = [
    //     'firstname', 'lastname', 'password','email
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];



    public static function userByID($userID)
    {
        $Corefunctions = new \App\customclasses\Corefunctions;
        $row = DB::table('users')->where('id', $userID)->first();
        $row = $Corefunctions->convertToArray($row);
        if (!empty($row)) {
            //$row['userImg'] =$Corefunctions->getUserImg($row);
        }
        return $row;
    }

    public static function checkUserByEmail($email)
    {
        $Corefunctions = new \App\customclasses\Corefunctions;
        $row = DB::table('users')->where('email', strtolower($email))->limit(1)->first();
        $row = $Corefunctions->convertToArray($row);
       
        return $row;
    }
    public static function checkUserEmailByUserId($userID, $email)
    {
        $Corefunctions = new \App\customclasses\Corefunctions;
        $result = DB::table('users')->select('first_name', 'last_name', 'id', 'email')->where('email', strtolower($email))->where('id', '!=', $userID)->first();
        $result = $Corefunctions->convertToArray($result);
        return $result;
    }
   

    

   
 
       
   
 
  
 
  
    

 
    
}
