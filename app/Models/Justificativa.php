<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justificativa extends Model
{
    use HasFactory;

    protected $table = 'justificativas';

    protected $fillable = [
        'id_usuario',
        'cotacao_id',
        'item_id',
        'valor_unitario',
        'descricao',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function cotacao()
    {
        return $this->belongsTo(\App\Models\Cotacao::class, 'cotacao_id');
    }

    public function item()
    {
        return $this->belongsTo(\App\Models\ItensCotacao::class, 'item_id');
    }
}
