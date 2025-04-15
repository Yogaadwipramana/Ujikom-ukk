<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up() {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id'); // Sesuai bigInt(20) AUTO_INCREMENT

            $table->string('name', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)
            $table->string('no_telepon', 255)->collation('utf8mb4_unicode_ci')->unique(); // varchar(255) + UNIQUE

            $table->integer('point')->default(10000); // int(11) default 10000

            $table->timestamp('created_at')->nullable(); // sesuai NULL default
            $table->timestamp('updated_at')->nullable(); // sesuai NULL default
        });
    }

    /**
     * Balikkan migrasi.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('members');
    }
};
