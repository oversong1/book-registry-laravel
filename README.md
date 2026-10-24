# Book Registry

Cadastro de livros em **Laravel 12**. Cada usuário faz login com senha e gerencia **só os próprios livros**
(criar, listar, ver, editar e apagar).

O projeto tem duas portas de entrada para as **mesmas regras**:

| | API | Telas |
|---|---|---|
| Endereço | `/api/v1/...` | `/books`, `/login`... |
| Resposta | JSON | HTML (Blade + Tailwind) |
| Login | Token (Laravel Sanctum) | Sessão (cookie) |

As duas usam o mesmo Model, a mesma validação (Form Requests) e a mesma regra de "só o dono" (Policy).

## Tecnologias

Laravel 12 · PHP 8.3 · MySQL 8.4 · Laravel Sanctum · Tailwind CSS 4 + Vite · Docker

## Requisitos

- Docker Desktop
- Portas livres: `8098` (aplicação), `8090` (Adminer) e `3310` (MySQL)

## Como rodar

```bash
git clone https://github.com/oversong1/book-registry-laravel.git
cd book-registry-laravel
docker compose up -d --build
```

**Na primeira vez, a aplicação leva alguns minutos para responder.** O container instala as dependências do
PHP e do Node, compila o Tailwind, cria o `.env`, cria as tabelas e os dados de demonstração. Acompanhe pelo log
até aparecer `[entrypoint] pronto`:

```bash
docker compose logs php -f
```

| Serviço | Endereço |
|---|---|
| Telas | http://localhost:8098 |
| API | http://localhost:8098/api/v1 |
| Adminer (banco) | http://localhost:8090 · Sistema: MySQL · Servidor: `db` · Usuário/senha/banco: `books` |

Para desligar: `docker compose down` (os dados continuam). Para apagar tudo, inclusive o banco:
`docker compose down -v`.

## Usuários de teste

| E-mail | Senha | Livros |
|---|---|---|
| test@example.com | password | 5 |
| other@example.com | password | 3 |

Com os dois dá para conferir que um usuário não acessa os livros do outro.

## Telas

| Página | Endereço |
|---|---|
| Login | http://localhost:8098/login |
| Criar conta | http://localhost:8098/register |
| Meus livros (lista, 10 por página) | http://localhost:8098/books |
| Novo livro | http://localhost:8098/books/create |
| Ver livro | http://localhost:8098/books/{id} |
| Editar livro | http://localhost:8098/books/{id}/edit |

Apagar fica na lista e na página do livro, com confirmação.

## API

Todas as rotas começam com `/api/v1`. Mande sempre o header `Accept: application/json`, e nas rotas protegidas
o header `Authorization: Bearer <token>` (o token vem na resposta do login).

| Método | Rota | Token? | O que faz |
|---|---|---|---|
| POST | `/register` | Não | Cria a conta e devolve um token |
| POST | `/login` | Não | Devolve um token (máximo de 5 tentativas por minuto) |
| GET | `/me` | Sim | Usuário dono do token |
| POST | `/logout` | Sim | Apaga o token usado na requisição |
| GET | `/books` | Sim | Lista os seus livros, paginada (`?per_page=1..100`, padrão 15) |
| POST | `/books` | Sim | Cria um livro |
| GET | `/books/{id}` | Sim | Mostra um livro (só o dono) |
| PUT/PATCH | `/books/{id}` | Sim | Atualiza (só o dono; o PATCH aceita só os campos que mudaram) |
| DELETE | `/books/{id}` | Sim | Apaga (só o dono) |

**Exemplo:**

```bash
curl -X POST http://localhost:8098/api/v1/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
# 200 {"token":"1|aBcD...","user":{"id":1,"name":"Test User","email":"test@example.com"}}

curl -X POST http://localhost:8098/api/v1/books \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|aBcD..." \
  -d '{"name":"Clean Code","isbn":"9780132350884","value":"49.90"}'
# 201 {"data":{"id":9,"name":"Clean Code","isbn":"9780132350884","value":"49.90","created_at":"...","updated_at":"..."}}
```

**Status de resposta:**

| Status | Quando |
|---|---|
| 200 / 201 | Deu certo / criado |
| 401 | Sem token ou token inválido |
| 403 | O livro é de outro usuário |
| 404 | Livro (ou rota) não existe |
| 422 | Dados inválidos: `{"message": "...", "errors": {"campo": ["..."]}}` |
| 429 | Muitas tentativas de login |

### Regras dos campos

| Campo | Regra |
|---|---|
| `name` | Obrigatório, texto, até 255 caracteres |
| `isbn` | Opcional, **só dígitos**, 10 ou 13; não repete para o mesmo usuário |
| `value` | Opcional, decimal ≥ 0 com até 2 casas; volta como texto (`"49.90"`) |

### Testar a API manualmente

- **VS Code + REST Client:** abra o arquivo [`api.http`](./api.http) e clique em **Send Request**, começando pelo
  Login. As outras requisições usam o token da resposta dele.
- **Postman:** use as rotas da tabela acima, com o header `Accept: application/json`, o corpo em
  **Body → raw → JSON** e o token em **Authorization → Bearer Token**.

## Testes automatizados

```bash
docker compose exec php php artisan test
```

São 12 testes (`src/tests/Feature`):
- **API:** login, senha errada, logout apagando o token, 401 sem token, listagem só com os próprios livros,
  `user_id` do corpo da requisição ignorado, regras de validação e 403 ao ver, editar ou apagar livro de outro
  usuário.
- **Telas:** redirecionamento para o login, login pelo formulário, cadastro pelo formulário e 403 nas páginas de
  livro de outro usuário.

Os testes usam um banco **SQLite em memória** (`src/phpunit.xml`): rápidos, isolados e sem tocar nos dados do MySQL.

## Premissas

O enunciado deixa alguns pontos em aberto. Estas foram as decisões:

- **Registro:** o enunciado só fala em login, mas sem usuário não há login. Existe registro na API e na tela,
  além dos usuários de teste.
- **Token na API, sessão nas telas:** cada um é o padrão do seu tipo de cliente.
- **Logout** apaga só o token usado; os outros aparelhos continuam logados.
- **ISBN "apenas dígitos":** 10 ou 13 dígitos (os dois tamanhos de ISBN), guardado como **texto** para não perder
  zero à esquerda. O dígito verificador não é validado. O mesmo usuário não repete um ISBN, mas usuários diferentes
  podem ter o mesmo livro.
- **Só o nome é obrigatório**, como no enunciado.
- **Valor** é dinheiro: `DECIMAL(10,2)`, nunca `float`.
- **Livro de outro usuário** responde **403**. Responder 404 esconderia até a existência do registro; para um
  cadastro de livros, 403 deixa a regra mais explícita.
- **DELETE** devolve JSON (`{"message": "Book deleted."}`), porque o enunciado pede JSON em todas as respostas.
- **Mensagens de validação** no inglês padrão do Laravel.

## Decisões principais e estrutura

| Ponto | Onde | Por quê |
|---|---|---|
| Validação | `app/Http/Requests` | Regras num lugar só, usadas pela API e pelas telas |
| "Só o dono" | `app/Policies/BookPolicy.php` | Uma regra, testada, usada pelas duas portas |
| Formato do JSON | `app/Http/Resources` | O JSON não depende das colunas do banco |
| Controllers | `app/Http/Controllers/Api` e `.../Web` | Curtos; só mudam a resposta (JSON ou View) |
| Dono do livro | `$request->user()->books()->create(...)` | `user_id` fora do `$fillable`: ninguém cria livro no nome de outro |
| Versão da API | prefixo `/api/v1` | Mudanças que quebram compatibilidade vão para uma `/api/v2` |
| Erros em JSON | `bootstrap/app.php` | Toda rota `/api/*` responde erro em JSON, até sem o header `Accept` |
| Banco | migration de `books` | Chave estrangeira com exclusão em cascata e índice único `(user_id, isbn)`, que também acelera a busca por dono |
| Segurança | rotas | Senha e token com hash, limite de 5 logins por minuto, mesma mensagem para e-mail ou senha errados |
| Volume | `BookController@index` | Lista sempre paginada, com limite de 100 por página na API |
| Instalação | `docker/php/entrypoint.sh` | Um comando monta tudo na primeira vez |

```
docker-compose.yml, docker/      Ambiente (PHP + Apache, MySQL, Adminer) e script de inicialização
api.http                         Requisições prontas para o REST Client
src/
  app/Http/Controllers/Api/      AuthController, BookController (JSON)
  app/Http/Controllers/Web/      AuthController, BookController (telas)
  app/Http/Requests/             StoreBookRequest, UpdateBookRequest
  app/Http/Resources/            BookResource, UserResource
  app/Models/                    User, Book
  app/Policies/                  BookPolicy
  database/                      migrations, factories, seeders
  resources/views/               layout, auth, books
  routes/                        api.php, web.php
  tests/Feature/                 ApiAuthTest, ApiBookTest, WebBookTest
```

## Fluxo de desenvolvimento

Cada etapa virou um commit com uma tag (`fase-01` a `fase-12`):

1. Ambiente Docker (PHP 8.3, MySQL 8.4, Adminer)
2. Laravel 12 conectado no MySQL e Tailwind compilado
3. Sanctum e respostas da API sempre em JSON
4. Login e registro na API (token) e nas telas (sessão)
5. Tabela `books`, Model e Factory
6. Validação com Form Requests
7. Formato do JSON com API Resource
8. Policy: só o dono acessa
9. CRUD de livros na API e nas telas
10. Testes automatizados
11. Seeder com dados de demonstração
12. Docker com 1 comando

## Como o projeto cresceria

A implementação é pequena de propósito, mas a estrutura é a que se mantém numa aplicação maior: cada
responsabilidade já tem a sua classe, e funcionalidades novas seguem o mesmo padrão.

- **Mais regras de negócio:** hoje a criação do livro é uma linha, repetida nos dois Controllers. Quando ganhar
  regras (e-mail, histórico, auditoria), ela vai para uma classe de ação (`CreateBook`), usada pela API, pelas
  telas, por filas e por comandos.
- **Mais usuários:** o login da API por token não guarda estado no servidor, então a aplicação pode rodar em
  várias máquinas atrás de um balanceador. As sessões das telas iriam para o Redis ou para o banco.
- **Mais dados:** paginação por cursor (a paginação por página fica lenta em tabelas grandes), índices para
  novos filtros, busca por nome com um motor de busca (Laravel Scout), cache e réplicas de leitura.
- **Catálogo compartilhado:** hoje cada usuário guarda sua própria cópia do livro. Com muitos usuários, o caminho
  é uma tabela de livros (uma linha por ISBN) e uma tabela de ligação usuário-livro, com o valor de cada um.
- **Papéis:** um administrador que vê tudo cabe no método `before()` da Policy ou num pacote de permissões.
- **Tokens:** expiração (`sanctum.expiration`) e limpeza dos antigos (`sanctum:prune-expired`).
- **Operação:** testes rodando no MySQL num pipeline de CI, documentação OpenAPI da API e monitoramento.

## Limitações conhecidas

- O ISBN repetido é barrado na validação. Duas requisições exatamente ao mesmo tempo poderiam passar por ela, e
  aí o índice único do banco recusaria a segunda com erro 500 em vez de 422.
- Os testes rodam em SQLite e o projeto em MySQL; uma diferença entre os dois bancos só apareceria rodando os
  testes no MySQL.
- Os tokens da API não expiram.
- Na primeira subida, a aplicação demora alguns minutos para responder (instalação dentro do container).

---

## English summary

**Book Registry** is a Laravel 12 application where each user logs in with a password and manages **only their
own books** (create, list, view, update, delete). It offers a **JSON API** (`/api/v1`, token authentication with
Laravel Sanctum) and **web screens** (Blade + Tailwind, session authentication). Both share the same validation
(Form Requests) and ownership rule (Policy).

**Run it:** `git clone` the repository and run `docker compose up -d --build`. The first start takes a few
minutes (dependencies, Tailwind build, `.env`, migrations and seed); follow `docker compose logs php -f` until
`[entrypoint] pronto` appears. App: http://localhost:8098 · API: http://localhost:8098/api/v1 · Adminer:
http://localhost:8090.

**Demo users:** `test@example.com` / `password` (5 books) and `other@example.com` / `password` (3 books).

**Tests:** `docker compose exec php php artisan test` (12 feature tests, in-memory SQLite).

**Book fields:** `name` required; `isbn` optional, digits only (10 or 13), unique per user, stored as a string;
`value` optional decimal with up to 2 places, stored as `DECIMAL(10,2)`.

**Key decisions:** thin controllers split into `Api` and `Web`; shared Form Requests and Policy; API Resources
for the JSON shape; `user_id` is never mass-assignable; versioned API (`/api/v1`); JSON errors for every API
route; composite unique index `(user_id, isbn)`; login rate limit; paginated lists; one-command Docker setup.

**Scaling:** move business logic into Action classes when it grows, stateless token auth for horizontal scaling,
cursor pagination, caching and read replicas, a shared book catalog for many users, roles via the Policy, token
expiration, CI running the tests on MySQL and an OpenAPI spec.
