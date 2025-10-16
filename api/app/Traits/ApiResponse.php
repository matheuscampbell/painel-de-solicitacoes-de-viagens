<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Retorna uma resposta JSON de sucesso.
     *
     * @param mixed $data Dados a serem incluídos na resposta.
     * @param int $status Código de status HTTP (padrão: 200).
     * @param array $headers Cabeçalhos HTTP adicionais (opcional).
     * @return JsonResponse
     */
    public function success(mixed $data, int $status = 200, $message = "Success", array $headers = [])
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => 'success',
        ], $status, $headers);
    }

    public function paginatedSuccess(LengthAwarePaginator $paginator, mixed $data, string $message = 'Success', array $headers = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
            'message' => $message,
            'status' => 'success',
        ], 200, $headers);
    }

    /**
     * Retorna uma resposta JSON de erro.
     *
     * @param string $message Mensagem de erro.
     * @param $status Código de status HTTP (padrão: 400).
     * @return JsonResponse
     */
    public function error(string $message, mixed $status = 500): JsonResponse
    {
        if($status == 0)
            $status = 500;
        return response()->json([
            'error' => $message,
            'status' => 'error',
            'success' => false,
        ], $status);
    }
}
