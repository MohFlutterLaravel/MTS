<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAchatProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('achat_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('achat_id');
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
        Schema::dropIfExists('achat_product');
    }
}
