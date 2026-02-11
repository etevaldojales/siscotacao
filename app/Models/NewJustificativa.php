<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewJustificativa extends Model
{
    use HasFactory;

    protected $table = 'justificativa';

    protected $fillable = [
        'descricao',
        'status',
    ];
}
