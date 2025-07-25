<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Listar tarefas (Admins veem tudo, utilizadores veem apenas as suas).
     * Agora com funcionalidade de busca.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Inicia a query base: Admins podem ver tudo, utilizadores normais apenas as suas
        $query = $user->hasRole('admin') ? Task::query()->with('user') : $user->tasks()->getQuery();

        // Verifica se um termo de busca foi enviado na requisição (ex: /api/tasks?search=termo)
        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            
            // Adiciona a condição de busca à query.
            // Procura o termo tanto no 'title' QUANTO na 'description'.
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('title', 'like', "%{$searchTerm}%")
                         ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Executa a query e retorna os resultados
        return $query->get();
    }

    /**
     * Criar uma nova tarefa (Apenas Admins)
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after:today',
            'user_id' => 'required|exists:users,id'
        ]);

        $task = Task::create($validatedData);

        return response()->json($task, 201);
    }

    /**
     * Mostrar uma tarefa específica
     */
    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $task;
    }

    /**
     * Atualizar uma tarefa
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after:today',
            'completed' => 'sometimes|boolean'
        ]);

        $task->update($validatedData);

        return response()->json($task);
    }

    /**
     * Apagar uma tarefa
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
