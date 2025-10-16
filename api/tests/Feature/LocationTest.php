<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_cities(): void
    {
        Http::fake([
            'servicodados.ibge.gov.br/*' => Http::response([
                [
                    'id' => 3550308,
                    'nome' => 'São Paulo',
                    'microrregiao' => [
                        'mesorregiao' => [
                            'UF' => [
                                'sigla' => 'SP',
                                'nome' => 'São Paulo',
                                'regiao' => [
                                    'nome' => 'Sudeste',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/locations/cities?query=sao');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'São Paulo')
            ->assertJsonPath('data.0.state', 'SP')
            ->assertJsonPath('message', 'Cidades recuperadas com sucesso.');
    }

    public function test_handles_external_service_failure(): void
    {
        Http::fake([
            'servicodados.ibge.gov.br/*' => Http::response([], 500),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/locations/cities?query=rio');

        $response->assertStatus(502)
            ->assertJsonPath('error', 'Falha ao consultar dados de cidades.');
    }
}

