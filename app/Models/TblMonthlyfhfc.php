<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMonthlyfhfc extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tbl_monthlyfhfc';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    protected $fillable = [
        'IDReg',
        'Reg',
        'Actype',
        'RevBHHours',
        'RevBHMin',
        'RevFHHours',
        'RevFHMin',
        'RevFC',
        'NoRevBHHours',
        'NoRevBHMin',
        'NoRevFHHours',
        'NoRevFHMin',
        'NoRevFC',
        'MonthEval',
        'AvaiDays',
        'TSN',
        'TSNMin',
        'CSN',
        'Remark',
    ];

    protected $casts = [
        'IDReg' => 'integer',
        'RevBHHours' => 'integer',
        'RevBHMin' => 'integer',
        'RevFHHours' => 'integer',
        'RevFHMin' => 'integer',
        'RevFC' => 'integer',
        'NoRevBHHours' => 'integer',
        'NoRevBHMin' => 'integer',
        'NoRevFHHours' => 'integer',
        'NoRevFHMin' => 'integer',
        'NoRevFC' => 'integer',
        'MonthEval' => 'date',
        'AvaiDays' => 'integer',
        'TSN' => 'integer',
        'TSNMin' => 'integer',
        'CSN' => 'integer',
    ];
}