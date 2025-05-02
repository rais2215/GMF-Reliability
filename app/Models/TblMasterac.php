<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMasterac extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tbl_masterac';
    protected $primaryKey = 'IDreg';
    public $incrementing = false ;
    protected $fillable = [
        'IDType',
        'ACType',
        'ACReg',
        'Operator',
        'SerialModule',
        'VariableNumber',
        'SerialNumber',
        'ManufYear',
        'DEliveryDate',
        'EngineType',
        'Lessor',
        'Active',
        // Tambahkan kolom lain jika ada
    ];

    protected $casts = [
        'IDType' => 'integer',
        'SerialNumber' => 'integer',
        'ManufYear' => 'datetime',
        'DEliveryDate' => 'datetime',
        'Active' => 'integer',
    ];
    
}