<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVenteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_vente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('vente_id');
            $table->integer('qte');
            $table->double('prix');
            $table->double('mantant');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_vente');
    }
}
