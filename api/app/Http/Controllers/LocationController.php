<?php

namespace App\Http\Controllers;

use App\Exceptions\Base\DomainException;
use App\Http\Requests\Locations\CityLookupRequest;
use App\Http\Resources\CityResource;
use App\Services\Locations\CityLookupService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class LocationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CityLookupService $cityLookupService
    ) {
        $this->middleware('auth:api');
    }

    public function cities(CityLookupRequest $request): JsonResponse
    {
        try {
            $cities = $this->cityLookupService->search($request->toDto());

            return $this->success(
                CityResource::collection($cities),
                200,
                'Cidades recuperadas com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao consultar cidades.');
        }
    }

    private function handleException(Throwable $exception, string $logMessage): JsonResponse
    {
        if ($exception instanceof DomainException) {
            return $this->error($exception->getMessage(), $exception->statusCode());
        }

        Log::error($logMessage, [
            'exception' => $exception,
        ]);

        return $this->error('Ocorreu um erro interno. Tente novamente mais tarde.', 500);
    }
}

