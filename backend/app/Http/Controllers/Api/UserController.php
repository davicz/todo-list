<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retorna todos os utilizadores com os seus cargos
        return User::with('roles')->paginate(10);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validatedData);

        // Opcional: Sincronizar cargos se enviado na requisição
        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        }
        
        $user->load('roles'); // Recarrega os cargos para a resposta

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Medida de segurança para impedir que um admin se apague a si mesmo
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Você не pode apagar a sua própria conta.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Utilizador apagado com sucesso.']);
    }
}
