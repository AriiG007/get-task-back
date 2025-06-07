<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\ModelsFilters\UsersFilters;
use App\Models\Role;
use App\Models\Permission;
use App\Traits\ModelFilters;
use App\Models\Team;


class User extends Authenticatable implements JWTSubject
{

    use HasUuids, HasFactory, SoftDeletes, ModelFilters;



    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'team_id',
        'role_id',
        'status',
        'is_validated',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public static function filterConfigClass(): string
    {
        return UsersFilters::class;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role->name === 'super_admin';
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('permission',  $permission)->exists();
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,         // Modelo destino
            'roles_permissions',       // Tabla pivote
            'role_id',               // Clave foránea en la tabla pivote (hacia roles)
            'permission_id',           // Clave relacionada en la tabla pivote (hacia permisos)
            'role_id',               // Clave local en este modelo (User → rol_id)
            'id'                    // Clave local en el modelo Rol (roles.id)
        );
    }



    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

        /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
