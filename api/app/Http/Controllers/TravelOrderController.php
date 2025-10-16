<?php

namespace App\Http\Controllers;

use App\Dtos\TravelOrders\TravelOrderIdentifierDto;
use App\Http\Requests\TravelOrders\TravelOrderIndexRequest;
use App\Http\Requests\TravelOrders\TravelOrderStatusUpdateRequest;
use App\Http\Requests\TravelOrders\TravelOrderStoreRequest;
use App\Http\Resources\TravelOrderResource;
use App\Services\TravelOrderService;
use App\Exceptions\Base\DomainException;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class TravelOrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly TravelOrderService $travelOrderService
    ) {
        $this->middleware('auth:api');
    }

    public function index(TravelOrderIndexRequest $request): JsonResponse
    {
        try {
            $dto = $request->toDto();
            $travelOrders = $this->travelOrderService->list($dto);

            if ($travelOrders instanceof LengthAwarePaginator) {
                return $this->paginatedSuccess(
                    $travelOrders,
                    TravelOrderResource::collection($travelOrders->getCollection()),
                    'Pedidos de viagem listados com sucesso.'
                );
            }

            return $this->success(
                TravelOrderResource::collection($travelOrders),
                200,
                'Pedidos de viagem listados com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao listar pedidos de viagem.');
        }
    }

    public function store(TravelOrderStoreRequest $request): JsonResponse
    {
        try {
            $dto = $request->toDto();
            $travelOrder = $this->travelOrderService->create($dto);

            return $this->success(
                new TravelOrderResource($travelOrder),
                201,
                'Pedido de viagem criado com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao criar pedido de viagem.');
        }
    }

    public function show(string $travelOrder): JsonResponse
    {
        try {
            $dto = new TravelOrderIdentifierDto(['uuid' => $travelOrder]);
            $travelOrder = $this->travelOrderService->show($dto);

            return $this->success(
                new TravelOrderResource($travelOrder),
                200,
                'Pedido de viagem recuperado com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao recuperar pedido de viagem.');
        }
    }

    public function updateStatus(TravelOrderStatusUpdateRequest $request, string $travelOrder): JsonResponse
    {
        try {
            $dto = $request->toDto($travelOrder);
            $travelOrder = $this->travelOrderService->updateStatus($dto);

            return $this->success(
                new TravelOrderResource($travelOrder),
                200,
                'Status do pedido de viagem atualizado com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao atualizar status do pedido de viagem.');
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
