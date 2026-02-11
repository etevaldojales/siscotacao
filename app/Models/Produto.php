<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'codigo',
        'name',
        'description',
        'price',
        'stock',
        'status',
        'user_id',
        'category_id',
        'cnpj_comprador',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'category_id');
    }

    public function marcas()
    {
        return $this->belongsToMany(Marca::class, 'marca_produto', 'produto_id', 'marca_id');
    }


    // Remove comprador() method as it conflicts with compradores() relationship

    public function getNomeProduto($id) {
        $produto = Produto::find($id);
        return $produto->name;
    }
}
