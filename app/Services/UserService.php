<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\ResetPasswordUserNotification;
use App\Notifications\CreatedUserNotification;
use App\Models\Role;
use App\Notifications\AwaitingApprovalNotification;



class UserService{

    public function getUserById(string $id): User
    {
        return User::with('role', 'team')->findOrFail($id);
    }


    public function getAllUsers($filters, $paginate = false)
    {

        $query = User::applyFilters($filters);

        $query->orderBy('name', 'asc');

        if($paginate)
            return $query->paginate(10);

        return $query->get();
    }

     // Autoregistro del usuario
    public function registerSelf(array $data): User
    {

        //
        /**
         * USER_VALIDATION_REQUIRED indica si el autoregistro requiere validacion por administrador,
         * si esta como false automatocamente se registra el usaurio como validado
         * el usuario no puede inciar sesion ni crear tareas is_validated es falso
         */
        $user = $this->createUser([
            ...$data,
            'password' => Hash::make($data['password']),
            'status'   => 'active',
            'is_validated' => (env('USER_VALIDATION_REQUIRED') == 'false')
        ]);


        AwaitingApprovalNotification::sendMail($user->email);

        return $user;
    }

    // Registro de usuario hecho por otro usuario, por ejemplo un administrador
    public function registerByUser(array $data): User
    {

        // validar que el usuario autenticado pueda crear un nuevo usuario con el rol que envia en el request.
        $this->validateUserRoleCreation($data);

        $randomPassword = Str::random(10);

        $user = $this->createUser([
            ...$data,
            'password' => Hash::make($randomPassword),
            'is_validated' => true,
        ]);


        CreatedUserNotification::sendMail($user->email, $randomPassword);

        return $user;
    }

    // Crear usuario
    private function createUser(array $data): User
    {
        Log::info('usuario auth regi', $data);
        return User::create($data);
    }

    public function updateUser(string $id, array $data): User
    {
        Log::info('Updating user with ID: ' . $id, ['data' => $data, 'assa' => 'assa']);
        Log::info('type id: ' . \gettype($id));
        $user = User::findOrFail($id);

       $user->update($data);
       return $user;

    }

    public function resetPasswordUser(string $id): void
    {
        $user = User::findOrFail($id);
        $randomPassword = Str::random(10);
        $user->update(['password' => Hash::make($randomPassword)]);

        ResetPasswordUserNotification::sendMail($user->email, $randomPassword);
    }

    public function deleteUser(string $id): void
    {
        $user = User::findOrFail($id);

        if($user->tasks()->count() > 0) {
            throw new \Exception('Cannot delete user with assigned tasks');
        }

        $user->delete();
    }

    /**
     * Validates que el usuario autenticado pueda crear un nuevo usuario con el rol que envia en el request.
     * por ahora solo valida los roles de tipo super_admin.
     * @param array $data
     * @throws \Exception
     */
    private function validateUserRoleCreation(array $data): void
    {
        $authUser = auth()->user();
        $requestRole = Role::findOrFail($data['role_id']);

        if ($requestRole->name !== 'super_admin' && !$authUser->isSuperAdmin()) {
            throw new \Exception('Unauthorized action');
        }
    }


}
