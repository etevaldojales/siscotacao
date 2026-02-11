<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FornecedorCotacao extends Model
{
    use HasFactory;

    protected $table = 'fornecedor_cotacao';

    protected $fillable = [
        'cotacao_id',
        'item_id',
        'fornecedor_id',
        'valor_unitario',
        'valor_total',
        'status',
        'forma_pagamento',
        'prazo_entrega',
        'tipo_frete',
        'valor_frete',
        'faturamento_minimo',
        'observacao',
    ];

    public function cotacao()
    {
        return $this->belongsTo(Cotacao::class, 'cotacao_id');
    }

    public function item()
    {
        return $this->belongsTo(ItensCotacao::class, 'item_id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }
}
