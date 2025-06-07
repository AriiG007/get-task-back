<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class RolesSeeder extends Seeder
{

    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'description' => 'Administrator with full access'],
            ['name' => 'team_leader', 'description' => 'Lead of the team with task management access'],
            ['name' => 'senior_team_member', 'description' => 'Senior member of the team with task management access'],
            ['name' => 'team_member', 'description' => 'Member of the team with read-only access and self-task management'],
        ];

        /** Permisos exclusivos para super_admin, team_leader, senior_team_member */
        $adminsPermission = [
            ['name' => 'create_users',  'permission' => 'create.users',  'description' => 'Permission to create users'],
            ['name' => 'approve_users',  'permission' => 'approve.users', 'description' => 'Permission to approve users registrations'],
            ['name' => 'edit_users', 'permission' => 'edit.users',    'description' => 'Permission to edit users'],
            ['name' => 'reset_password_users', 'permission' => 'reset.password.users', 'description' => 'Permission to reset user passwords'],

            ['name' => 'list_users', 'permission' => 'list.users', 'description' => 'Permission to view users'],
            ['name' => 'list_roles', 'permission' => 'list.roles', 'description' => 'Permission to view roles'],

            ['name' => 'assign_tasks', 'permission' => 'assign.tasks', 'description' => 'Permission to assign tasks'],
            ['name' => 'list_all_tasks', 'permission' => 'list.all.tasks', 'description' => 'Permission to list all team tasks'],
        ];

        /** Permisos para team_member */
        $membersPermissions = [
            ['name' => 'list_tasks', 'permission' => 'list.tasks', 'description' => 'Permission to view tasks'],
            ['name' => 'create_tasks', 'permission' => 'create.tasks', 'description' => 'Permission to create tasks'],

            ['name' => 'edit_tasks', 'permission' => 'edit.tasks', 'description' => 'Permission to edit tasks'],
            ['name' => 'move_task_stage', 'permission' => 'move.task.stage', 'description' => 'Permission to move tasks between stages'],
        ];


        /** Crear/actualizar permisos y obtener ids */

        $membersPermissionsIds = [];
        $adminsPermissionIds = [];


        foreach ($adminsPermission as $permiso) {
            $permiso_id = Permission::updateOrCreate(['permission' => $permiso['permission']], $permiso)->id;
            $adminsPermissionIds[] = $permiso_id;
        }

        foreach ($membersPermissions as $permiso) {
            $permiso_id = Permission::updateOrCreate(['permission' => $permiso['permission']], $permiso)->id;
            $membersPermissionsIds[] = $permiso_id;
            $adminsPermissionIds[] = $permiso_id;
        }

        /** Crear/actualizar roles y asignar permisos */

        foreach ($roles as $role) {
            $rolePermissions = $role['name'] === 'team_member' ? $membersPermissionsIds : $adminsPermissionIds;

            Log::info("Creating role: {$role['name']} with permissions: " , $rolePermissions);

            $rol = Role::updateOrCreate($role);

            $rol->permissions()->sync($rolePermissions);

        }

    }
}
