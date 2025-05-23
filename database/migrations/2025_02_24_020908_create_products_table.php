<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->string('image', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->decimal('price', 15, 2);
            $table->integer('stock');

            $table->timestamp('created_at')->nullable(); 
            $table->timestamp('updated_at')->nullable(); 
        });
    }

    public function down() {
        Schema::dropIfExists('products');
    }
};
