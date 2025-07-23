<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Listar tarefas (Admins veem tudo, usuários veem apenas as suas)
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            return Task::with('user')->get(); // Admins veem tudo e quem é o dono
        }

        return $user->tasks; // Usuários normais veem apenas as suas
    }

    // Criar uma nova tarefa (Apenas Admins)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'user_id' => 'required|exists:users,id' // Valida que o usuário para quem a tarefa será criada existe
        ]);

        $task = Task::create($validatedData);

        return response()->json($task, 201);
    }

    // Mostrar uma tarefa específica
    public function show(Task $task)
    {
        // Garante que o usuário só pode ver sua própria tarefa, a menos que seja admin
        if ($task->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $task;
    }

    // Atualizar uma tarefa
    public function update(Request $request, Task $task)
    {
        // Garante que o usuário só pode editar sua própria tarefa, a menos que seja admin
        if ($task->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'completed' => 'sometimes|boolean'
        ]);

        $task->update($validatedData);

        return response()->json($task);
    }

    // Deletar uma tarefa
    public function destroy(Request $request, Task $task)
    {
        // Garante que o usuário só pode deletar sua própria tarefa, a menos que seja admin
        if ($task->user_id !== Auth::id() && !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}