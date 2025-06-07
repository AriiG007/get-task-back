<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Role;
use App\Models\RolePermission;

class Permission extends Model
{
      use HasUuids;

      protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'permission',
        'description',
    ];


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions', 'permission_id', 'role_id')
                ->using(RolePermission::class)
                ->withTimestamps();
    }

}
