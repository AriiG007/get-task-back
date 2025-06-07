<?php

namespace App\ModelsFilters;

use Illuminate\Validation\Rule;

class UsersFilters
{
    /**
     * Define los filtros para el modelo User.
     * Las key del arreglo, define los filtros que estan permitidos
     * La estructura de cada filtro se usa para generar las validaciones que se van hacer
     * en el request para poder aplicar los filtros, por ejemplo:
     *  'role_id' => [
     *           'type' => 'integer',
     *           'exists' => ['table' => 'roles', 'column' => 'id'],
     *       ],
     * type define el tipo de dato que se espera en el request en este caso, se espera que sean identificadores
     * de roles, exists define en que tabla de la base de datos debe existir y que columna hace referencia
     * a esos identificadores.
     * Nota: Revisar la implementacion en app/Http/Requests/IndexModelRequest.php
     */
    public static function filters(): array
    {
        return [
            'role_id' => [
                'type' => 'string',
                'exists' => ['table' => 'roles', 'column' => 'id'],
            ],
            'team_id' => [
                'type' => 'string',
                'exists' => ['table' => 'teams', 'column' => 'id'],
            ],
            'status' => [
                'type' => 'string',
                'in' => ['active', 'inactive'],
            ],
            'is_validated' => [
                'type' => 'boolean',
            ],
            'relations' => [
                'in' => ['role', 'team'],
            ],
        ];
    }
}
