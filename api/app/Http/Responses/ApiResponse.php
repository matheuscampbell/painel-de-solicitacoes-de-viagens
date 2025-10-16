<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse extends JsonResponse
{
    public function __construct($data = null, $status = 200,$message ="Requisição realizada com sucesso!", $headers = [], $options = 0)
    {
        parent::__construct([
            'data' => $data,
            'status' => $status,
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
        ], $status, $headers, $options);
    }

    /**
     * @param $data array|string if string, will be used as message
     * @param $status
     * @return ApiResponse
     */
    public static function success($data = null, $status = 200): ApiResponse
    {
        $msg = "Requisição realizada com sucesso!";
        if(is_string($data)){
            $msg = $data;
            $data = null;
        }
        return new self($data, $status,$msg);
    }

    public static function error($message, $status = 400): ApiResponse
    {
        if($message == "Unauthenticated.")
            $message = "Usuário não autenticado. Faça login novamente.";
        return new self(['error' => $message], $status,$message);
    }
}
