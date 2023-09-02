<?php

namespace App\customclasses;
use DB;

use App\Models\User;
 
 
use Illuminate\Support\Facades\Storage;
use Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
 

class Corefunctions
{

	public function __construct(){



	}
	function checkDuplicate( $table, $field1, $fieldvalue1, $field2 = '', $fieldvalue2 = '' ) {
        global $db;
        $db1   = $db;
        $query = DB::table($table)->where($field1, $fieldvalue1)->limit(1)->first();

        return $query ? true : false;
    }
      
    function generateUniqueKey( $count, $table, $field ) {
        $ukey = $this->GUID();
        while ( $this->checkDuplicate( $table, $field, $ukey ) ) {
            $ukey = $this->GUID();
        }
        return $ukey;
    }

    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }



    function convertToArray($dataArray){
        $dataArray =json_decode(json_encode($dataArray), true);
        return $dataArray;
    }
	 
     

     

      
    function getArrayIndexed( $array, $index ) {
        $finalArray = array( );
        if ( !empty( $array ) ) {
            foreach ( $array as $a ) {
                $finalArray[ $a->$index  ] = $a;
            }
        }
        return $finalArray;
    }


    function getIDSfromArray( $data,$field ){
            if( empty($data) || !is_array($data)) return array();
            $return = array();
            foreach( $data as $d ){
              $return[] = $d[$field];
            }
            return $return;
          }
    function getArrayIndexed1( $array, $index ) {
        $finalArray = array( );
        if ( !empty( $array ) ) {
            foreach ( $array as $a ) {

                $finalArray[ $a[$index]  ] = $a;
            }
        }
        return $finalArray;
    }
      
    

     

    
    


   

     

}
