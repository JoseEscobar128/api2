<?php

namespace App\Models\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class UserFromApi extends Authenticatable
{
    use HasRoles;

    /**
     * El guard que usará Spatie para este modelo.
     * Es crucial que coincida con la configuración del guard en config/auth.php.
     */
    protected $guard_name = 'token-introspection';

    /**
     * No hay tabla de usuarios para este modelo.
     */
    protected $table = null;
    
    protected $fillable = ['id', 'name', 'email'];

    /**
     * Sobrescribe el método findById de Spatie para usar la base de datos de la API 1.
     * O simplemente lo dejamos como está para que no se use.
     * En este caso, no hay que buscar, ya que el usuario ya viene con los datos.
     */
    public static function findById(int $id, string $guardName): ?\Illuminate\Database\Eloquent\Model
    {
        // No buscamos en la base de datos local.
        return null;
    }
}