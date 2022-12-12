<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAchatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('achats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('source_id');
            $table->foreign('source_id')->references('id')->on('sources');
            
            $table->unsignedBigInteger('caisse_id');
            $table->foreign('caisse_id')->references('id')->on('caisses');
            
            $table->unsignedBigInteger('payemode_id');
            $table->foreign('payemode_id')->references('id')->on('payemodes');
            
            $table->double('mantant');
            $table->string('observation')->nullable();
            $table->boolean('isvalid')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('achats');
    }
}
