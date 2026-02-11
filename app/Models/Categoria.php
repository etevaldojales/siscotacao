<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'user_id',
    ];

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'category_id');
    }

    public function fornecedores()
    {
        return $this->belongsToMany(Fornecedor::class, 'categoria_fornecedor', 'categoria_id', 'fornecedor_id');
    }

    public function usersComprador()
    {
        return $this->belongsToMany(User::class, 'categoria_user_comprador', 'categoria_id', 'user_id');
    }
}
