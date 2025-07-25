# API To-Do List com Laravel

Esta é a API backend para a aplicação To-Do List, desenvolvida com o framework Laravel. A API fornece endpoints para autenticação de utilizadores, gestão de cargos/permissões e operações CRUD (Criar, Ler, Atualizar, Apagar) для tarefas.

## 🔗 Frontend Correspondente

Esta API foi desenhada para ser consumida pela nossa aplicação frontend desenvolvida em Vue.js. Para ver a aplicação completa em ação e obter as instruções de instalação do cliente, por favor, visite o repositório do frontend:

* **Repositório Frontend (Vue.js):** [https://github.com/davicz/todo-frontend](https://github.com/davicz/todo-list-vue-frontend)

## ✨ Funcionalidades

* **Autenticação Segura:** Sistema de registo e login baseado em tokens usando Laravel Sanctum.
* **Gestão de Cargos e Permissões:** Utiliza o pacote `spatie/laravel-permission` para diferenciar utilizadores `admin` e `user`.
    * **Admins:** Podem gerir todas as tarefas do sistema e criar tarefas para qualquer utilizador.
    * **Users:** Podem visualizar, atualizar e apagar apenas as suas próprias tarefas.
* **CRUD Completo de Tarefas:** Endpoints para criar, listar, visualizar, atualizar e apagar tarefas.
* **Ambiente Containerizado:** A aplicação é totalmente containerizada com Docker e Docker Compose para facilitar a configuração e a execução.
* **Testes Automatizados:** Testes de funcionalidade para garantir a integridade das regras de negócio e autorização.

## 🚀 Tecnologias Utilizadas

* **Backend:** Laravel 11
* **Base de Dados:** PostgreSQL
* **Autenticação:** Laravel Sanctum
* **Autorização:** Spatie Laravel Permission
* **Ambiente de Desenvolvimento:** Docker & Docker Compose

## 📋 Pré-requisitos

Antes de começar, garanta que você tem as seguintes ferramentas instaladas na sua máquina:

* [Git](https://git-scm.com/)
* [Docker](https://www.docker.com/products/docker-desktop/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## ⚙️ Instalação e Configuração

Siga os passos abaixo para clonar e configurar o projeto na sua máquina local.

**1. Clone o Repositório**
```bash
git clone [https://github.com/davicz/todo-list.git](https://github.com/davicz/todo-list.git)
cd todo-list
```

**2. Configure o Ficheiro de Ambiente**
Copie o ficheiro de ambiente de exemplo. Este ficheiro contém todas as variáveis de configuração da aplicação.
```bash
cp .env.example .env
```
*Nenhuma alteração é necessária no ficheiro `.env` para rodar com Docker, pois as credenciais da base de dados já estão configuradas para se conectar ao contêiner.*

**3. Construa e Inicie os Contêineres**
Este comando irá construir as imagens Docker e iniciar todos os serviços (aplicação, base de dados, etc.) em segundo plano.
```bash
docker-compose up -d --build
```

**4. Instale as Dependências do PHP**
Execute o Composer dentro do contêiner para instalar as dependências do Laravel.
```bash
docker-compose exec app composer install
```

**5. Gere a Chave da Aplicação**
O Laravel precisa de uma chave de encriptação única para funcionar.
```bash
docker-compose exec app php artisan key:generate
```

**6. Execute as Migrations e os Seeders**
Este comando final irá criar todas as tabelas na base de dados e populá-las com os dados iniciais (cargos, permissões e utilizadores padrão).
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

🎉 **Pronto!** A sua API está a rodar e acessível em `http://localhost:8000`.

## 🧪 A Rodar os Testes

Para garantir que todas as funcionalidades críticas estão a operar corretamente, pode rodar a suíte de testes automatizados com o seguinte comando:
```bash
docker-compose exec app php artisan test
```

## 👤 Utilizadores Padrão

Os seeders criam dois utilizadores para facilitar os testes. As senhas para ambos são `password123`.

| Cargo   | Email               | Senha         |
| :------ | :------------------ | :------------ |
| **Admin** | `admin@exemplo.com` | `password123` |
| **User** | `user@exemplo.com`  | `password123` |

## 📖 Endpoints da API

A seguir está a documentação dos principais endpoints disponíveis.

**Header Obrigatório para Rotas Protegidas:**
`Authorization`: `Bearer <seu_token_de_acesso>`

### Autenticação

| Método | Endpoint      | Descrição                                  | Corpo (Body) da Requisição (JSON)                                                  |
| :----- | :------------ | :----------------------------------------- | :--------------------------------------------------------------------------------- |
| `POST` | `/api/register` | Regista um novo utilizador (com cargo 'user'). | `{ "name": "...", "email": "...", "password": "...", "password_confirmation": "..." }` |
| `POST` | `/api/login`    | Autentica um utilizador e retorna um token.     | `{ "email": "...", "password": "..." }`                                              |
| `POST` | `/api/logout`   | Invalida o token do utilizador autenticado.   | *(Vazio)* |

### Tarefas (Tasks)

| Método   | Endpoint        | Descrição                                                                         | Corpo (Body) da Requisição (JSON)                                                                  | Permissão Necessária    |
| :------- | :-------------- | :-------------------------------------------------------------------------------- | :------------------------------------------------------------------------------------------------- | :---------------------- |
| `GET`    | `/api/tasks`      | Lista tarefas. Admins veem todas; utilizadores veem apenas as suas.                   | *(Vazio)* | Utilizador Autenticado     |
| `POST`   | `/api/tasks`      | Cria uma nova tarefa para um utilizador específico.                                  | `{ "title": "...", "description": "...", "due_date": "YYYY-MM-DD", "user_id": ... }`               | **Admin** |
| `GET`    | `/api/tasks/{id}` | Mostra os detalhes de uma tarefa específica.                                      | *(Vazio)* | Dono da Tarefa ou Admin |
| `PUT`    | `/api/tasks/{id}` | Atualiza uma tarefa existente.                                                    | `{ "title": "...", "description": "...", "due_date": "...", "completed": true/false }` (campos opcionais) | Dono da Tarefa ou Admin |
| `DELETE` | `/api/tasks/{id}` | Exclui uma tarefa.                                                                | *(Vazio)* | Dono da Tarefa ou Admin |
