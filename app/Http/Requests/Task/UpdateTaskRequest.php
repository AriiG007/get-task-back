<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function rules(): array
    {
        /**
         * Request de validacion usado para hacer asignacion, cancelación o edición de tareas.
         * Valida que ruta es la que se está accediendo y aplica las reglas correspondientes.
         * Por defecto se asume que es la de editar y se retorna validaciones para editar una tarea.
         */

        switch($this->route()->getName()) {
            case 'assign.task':
                return ['user_id' => 'required|exists:users,id'];
            case 'cancel.task':
                return ['cancellation_reason' => 'required|string|max:255'];
            default:
                return [
                    'name' => 'sometimes|required|string|max:100',
                    'description' => 'sometimes|required|string|max:255'
                ];
        }

    }

    public function authorize(): bool
    {
        return true;
    }
}
