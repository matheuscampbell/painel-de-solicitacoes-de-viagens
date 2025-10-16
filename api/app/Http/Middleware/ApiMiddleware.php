<?php
namespace App\Http\Middleware;

use Closure;
use App\Http\Responses\ApiResponse;

class ApiMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        // Verifique se a resposta não é uma instância de ApiResponse
        if (! $response instanceof ApiResponse && false) {
            // Envie os dados como uma resposta de sucesso padrão
            //check if is error
            if($response->status() >= 400){
                $response = ApiResponse::error($response->getData(true), $response->status());
            }else{
                $response = ApiResponse::success($response->getData(true), $response->status());
            }
        }
        return $response;
    }
}
