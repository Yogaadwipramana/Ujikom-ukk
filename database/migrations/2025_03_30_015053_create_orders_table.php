<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id'); 


            $table->longText('products_id')->charset('utf8mb4')->collation('utf8mb4_bin');


            $table->unsignedBigInteger('members_id')->nullable();
            $table->foreign('members_id')->references('id')->on('members')->onDelete('set null');


            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');

            $table->dateTime('tanggal_penjualan');


            $table->longText('total_barang')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->longText('total_harga')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');

            $table->integer('customer_pay');
            $table->integer('customer_return');

            $table->integer('member_point_used')->default(0);

            $table->integer('total_harga_after_point', )->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
