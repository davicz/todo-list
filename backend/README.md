# API To-Do List com Laravel

Esta é a API backend para a aplicação To-Do List, desenvolvida com o framework Laravel. A API fornece endpoints para autenticação de usuários, gerenciamento de cargos/permissões e operações CRUD (Criar, Ler, Atualizar, Deletar) para tarefas.

## ✨ Funcionalidades

* **Autenticação Segura:** Sistema de registro e login baseado em tokens usando Laravel Sanctum.
* **Gerenciamento de Cargos e Permissões:** Utiliza o pacote `spatie/laravel-permission` para diferenciar usuários `admin` e `user`.
    * **Admins:** Podem gerenciar todas as tarefas do sistema e criar tarefas para qualquer usuário.
    * **Users:** Podem visualizar, atualizar e deletar apenas suas próprias tarefas.
* **CRUD Completo de Tarefas:** Endpoints para criar, listar, visualizar, atualizar e deletar tarefas.
* **Ambiente Containerizado:** A aplicação é totalmente containerizada com Docker e Docker Compose para facilitar a configuração e a execução.
* **Testes Automatizados:** Testes de funcionalidade para garantir a integridade das regras de negócio e autorização.

## 🚀 Tecnologias Utilizadas

* **Backend:** Laravel 11
* **Banco de Dados:** PostgreSQL
* **Autenticação:** Laravel Sanctum
* **Autorização:** Spatie Laravel Permission
* **Ambiente de Desenvolvimento:** Docker & Docker Compose

## 📋 Pré-requisitos

Antes de começar, garanta que você tem as seguintes ferramentas instaladas na sua máquina:

* [Git](https://git-scm.com/)
* [Docker](https://www.docker.com/products/docker-desktop/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## ⚙️ Instalação e Configuração

Siga os passos abaixo para clonar e configurar o projeto em sua máquina local.

**1. Clone o Repositório**
```bash
git clone [https://github.com/davicz/todo-list.git](https://github.com/davicz/todo-list.git)
cd todo-list
```

**2. Configure o Arquivo de Ambiente**
Copie o arquivo de ambiente de exemplo. Este arquivo contém todas as variáveis de configuração da aplicação.
```bash
cp .env.example .env
```
*Nenhuma alteração é necessária no arquivo `.env` para rodar com Docker, pois as credenciais do banco de dados já estão configuradas para se conectar ao contêiner.*

**3. Construa e Inicie os Contêineres**
Este comando irá construir as imagens Docker e iniciar todos os serviços (aplicação, banco de dados, etc.) em segundo plano.
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
Este comando final irá criar todas as tabelas no banco de dados e populá-las com os dados iniciais (cargos, permissões e usuários padrão).
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

🎉 **Pronto!** Sua API está rodando e acessível em `http://localhost:8000`.

## 🧪 Rodando os Testes

Para garantir que todas as funcionalidades críticas estão operando corretamente, você pode rodar a suíte de testes automatizados com o seguinte comando:
```bash
docker-compose exec app php artisan test
```

## 👤 Usuários Padrão

Os seeders criam dois usuários para facilitar os testes. As senhas para ambos são `password123`.

| Cargo     | Email               | Senha         |
| :-------- | :------------------ | :------------ |
| **Admin** | `admin@exemplo.com` | `password123` |
| **User**  | `user@exemplo.com`  | `password123` |

## 📖 Endpoints da API

A seguir está a documentação dos principais endpoints disponíveis.

**Header Obrigatório para Rotas Protegidas:**
`Authorization`: `Bearer <seu_token_de_acesso>`

### Autenticação

| Método | Endpoint      | Descrição                                  | Corpo (Body) da Requisição (JSON)                                                  |
| :----- | :------------ | :----------------------------------------- | :--------------------------------------------------------------------------------- |
| `POST` | `/api/register` | Registra um novo usuário (com cargo 'user'). | `{ "name": "...", "email": "...", "password": "...", "password_confirmation": "..." }` |
| `POST` | `/api/login`    | Autentica um usuário e retorna um token.     | `{ "email": "...", "password": "..." }`                                              |
| `POST` | `/api/logout`   | Invalida o token do usuário autenticado.   | *(Vazio)* |

### Tarefas (Tasks)

| Método   | Endpoint        | Descrição                                                                         | Corpo (Body) da Requisição (JSON)                                                                  | Permissão Necessária    |
| :------- | :-------------- | :-------------------------------------------------------------------------------- | :------------------------------------------------------------------------------------------------- | :---------------------- |
| `GET`    | `/api/tasks`      | Lista tarefas. Admins veem todas; usuários veem apenas as suas.                   | *(Vazio)* | Usuário Autenticado     |
| `POST`   | `/api/tasks`      | Cria uma nova tarefa para um usuário específico.                                  | `{ "title": "...", "description": "...", "due_date": "YYYY-MM-DD", "user_id": ... }`               | **Admin** |
| `GET`    | `/api/tasks/{id}` | Mostra os detalhes de uma tarefa específica.                                      | *(Vazio)* | Dono da Tarefa ou Admin |
| `PUT`    | `/api/tasks/{id}` | Atualiza uma tarefa existente.                                                    | `{ "title": "...", "description": "...", "due_date": "...", "completed": true/false }` (campos opcionais) | Dono da Tarefa ou Admin |
| `DELETE` | `/api/tasks/{id}` | Exclui uma tarefa.                                                                | *(Vazio)* | Dono da Tarefa ou Admin |
