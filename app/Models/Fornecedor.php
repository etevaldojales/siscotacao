<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    use HasFactory;

    public $table = 'fornecedores';

    protected $fillable = [
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'email',
        'email2',
        'inscricao_estadual',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'telefone',
        'celular',
        'whatsapp',
        'tipo',
        'cnpj_matriz',
        'status',
    ];

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categoria_fornecedor', 'fornecedor_id', 'categoria_id');
    }
}
