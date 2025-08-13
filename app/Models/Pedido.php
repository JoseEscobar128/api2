<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'cliente_id',
        'usuario_creo_id',
        'modalidad_pedido_id',
        'estado_pedido_id',
        'mesa_id',      
        'total_pedido',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'usuario_creo_id');
    }

    public function usuarioCreo()
    {
        return $this->belongsTo(Usuario::class, 'usuario_creo_id');
    }

    public function modalidad()
    {
        return $this->belongsTo(ModalidadPedido::class, 'modalidad_pedido_id');
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function estadoPedido()
    {
        return $this->belongsTo(EstadoPedido::class, 'estado_pedido_id');
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }
}
