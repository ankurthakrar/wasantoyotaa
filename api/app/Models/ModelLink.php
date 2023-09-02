<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use Exception;
use App\customclasses\Corefunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelLink extends Model{
    use HasFactory, SoftDeletes;

    protected $table = 'model_links';

    protected $appends = ['model_new_name','grade_new_name','int_color_new_name','suffix_new_name','ext_color_new_name'];

    public function getModelNewNameAttribute()
    {
        if ($this->model_id > 0) {
            return ModelMst::where('id',$this->model_id)->pluck('name')->first() ?? null;
        }
        return null;
    }
    public function getGradeNewNameAttribute()
    {
        if ($this->grade_id > 0) {
            return Grade::where('id',$this->grade_id)->pluck('name')->first() ?? null;
        }
        return null;
    }
    public function getIntColorNewNameAttribute()
    {
        if ($this->int_color_id > 0) {
            return IntColor::where('id',$this->int_color_id)->pluck('name')->first() ?? null;
        }
        return null;
    }
    public function getSuffixNewNameAttribute()
    {
        if ($this->suffix_id > 0) {
            return Suffix::where('id',$this->suffix_id)->pluck('name')->first() ?? null;
        }
        return null;
    }
    public function getExtColorNewNameAttribute()
    {
        if ($this->ext_color_id > 0) {
            return ExtColor::where('id',$this->ext_color_id)->pluck('name')->first() ?? null;
        }
        return null;
    }

}