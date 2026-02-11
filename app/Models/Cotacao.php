<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotacao extends Model
{
    use HasFactory;

    protected $table = 'cotacoes';

    protected $fillable = [
        'numero',
        'inicio',
        'encerramento',
        'status',
        'status_envio',
        'id_usuario',
        'descricao',
        'observacao',
        'endereco_entrega',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'encerramento' => 'datetime',
    ];
}
