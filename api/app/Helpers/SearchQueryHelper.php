<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class SearchQueryHelper
{
    public static function buildQuery($query, $request)
    {
        foreach ($request->validated() as $field => $value) {
            if ($value !== null) {
                // Assumindo que o campo e o valor podem ser diretamente mapeados para sua consulta
                $query->where($field, $value);
            }
        }
        return $query;
    }

    public static function buildQueryWithLike($query, $request)
    {
        foreach ($request->validated() as $field => $value) {
            if ($value !== null) {
                // Assumindo que o campo e o valor podem ser diretamente mapeados para sua consulta
                $query->where($field, 'like', "%$value%");
            }
        }
        return $query;
    }

    public static function buildQueryWithLikeArray($query, array $array, $semOrderBy = false)
    {
        $i = 0;
        $select = [];
        foreach ($array as $field => $value) {
            if ($value !== null) {
                if ($i === 0) {
                    $query->where($field, 'like', "%{$value}%");
                } else {
                    $query->orWhere($field, 'like', "%{$value}%");
                }
                $select[] = "(CASE WHEN $field LIKE '%$value%' THEN 1 ELSE 0 END)";
            }
            $i++;
        }
        if (!$semOrderBy) {
            $query->selectRaw("(".implode(' + ', $select) . ') as score');
            $query->orderBy('score', 'desc');
        }

        return $query;
    }

    public static function buildQueryFilter(Builder $query, $data): Builder
    {
        foreach ($data as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }
        return $query;
    }

    public static function buildQuerySearch(Builder $query, $data): Builder
    {
        foreach ($data as $field => $value) {
            if ($value !== null) {
                // Assumindo que o campo e o valor podem ser diretamente mapeados para sua consulta
                if(typeOf($value) === 'array'){
                    $query->whereIn($field, $value);
                }
                elseif (typeOf($value) === 'boolean' || typeOf($value) === 'integer') {
                    $query->where($field, $value);
                }
                else {
                    $query->where($field, 'like', "%$value%");
                }
            }
        }
        return $query;
    }
}
