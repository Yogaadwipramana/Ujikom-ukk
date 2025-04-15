<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // bigint(20) unsigned AUTO_INCREMENT
            $table->string('name', 255); // varchar(255)
            $table->string('email', 255); // tanpa unique
            $table->enum('role', ['admin', 'petugas'])->default('petugas'); // enum
            $table->string('password', 255); // varchar(255)
            $table->timestamp('created_at')->nullable(); // NULL allowed
            $table->timestamp('updated_at')->nullable(); // NULL allowed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
};
