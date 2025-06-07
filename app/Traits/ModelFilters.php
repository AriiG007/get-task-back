<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait ModelFilters
{
    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {


        $filterConfigClass = static::filterConfigClass();
        $allowedFilters = $filterConfigClass::filters();

        Log::info('aplicando filtors');


        foreach ($filters as $key => $value) {

            // Si el filtro no está en la lista de permitidos, lo ignoramos
            if (!isset($allowedFilters[$key])) {
                Log::info( $key. " no es un filtro valido ",[$value]);
                continue;
            }

            // si el filtro es relations, cargamos las relaciones
            if ($key === 'relations') {
                $query->with(is_array($value) ? $value : explode(',', $value));
                continue;
            }

            /**
             * Valida si el valor del filtro es un array, si no lo es, lo convierte en uno
             * para aplicar el filtro whereIn a la consulta ya sea que sea un solo valor o varios
             */
            $value = is_array($value) ? $value : explode(',', $value);

            Log::info($key.' filtro aplicado ',[$value]);

            $query->whereIn($key, $value);

        }

        return $query;
    }
}
