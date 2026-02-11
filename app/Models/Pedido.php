<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'id_usuario',
        'id_fornecedor',
        'cotacao_id',
        'numero',
        'valor',
        'actived',
        'status',
        'forma_pagamento',
        'prazo_entrega',
        'tipo_frete',
        'valor_frete',
        'observacao',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }

    public function itens()
    {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }
}
