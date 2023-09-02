<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use Exception;
use App\customclasses\Corefunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelMst extends Model{
    use HasFactory, SoftDeletes;

    protected $table = 'models';

}