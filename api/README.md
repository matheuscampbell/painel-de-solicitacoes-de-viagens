# Travel Orders API

API REST em Laravel para gerenciamento de pedidos de viagem com autenticação JWT, histórico de status, notificações e painel administrativo para usuários.

## Tecnologias

-   PHP 8.3 / Laravel 10
-   PostgreSQL 16
-   Redis 7 (cache/queue futuro)
-   Nginx + PHP-FPM (Docker)
-   JWT (tymon/jwt-auth)

## Requisitos

-   Docker e Docker Compose
-   `make` opcional (para atalhos)

## Setup Rápido

1. Levante a stack:
    ```bash
    docker compose up -d --build
    ```
2. Execute as migrations (UUIDs incluídos):
    ```bash
    docker compose run --rm php php artisan migrate --force
    ```
3. Popule dados de referência (opcional):
    ```bash
    docker compose run --rm php php artisan db:seed --class=UserSeeder
    ```
    Usuários criados pelo seed:
    - Admin: `admin@example.com` / `secret123`
    - Cliente: `user@example.com` / `secret123`

## Testes

-   Todas as suítes (feature + unidade remanescentes):
    ```bash
    docker compose run --rm php php artisan test
    ```
-   Somente testes de feature:
    ```bash
    docker compose run --rm php php artisan test --testsuite=Feature
    ```

Os testes cobrem cenários felizes e negativos para criação/atualização de pedidos, notificações (leitura e contagem), autenticação/admin e validações principais.

## Documentação da API

A especificação OpenAPI está disponível em [`openapi.yaml`](./openapi.yaml). Você pode importá-la no Swagger UI, Insomnia ou Postman.

Principais grupos:

-   **Autenticação**: login, registro, refresh, logout.
-   **Pedidos de viagem**: CRUD parcial com filtros, histórico e notificações.
-   **Notificações**: listagem, marcação como lida e contagem de não lidas.
-   **Localidades**: consulta à API pública do IBGE.
-   **Admin/Usuários**: criação, atualização, ativação/desativação e filtros.

## Fluxos Principais

1. **Autenticação**
    - `POST /api/login` → recebe token JWT (`Bearer`).
    - `GET /api/getMe` → retorna dados do usuário autenticado.
    - `GET /api/logout` / `GET /api/refresh-token` → gerenciamento do token.
2. **Pedidos de Viagem**
    - `POST /api/travel-orders` → cria pedido (origem, destino, datas, notas).
    - `PATCH /api/travel-orders/{uuid}/status` → admin aprova/cancela; grava histórico com anotação.
    - `GET /api/travel-orders` → filtros por status, destino, origem e períodos.
3. **Notificações**
    - `GET /api/notifications` → inclui `is_read` e dados referência (UUIDs).
    - `PATCH /api/notifications/{id}/read` → marca como lida.
    - `GET /api/notifications/unread-count` → quantifica não lidas.
4. **Usuários (Admin)**
    - `POST /api/admin/users` → cria usuário com senha.
    - `PATCH /api/admin/users/{uuid}` → altera dados e senha.
    - `PATCH /api/admin/users/{uuid}/status` → ativa/desativa.
    - Listagem com filtros (`search`, `tipo_usuario`, `status`).
5. **Localidades**
    - `GET /api/locations/cities?query=...` → busca cidades via IBGE (cache com TTL 1h).

## Convenções Importantes

-   Todos os recursos expostos utilizam UUID (bigint interno continua para FK).
-   Respostas seguem padrão `{ status, message, data }` em casos de sucesso (exceto validações 422 retornadas pelo `FormRequest`).
-   Exceções de domínio retornam JSON coerente (`error`, HTTP adequado) via `ApiResponse`.
-   Logs relevantes: criação e atualização de pedidos, mudança de status e operações administrativas com usuários.


