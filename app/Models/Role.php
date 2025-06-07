<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Permission;
use App\Models\RolePermission;

class Role extends Model
{
    use HasUuids;


    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

     protected $hidden = [
        'created_at',
        'updated_at',
    ];




    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions', 'role_id', 'permission_id')
                ->using(RolePermission::class)
                ->withTimestamps();
    }

}
