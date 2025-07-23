<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Adiciona a coluna user_id e a define como chave estrangeira
            $table->foreignId('user_id')      // Cria a coluna do tipo BIGINT UNSIGNED
                  ->after('id')               // (Opcional) Coloca a coluna logo depois da coluna 'id'
                  ->constrained()             // Adiciona a restrição de chave estrangeira para a tabela 'users'
                  ->onDelete('cascade');      // Se um usuário for deletado, todas as suas tarefas também serão
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Remove primeiro a restrição e depois a coluna
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};