<?php

namespace Tests\Feature\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TaskAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'create tasks']);
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        $adminRole->givePermissionTo('create tasks');

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');
    }


    public function regular_user_cannot_create_task(): void
    {
        // Simula uma requisição POST para /api/tasks
        $response = $this->actingAs($this->regularUser, 'sanctum')
                         ->postJson('/api/tasks', [
                            'title' => 'Tarefa do Usuário Comum',
                            'user_id' => $this->regularUser->id
                         ]);

        $response->assertStatus(403);
    }

    public function admin_user_can_create_task(): void
    {
        // Simula uma requisição POST, autenticado como o admin.
        $response = $this->actingAs($this->adminUser, 'sanctum')
                         ->postJson('/api/tasks', [
                            'title' => 'Tarefa criada pelo Admin',
                            'description' => 'Descrição da tarefa',
                            'due_date' => '2025-12-31',
                            'user_id' => $this->regularUser->id
                         ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarefa criada pelo Admin'
        ]);
    }

    public function regular_user_cannot_update_another_users_task(): void
    {
        $taskOfAdmin = Task::factory()->create(['user_id' => $this->adminUser->id]);

        // O usuário comum tenta atualizar a tarefa do admin
        $response = $this->actingAs($this->regularUser, 'sanctum')
                         ->putJson('/api/tasks/' . $taskOfAdmin->id, [
                            'title' => 'Título Hackeado'
                         ]);

        $response->assertStatus(403);
    }

    public function regular_user_can_update_their_own_task(): void
    {
        $taskOfUser = Task::factory()->create(['user_id' => $this->regularUser->id]);

        // O usuário comum atualiza sua própria tarefa
        $response = $this->actingAs($this->regularUser, 'sanctum')
                         ->putJson('/api/tasks/' . $taskOfUser->id, [
                            'title' => 'Título Atualizado',
                            'completed' => true
                         ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $taskOfUser->id,
            'title' => 'Título Atualizado'
        ]);
    }
}