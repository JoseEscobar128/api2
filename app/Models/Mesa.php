<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    protected $table = 'mesas';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'capacidad',
        'ocupada',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'capacidad' => 'integer',
        'ocupada' => 'boolean',
    ];

    /**
     * Una mesa puede tener varios pedidos (relación opcional).
     */
    public function pedidos()
    {
        // Ajusta el namespace si tu modelo Pedido está en otra carpeta
        return $this->hasMany(\App\Models\Pedido::class, 'mesa_id');
    }

    /**
     * Marca la mesa como ocupada.
     */
    public function marcarOcupada(): void
    {
        $this->ocupada = true;
        $this->save();
    }

    /**
     * Marca la mesa como libre.
     */
    public function marcarLibre(): void
    {
        $this->ocupada = false;
        $this->save();
    }

    /**
     * Scope para mesas libres.
     */
    public function scopeLibres($query)
    {
        return $query->where('ocupada', false);
    }

    /**
     * Scope para mesas ocupadas.
     */
    public function scopeOcupadas($query)
    {
        return $query->where('ocupada', true);
    }
}
