<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokenablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokenables', function (Blueprint $table) {
            $table->unsignedBigInteger('token_id');
            $table->foreign('token_id')
                ->references('id')
                ->on('tokens');
            $table->morphs('tokenable');
            $table->index(['token_id', 'tokenable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tokenables');
    }
}
