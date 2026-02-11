<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItensCotacao extends Model
{
    use HasFactory;

    protected $table = 'itens_cotacao';

    protected $fillable = [
        'cotacao_id',
        'product_id',
        'marca_id',
        'quantidade',
        'unidade',
        'valor',
        'observacao',
    ];

    public function cotacao()
    {
        return $this->belongsTo(Cotacao::class, 'cotacao_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'product_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }
}
