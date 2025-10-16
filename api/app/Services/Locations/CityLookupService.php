<?php

namespace App\Services\Locations;

use App\Dtos\Locations\CityLookupDto;
use App\Exceptions\External\ExternalServiceException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class CityLookupService
{
    private const CACHE_PREFIX = 'ibge_cities_';
    private const CACHE_TTL = 3600; // seconds

    public function search(CityLookupDto $dto): array
    {
        $cacheKey = $this->cacheKey($dto);
        $baseUrl = config('services_locations.ibge.base_url');
        $endpoint = rtrim($baseUrl, '/') . '/localidades/municipios';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($dto, $endpoint) {
            try {
                $response = Http::timeout(5)
                    ->acceptJson()
                    ->get($endpoint, [
                        'nome' => $dto->query,
                    ]);

                if ($response->failed()) {
                    throw new ExternalServiceException('IBGE', 'Falha ao consultar dados de cidades.', $response->status());
                }

                $cities = $this->filterCities($response, $dto);

                return $cities->all();
            } catch (Throwable $throwable) {
                if ($throwable instanceof ExternalServiceException) {
                    throw $throwable;
                }

                throw new ExternalServiceException('IBGE', 'Erro ao consultar localidades.', previous: $throwable);
            }
        });
    }

    private function cacheKey(CityLookupDto $dto): string
    {
        return self::CACHE_PREFIX . md5(json_encode($dto->toArray()));
    }

    /**
     * @param \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response $response
     * @param CityLookupDto $dto
     * @return mixed
     */
    function filterCities(\GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response $response, CityLookupDto $dto)
    {
        $cities = collect($response->json())
            ->filter(function ($item) use ($dto) {
                if (!$dto->state) {
                    return true;
                }

                $uf = $item['microrregiao']['mesorregiao']['UF']['sigla'] ?? null;
                return $uf && Str::upper($uf) === Str::upper($dto->state);
            })
            ->map(function ($item) {
                $uf = $item['microrregiao']['mesorregiao']['UF'] ?? [];
                $region = $uf['regiao'] ?? [];

                return [
                    'id' => $item['id'],
                    'name' => $item['nome'],
                    'state' => $uf['sigla'] ?? null,
                    'state_name' => $uf['nome'] ?? null,
                    'region' => $region['nome'] ?? null,
                ];
            })
            ->values();

        if ($dto->limit) {
            $cities = $cities->take($dto->limit);
        }
        return $cities;
    }
}
