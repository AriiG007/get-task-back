<?php

namespace App\ModelsFilters;

use Illuminate\Validation\Rule;

    /**
     * Define los filtros para el modelo User.
     * Las key del arreglo, define los filtros que estan permitidos
     * La estructura de cada filtro se usa para generar las validaciones que se van hacer
     * en el request para poder aplicar los filtros, por ejemplo:
     *  'user_id' => [
     *           'type' => 'integer',
     *           'exists' => ['table' => 'users', 'column' => 'id'],
     *       ],
     * type define el tipo de dato que se espera en el request en este caso, se espera que sean identificadores
     * de usuarios, exists define en que tabla de la base de datos debe existir y que columna hace referencia
     * a esos identificadores.
     * Nota: Revisar la implementacion en app/Http/Requests/IndexModelRequest.php
     */

class TasksFilters
{
    public static function filters(): array
    {
        return [
            'is_active' => [
                'type' => 'boolean',
            ],
            'user_id' => [
                'type' => 'string',
                'exists' => ['table' => 'users', 'column' => 'id'],
            ],
            'stage_id' => [
                 'type' => 'string',
                'exists' => ['table' => 'stages', 'column' => 'id']
            ],
            'relations' => [
                'in' => ['user'],
            ],
        ];
    }
}
