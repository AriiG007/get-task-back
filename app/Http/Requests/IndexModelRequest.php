<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class IndexModelRequest extends FormRequest
{
    protected string $model = '';

    public function rules(): array
    {

        $filterConfigClass = $this->model::filterConfigClass();

        /**
         * Se obtiene los filtros permitidos del modelo
         * Si no hay filtros permitidos, no se aplica ninguna validación
         */
         $allowedFilters = $filterConfigClass::filters();

        if (!isset($allowedFilters) || empty($allowedFilters)) {

            return [];
        }

        Log::info('allowedFiltersbindexmodel request: ', $allowedFilters);

        /** Se agrega la validacion de filtros permitidos */

        $rules =  [
            'filters' => [
                'sometimes',
                'array',
                function ($attribute, $value, $fail) use ($allowedFilters) {
                    foreach ($value as $key => $val) {
                        if (!isset($allowedFilters[$key])) {
                            $fail("The filter '{$key}' is not allowed.");
                        }
                    }
                }
            ]
        ];

        foreach ($allowedFilters as $filter => $filter_config) {


            /** Valida si existe el filtro relations para agregar validaciones de relaciones permitidas
             * para filtrar el modelo
             */
            if($filter === 'relations' && isset($filter_config['in'])) {
                $rules['filters.relations'] = ['sometimes', 'array'];
                $rules['filters.relations.*'] = ['string', Rule::in($filter_config['in'])];
                continue;
            }

            /**
             * Valida si el filtro tiene una clave 'type' que indica el tipo de dato esperado.
             * Si no se especifica, se asume que es 'string'.
             */

            $type = $filter_config['type'] ?? 'string';

            /**
             * Si el filtro tiene una clave 'exists' que contiene 'table' y 'column',
             * se valida que los valores sean del tipo $type y existan en la tabla y columna especificadas.
             * Por ejemplo, si el filtro es 'role_id' y tiene 'exists' con 'table' => 'roles' y 'column' => 'id'
             * entonces se agregara la validacion del filtro para que los valores sean del tipo $type y existan en la tabla 'roles' y columna 'id'.
             */

            if(isset($filter_config['exists']) && isset($filter_config['exists']['table'], $filter_config['exists']['column'])) {
                $rules['filters.' . $filter] = ['sometimes', 'array'];
                $rules['filters.' . $filter . '.*'] = [$type, 'exists:' . $filter_config['exists']['table'] . ',' . $filter_config['exists']['column']];
                continue;
            }

            /**
             * Si el filtro tiene una clave 'in', significa que puede contener un conjunto de valores permitidos.
             * En este caso, se valida que los valores sean del tipo especificado por $type y estén dentro de los valores permitidos en 'in'.
             * Si no tiene 'in', se valida que el valor sea del tipo especificado por $type.
             */

            if (isset($filter_config['in'])) {
                $rules['filters.' . $filter] = ['sometimes', 'array'];
                $rules['filters.' . $filter . '.*'] = [$type, Rule::in($filter_config['in'])];

            }else{
                $rules['filters.' . $filter] = ['sometimes', $type];
            }

        }

         Log::info('rules: ', $rules);

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
