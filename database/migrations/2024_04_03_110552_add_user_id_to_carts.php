<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCarts extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();

            // Menambahkan foreign key constraint
            $table->foreign('user_id')->references('user_id')->on('buyers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Menghapus foreign key constraint
            $table->dropForeign(['user_id']);

            // Menghapus kolom user_id
            $table->dropColumn('user_id');
        });
    }
};
