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
        Schema::table('users', function (Blueprint $table) {
            $table->string('token')->nullable();
            // $table->renameColumn('token', 'api_token');
            //mengganti nama kolom,argument 1 yg lama & argument 2 yg baru

            // $table->text('token')->change();
            //mengganti tipe data dari string ke text lalu diikuti method change
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('token');
            // $table->renameColumn('api_token', 'token');
        });
    }
};
