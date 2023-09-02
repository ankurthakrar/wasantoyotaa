<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use Exception;
use App\customclasses\Corefunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model{
    use HasFactory, SoftDeletes;

    protected $table = 'stocks';

    public static function checkStockByVinNo($vinno)
    {
        $Corefunctions = new \App\customclasses\Corefunctions;
        $row = DB::table('stocks')->where('vin_no', $vinno)->limit(1)->first();
        $row = $Corefunctions->convertToArray($row);
       
        return $row;
    }

}