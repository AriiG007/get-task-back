<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\User\IndexModelRequest;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UpdateUserRequest;


class UserController extends Controller
{
    public function __construct(private UserService $userService) {}


    public function index(UserIndexRequest $request)
    {
        $filters = $request->validated()['filters'] ?? [];
        $paginate = $request->has('paginate');

        $users = $this->userService->getAllUsers($filters, $paginate);

        return response()->json($users, Response::HTTP_OK);
    }

    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Para auto registro de usuario.
     *
     * @param  \App\Http\Requests\User\RegisterUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterUserRequest $request)
    {
        $this->userService->registerSelf($request->validated());
        return response()->json(['message' => 'Registered user, pending approval'], Response::HTTP_CREATED);
    }

    /**
     * Crear un usuario por parte de un administrador o usuario con permisos para ello.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterUserRequest $request)
    {

        $user = $this->userService->registerByUser($request->validated());
        return response()->json(['message' => 'User created'], Response::HTTP_CREATED);
    }


    public function update($id, UpdateUserRequest $request)
    {
        if(empty($request->all())) {
            return response()->json(['message' => 'No data provided'], Response::HTTP_BAD_REQUEST);
        }

        $this->userService->updateUser($id, $request->validated());

        return response()->json(['message' => 'User updated'], Response::HTTP_OK);
    }

    /**
     * Aprobar un usuario pendiente de validación.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approveUser($id)
    {
        $this->userService->updateUser($id, ['is_validated' => true]);
        return response()->json(['message' => 'User approved'], Response::HTTP_OK);
    }

    /**
     * Asignar un nuevo password a un usuario.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetPassword($id)
    {
        $this->userService->resetPasswordUser($id);
        return response()->json(['message' => 'Password reset successfully'], Response::HTTP_OK);
    }


    /**
     * Eliminar un usuario.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted'], Response::HTTP_OK);
    }


}
