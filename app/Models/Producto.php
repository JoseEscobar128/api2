<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;
     protected $fillable = [
        'categoria_id',
        'sucursal_id',
        'nombre',
        'descripcion',
        'precio',
        'imagen_principal',
    ];

    protected $casts = [
        'imagen_principal' => 'array',
    ];
    

   
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

  
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

}


