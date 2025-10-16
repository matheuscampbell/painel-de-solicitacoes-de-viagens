# Travel Orders

Aplicação full-stack para abertura, análise e acompanhamento de pedidos de viagem corporativa. A solução disponibiliza autenticação via JWT, aprovação com histórico de status, notificações e um painel administrativo.

## Visão Geral

- API REST em Laravel centraliza regras de negócio, integrações externas e expõe a especificação OpenAPI.
- Dashboard em Vue 3 + Vuetify consome a API e entrega a experiência para administradores e solicitantes.
- Stack Docker (PHP-FPM, Nginx, PostgreSQL e Redis) garante ambiente local padronizado.
- Seeds opcionais criam usuários de exemplo para explorar rapidamente os fluxos principais.

## Estrutura do Projeto

- `api/` – código-fonte da API Laravel e documentação (`openapi.yaml`).
- `dashboard/` – SPA em Vue 3/Vite com os fluxos de autenticação, pedidos e notificações.
- `nginx/` – configuração do proxy reverso que expõe a API na porta 8080.
- `docker-compose.yml` – orquestra, em contêineres, todos os serviços necessários.

## Requisitos

- Docker e Docker Compose instalados e atualizados

## Setup Rápido

1. Verifique as variáveis de ambiente de desenvolvimento:
   - `api/.env` e `dashboard/.env` já estão prontos para uso local; ajuste credenciais e segredos conforme necessário.
2. Suba a stack:
   ```bash
   docker compose up -d --build
   ```
3. Execute as migrations (UUIDs incluídos):
   ```bash
   docker compose run --rm php php artisan migrate --force
   ```
4. (Opcional) Popule dados de referência:
   ```bash
   docker compose run --rm php php artisan db:seed --class=UserSeeder
   ```
   Usuários criados pelo seed:
   - Admin: `admin@example.com` / `secret123`
   - Cliente: `user@example.com` / `secret123`
5. Acesse o dashboard em `http://localhost:5173/` e autentique-se com uma das credenciais acima.

## URLs Úteis

- Dashboard: `http://localhost:5173/`
- API base: `http://localhost:8080/api`
- Documentação da API: importe `api/openapi.yaml` no Swagger UI, Insomnia ou Postman

## Comandos Frequentes

- Rodar testes automatizados da API:
  ```bash
  docker compose run --rm php php artisan test
  ```
- Aplicar migrations pendentes após alterações:
  ```bash
  docker compose run --rm php php artisan migrate
  ```
- Acompanhar logs da API em tempo real:
  ```bash
  docker compose logs -f php
  ```

## Disclaimer

Neste projeto procurei implementar as funcionalidades mais basicas tanto dos frameworks quanto em questao de arquitetura, visando um entendimento mais facil do codigo. Em um projeto real, eu adotaria boas praticas como Clean Architecture e SOLID ao extremo com algumas abstrações principalmente no Laravel.
