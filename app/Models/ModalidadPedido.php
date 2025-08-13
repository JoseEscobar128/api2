<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModalidadPedido extends Model
{
    protected $fillable = ['nombre'];
    protected $table = 'modalidades_pedido';

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'modalidad_pedido_id');
    }
}
